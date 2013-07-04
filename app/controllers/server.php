<?php

Class ServerController extends Controller
{
    /**
     * Constructor
     * @param  object $app
     */
    public function __construct($app)
    {
        parent::__construct($app);
        $this->auth();
    }

    /**
     * Add server
     */
    public function addAction()
    {
        $servername = $this->requireNotEmptyParam('servername');
        $serverip = $this->requireNotEmptyParam('serverip');
        $serverport = $this->requireNotEmptyParam('serverport');
        $username = $this->req->get('username');
        $password = $this->req->get('password');

        if ($this->serverExists($serverip, $serverport)) {
            $this->apiErr('server already exists');
        }

        $stmt = $this->db->prepare(
            'INSERT INTO servers (servername, serverip, serverport, username, password) '
            .' VALUES(:servername, :serverip, :serverport, :username, :password)');
        $stmt->bindValue(':servername', $servername, SQLITE3_TEXT);
        $stmt->bindValue(':serverip', $serverip, SQLITE3_TEXT);
        $stmt->bindValue(':serverport', $serverport, SQLITE3_TEXT);
        $stmt->bindValue(':username', $username, SQLITE3_TEXT);
        $stmt->bindValue(':password', $password, SQLITE3_TEXT);
        $stmt->execute();

        $serverid = $this->db->lastInsertRowID();

        $this->apiOk(array(
            'serverid' => $serverid,
        ), 'server add successfully');
    }

    /**
     * Update server
     */
    public function updateAction()
    {
        $serverid = $this->requireNotEmptyParam('serverid');
        if (!$this->serveridExists($serverid)) {
            $this->apiErr('server does not exist');
        }

        $servername = $this->req->get('servername');
        $serverip = $this->req->get('serverip');
        $serverport = $this->req->get('serverport');
        $username = $this->req->get('username');
        $password = $this->req->get('password');

        $statements = array();
        if (!is_null($servername)) {
            $this->requireNotEmptyParam('servername');
            $statements[] = 'servername=:servername';
        }
        if (!is_null($serverip)) {
            $this->requireNotEmptyParam('serverip');
            $statements[] = 'serverip=:serverip';
        }
        if (!is_null($serverport)) {
            $this->requireNotEmptyParam('serverport');
            $statements[] = 'serverport=:serverport';
        }
        if (!is_null($username)) $statements[] = 'username=:username';
        if (!is_null($password)) $statements[] = 'password=:password';

        if (!$statements) $this->apiErr('nothing to update');

        $sql = 'UPDATE servers SET '.implode(',', $statements).' WHERE serverid=:serverid';
        $stmt = $this->db->prepare($sql);
        if (!is_null($servername)) $stmt->bindValue(':servername', $servername, SQLITE3_TEXT);
        if (!is_null($serverip)) $stmt->bindValue(':serverip', $serverip, SQLITE3_TEXT);
        if (!is_null($serverport)) $stmt->bindValue(':serverport', $serverport, SQLITE3_TEXT);
        if (!is_null($username)) $stmt->bindValue(':username', $username, SQLITE3_TEXT);
        if (!is_null($password)) $stmt->bindValue(':password', $password, SQLITE3_TEXT);
        $stmt->bindValue(':serverid', $serverid, SQLITE3_INTEGER);
        $stmt->execute();

        $this->apiOk(array(
            'serverid' => $serverid,
        ), 'server update successfully');
    }

    /**
     * Delete server
     */
    public function deleteAction()
    {
        $serverid = $this->requireNotEmptyParam('serverid');
        if (!$this->serveridExists($serverid)) {
            $this->apiErr('server does not exist');
        }

        $stmt = $this->db->prepare('DELETE FROM servers WHERE serverid=:serverid');
        $stmt->bindValue(':serverid', $serverid, SQLITE3_INTEGER);
        $stmt->execute();

        $this->apiOk(array(
            'serverid' => $serverid,
        ), 'server delete successfully');
    }

    /**
     * List server
     */
    public function listAction()
    {
        $result = $this->db->query('SELECT serverid, servername, serverip, serverport, username FROM servers');

        $servers = array();
        while ($server = $result->fetchArray(SQLITE3_ASSOC)) {
            $servers[] = $server;
        }

        $this->apiOk(array(
            'servers' => $servers,
        ), 'server list fetch successfully');
    }

    /**
     * Check if server with specified ip:port exists
     * @param string $serverip
     * @param string $serverport
     */
    private function serverExists($serverip, $serverport)
    {
        $stmt = $this->db->prepare('SELECT serverid FROM servers WHERE serverip=:serverip AND serverport=:serverport');
        $stmt->bindValue(':serverip', $serverip, SQLITE3_TEXT);
        $stmt->bindValue(':serverport', $serverport, SQLITE3_TEXT);
        $result = $stmt->execute();
        $server = $result->fetchArray(SQLITE3_ASSOC);

        return $server ? true : false;
    }

    /**
     * Check if server with specified id exists
     * @param int $serverid
     */
    private function serveridExists($serverid)
    {
        $stmt = $this->db->prepare('SELECT serverid FROM servers WHERE serverid=:serverid');
        $stmt->bindValue(':serverid', $serverid, SQLITE3_INTEGER);
        $result = $stmt->execute();
        $server = $result->fetchArray(SQLITE3_ASSOC);

        return $server ? true : false;
    }
}
