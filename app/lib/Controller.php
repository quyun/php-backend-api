<?php

Class Controller
{
    /**
     * @var object
     */
    protected $_db;

    /**
     * Constructor
     * @param  object $app
     */
    public function __construct($app)
    {
        $this->app = $app;
        $this->req = $app->request();

        session_start();
    }

    public function __get($name)
    {
        if ($name == 'db') return $this->db();
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
            $this->apiErr(null, 'can not open database');
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
     * Get a required param's value from request
     * @param  string $paramName
     */
    protected function requireParam($paramName)
    {
        $value = $this->req->get($paramName);
        if (is_null($value)) {
            $this->apiErr("{$paramName} is required");
        }
        return $value;
    }

    /**
     * Get a required not empty param's value from request
     * @param  string $paramName
     */
    protected function requireNotEmptyParam($paramName)
    {
        $value = $this->req->get($paramName);
        if (is_null($value)) {
            $this->apiErr("{$paramName} is required");
        }
        if (empty($value)) {
            $this->apiErr("{$paramName} is not allow to be empty");
        }
        return $value;
    }

    /**
     * Return success result to client
     * @param  string $data
     */
    protected function apiOk($data, $message='')
    {
        $dataFormat = $this->req->get('f');
        $indent = $this->req->get('i');
        if ($dataFormat == 'jsonp') {
            $callbackName = $this->req->get('cb');
            $this->jsonpOk($data, $message, $callbackName, $indent);
        } else {
            $res = $this->app->response();
            $res['Content-Type'] = 'application/json';
            $this->jsonOk($data, $message, $indent);
        }
        $this->app->stop();
    }

    /**
     * Return error result to client
     */
    public function apiErr($message='')
    {
        $dataFormat = $this->req->get('f');
        $indent = $this->req->get('i');
        if ($dataFormat == 'jsonp') {
            $callbackName = $this->req->get('cb');
            $this->jsonpErr($message, $callbackName, $indent);
        } else {
            $res = $this->app->response();
            $res['Content-Type'] = 'application/json';
            $this->jsonErr($message, $indent);
        }
        $this->app->stop();
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
