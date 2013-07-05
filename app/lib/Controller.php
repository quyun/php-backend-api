<?php

Class Controller
{
    /**
     * @var object
     */
    protected $_db;

    /**
     * @var array
     */
    protected $_backends;

    /**
     * @var array
     */
    private $_global_messages = array(
        'zh_CN' => array(
            // -err-
            'can not open database'                 => '无法打开数据库',
            'server does not exist'                 => '服务器不存在',
            'unable to connect to backend server'   => '无法连接到后台进程服务器',
            'not login yet'                         => '尚未登录',
        ),
    );

    /**
     * @var array
     */
    private $_partials = array(
        'zh_CN' => array(
            'backend server return'         => '后台进程服务器返回',
            'is required'                   => '是必须的',
            'is not allow to be empty'      => '不允许为空',
        ),
    );

    /**
     * @var array
     */
    private $_global_fields = array(
        'zh_CN' => array(
            'FAILED'        => '失败',
            'DENIED'        => '禁止',
            'UNKNOWN'       => '未知指令',
        ),
    );

    /**
     * Constructor
     * @param  object $app
     */
    public function __construct($app)
    {
        $this->app = $app;
        $this->req = $app->request();

        $this->_backends = array();

        session_start();
    }

    public function __get($name)
    {
        if ($name == 'db') return $this->db();
        if ($name == 'backend') return $this->backend();
        return $this->$name;
    }

    /**
     * Return db object
     */
    private function db()
    {
        if ($this->_db) return $this->_db;
        try {
            $dbpath = $this->app->config('dbpath');
            $db = new SQLite3($dbpath, SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE);
        } catch (Exception $e) {
            $this->apiErr('can not open database');
        }
        $this->_db = $db;
        return $this->_db;
    }

    /**
     * Initialize database
     */
    protected function initDb()
    {
        $tables = $this->db->querySingle("SELECT name FROM sqlite_master WHERE type='table'");
        if ($tables) return;

        // init database
        $this->db->exec(
            'CREATE TABLE users ('
            .'username STRING PRIMARY KEY, '
            .'password STRING)');
        $passwordMd5 = md5('admin');
        $this->db->exec(
            'INSERT INTO users (username, password) '
            ."VALUES ('admin', '{$passwordMd5}')");
        $this->db->exec(
            'CREATE TABLE servers ('
            .'serverid INTEGER PRIMARY KEY AUTOINCREMENT, '
            .'servername STRING, '
            .'serverip STRING, '
            .'serverport STRING, '
            .'username STRING, '
            .'password STRING)');
    }

    /**
     * Return backend object
     */
    private function backend()
    {
        $serverid = $this->requireNotEmptyParam('serverid');
        if (isset($this->_backends[$serverid])) return $this->_backends[$serverid];

        // get server ip/port
        $stmt = $this->db->prepare('SELECT serverip, serverport, username, password FROM servers WHERE serverid=:serverid');
        $stmt->bindValue(':serverid', $serverid, SQLITE3_INTEGER);
        $result = $stmt->execute();
        $server = $result->fetchArray(SQLITE3_ASSOC);
        if (!$server) {
            $this->apiErr('server does not exist');
        }

        $be = new Backend();
        $be->init($server['serverip'], $server['serverport']);
        if ($server['username'] && $server['password']) {
            $be->init_auth($server['username'], $server['password']);
        }
        $this->_backends[$serverid] = $be;

        return $this->_backends[$serverid];
    }

    /**
     * Process with unnormal backend result
     * @param array $result
     */
    protected function processUnnormalBackendResult($result)
    {
        if (!$result) {
            $this->apiErr('unable to connect to backend server');
        }
        if ($result['code'] != 'OK') {
            $this->apiErr("backend server return \"{$result['code']}\"");
        }
    }

    /**
     * Check if user is login
     * @param  string $paramName
     */
    protected function auth()
    {
        if (!isset($_SESSION['username'])) {
            $this->apiErr('not login yet');
        }
    }

    /**
     * Get a required not empty param's value from request
     * @param  string $paramName
     */
    protected function requireNotEmptyParam($paramName)
    {
        $value = $this->req->get($paramName);
        if (is_null($value)) {
            $this->apiErr("\"{$paramName}\" is required");
        }
        if ($value == '') {
            $this->apiErr("\"{$paramName}\" is not allow to be empty");
        }
        return $value;
    }

    /**
     * Return success result to client
     * @param  string $data
     */
    protected function apiOk($data, $message='')
    {
        $message = $this->translateMessage($message);
        $dataFormat = $this->req->get('f');
        $indent = $this->req->get('i');
        $res = $this->app->response();
        if ($dataFormat == 'jsonp') {
            $callbackName = $this->req->get('cb');
            $res['Content-Type'] = 'application/x-javascript; charset=utf-8';
            $this->jsonpOk($data, $message, $callbackName, $indent);
        } else {
            $res['Content-Type'] = 'application/json; charset=utf-8';
            $this->jsonOk($data, $message, $indent);
        }
        $this->app->stop();
    }

    /**
     * Return error result to client
     */
    public function apiErr($message='')
    {
        $message = $this->translateMessage($message);
        $dataFormat = $this->req->get('f');
        $indent = $this->req->get('i');
        $res = $this->app->response();
        if ($dataFormat == 'jsonp') {
            $callbackName = $this->req->get('cb');
            $res['Content-Type'] = 'application/x-javascript; charset=utf-8';
            $this->jsonpErr($message, $callbackName, $indent);
        } else {
            $res['Content-Type'] = 'application/json; charset=utf-8';
            $this->jsonErr($message, $indent);
        }
        $this->app->stop();
    }

    /**
     * Translate message
     * @param  string $message
     */
    private function translateMessage($message)
    {
        if (!$message) return $message;
        $language = $this->req->get('l');
        $_messages = array_merge_recursive($this->_global_messages, $this->_messages);
        if (!in_array($language, array_keys($_messages))) return $message;

        if (isset($_messages[$language][$message])) {
            return $_messages[$language][$message];
        }

        // detect "field" in message
        if (preg_match('/([^"]*)"([^"]+)"([^"]*)/', $message, $matches)) {
            $pre = $this->translatePartial($matches[1], $language);
            $field = $this->translateField($matches[2], $language);
            $post = $this->translatePartial($matches[3], $language);
            return implode('', array($pre, '"'.$field.'"', $post));
        }
    }

    /**
     * Translate message partial
     * @param  string $partial
     */
    private function translatePartial($partial, $language)
    {
        if (!$partial) return $partial;
        $partial = trim($partial);

        if (isset($this->_partials[$language][$partial])) {
            return $this->_partials[$language][$partial];
        }
        return $partial;
    }

    /**
     * Translate message field
     * @param  string $field
     */
    private function translateField($field, $language)
    {
        if (!$field) return $field;
        $_fields = array_merge_recursive($this->_global_fields, $this->_fields);
        if (isset($_fields[$language][$field])) {
            return $_fields[$language][$field];
        }
        return $field;
    }
    
    /**
     * Return success json result to client
     * @param  string $data
     */
    private function jsonOk($data, $message='', $indent=false)
    {
        $jsonStr = json_encode(array(
            'code' => 0,
            'message' => $message,
            'data' => $data,
        ));

        echo $indent ? $this->jsonIndent($jsonStr) : $jsonStr;
    }

    /**
     * Return error json result to client
     */
    private function jsonErr($message='', $indent=false)
    {
        $jsonStr = json_encode(array(
            'code' => -1,
            'message' => $message,
        ));

        echo $indent ? $this->jsonIndent($jsonStr) : $jsonStr;
    }
    
    /**
     * Return success jsonp result to client
     * @param  string $data
     */
    private function jsonpOk($data, $message='', $callbackName=null, $indent=false)
    {
        if (!$callbackName) $callbackName = '_callback';
        echo $callbackName.'(';
        $this->jsonOk($data, $message, $indent);
        echo ');';
    }

    /**
     * Return error jsonp result to client
     * @param  string $data
     */
    private function jsonpErr($message='', $callbackName=null, $indent=false)
    {
        if (!$callbackName) $callbackName = '_callback';
        echo $callbackName.'(';
        $this->jsonErr($message, $indent);
        echo ');';
    }

    /**
     * Indents a flat JSON string to make it more human-readable.
     *
     * @param string $json The original JSON string to process.
     *
     * @return string Indented version of the original JSON string.
     */
    private function jsonIndent($json)
    {
        $result      = '';
        $pos         = 0;
        $strLen      = strlen($json);
        $indentStr   = '  ';
        $newLine     = "\n";
        $prevChar    = '';
        $outOfQuotes = true;

        for ($i=0; $i<=$strLen; $i++) {

            // Grab the next character in the string.
            $char = substr($json, $i, 1);

            // Are we inside a quoted string?
            if ($char == '"' && $prevChar != '\\') {
                $outOfQuotes = !$outOfQuotes;

            // If this character is the end of an element, 
            // output a new line and indent the next line.
            } else if(($char == '}' || $char == ']') && $outOfQuotes) {
                $result .= $newLine;
                $pos --;
                for ($j=0; $j<$pos; $j++) {
                    $result .= $indentStr;
                }
            }

            // Add the character to the result string.
            $result .= $char;

            // If the last character was the beginning of an element, 
            // output a new line and indent the next line.
            if (($char == ',' || $char == '{' || $char == '[') && $outOfQuotes) {
                $result .= $newLine;
                if ($char == '{' || $char == '[') {
                    $pos ++;
                }

                for ($j = 0; $j < $pos; $j++) {
                    $result .= $indentStr;
                }
            }

            $prevChar = $char;
        }

        return $result;
    }
}
