<?php

Class UserController extends Controller
{

    /**
     * @var array
     */
    protected $_messages = array(
        'zh_CN' => array(
            // -err-
            'already login, please logout first'    => '已经登录，请先注销',
            'username or password does not match'   => '用户名或密码不正确',
            'nothing to update'                     => '不需要更新',
            // -ok-
            'login successfully'                    => '登录成功',
            'logout successfully'                   => '注销成功',
            'update successfully'                   => '更新成功',
            'get user info successfully'            => '获取用户信息成功',
        ),
    );

    /**
     * @var array
     */
    protected $_fields = array(
        'zh_CN' => array(
            'username'      => '用户名',
            'password'      => '密码',
        ),
    );

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
     * Get current user info
     */
    public function getAction()
    {
        $this->auth();

        $this->apiOk(array(
            'username' => $_SESSION['username'],
        ), 'get user info successfully');
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
        if (!is_null($password)) $stmt->bindValue(':password', md5($password), SQLITE3_TEXT);
        $stmt->bindValue(':origUsername', $origUsername, SQLITE3_TEXT);
        $stmt->execute();

        $_SESSION['username'] = $origUsername;

        $this->apiOk(array(
            'username' => $origUsername,
        ), 'update successfully');
    }
}
