<?php

Class UserController extends Controller
{
    /**
     * Login
     */
    public function loginAction()
    {
        $username = $this->requireNotEmptyParam('username');
        $password = $this->requireNotEmptyParam('password');

        if ($_SESSION['username']) {
            $this->apiErr('already login, please logout first');
        }

        $this->initDb();

        $stmt = $this->db->prepare('SELECT username, password FROM users WHERE username=:username');
        $stmt->bindValue(':username', $username, SQLITE3_TEXT);
        $result = $stmt->execute();
        $user = $result->fetchArray(SQLITE3_ASSOC);

        if (!$user || md5($password) != $user['password']) {
            $this->apiErr('username or password does not match');
        }

        $_SESSION['username'] = $username;

        $this->apiOk(array(
            'username' => $username,
        ), 'login successfully');
    }

    /**
     * Logout
     */
    public function logoutAction()
    {
        $this->auth();

        $username = $_SESSION['username'];
        session_destroy();

        $this->apiOk(array(
            'username' => $username,
        ), 'logout successfully');
    }

    /**
     * Update
     */
    public function updateAction()
    {
        $this->auth();

        $username = $this->req->get('username');
        $password = $this->req->get('password');
        $origUsername = $_SESSION['username'];

        $statements = array();
        if (!is_null($username)) {
            $this->requireNotEmptyParam('username');
            $statements[] = 'username=:username';
        }
        if (!is_null($password)) {
            $this->requireNotEmptyParam('password');
            $statements[] = 'password=:password';
        }

        if (!$statements) $this->apiErr('nothing to update');

        $sql = 'UPDATE users SET '.implode(',', $statements).' WHERE username=:origUsername';
        $stmt = $this->db->prepare($sql);
        if (!is_null($username)) $stmt->bindValue(':username', $username, SQLITE3_TEXT);
        if (!is_null($password)) $stmt->bindValue(':password', $password, SQLITE3_TEXT);
        $stmt->bindValue(':origUsername', $origUsername, SQLITE3_TEXT);
        $stmt->execute();

        $_SESSION['username'] = $origUsername;

        $this->apiOk(array(
            'username' => $origUsername,
        ), 'update successfully');
    }
}
