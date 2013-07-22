<?php

Class BackendController extends Controller
{

    /**
     * @var array
     */
    protected $_messages = array(
        'zh_CN' => array(
            // -err-
            'nothing to update'                             => '不需要更新',
            // -ok-
            'process add successfully'                      => '进程添加成功',
            'process update successfully'                   => '进程更新成功',
            'process delete successfully'                   => '进程删除成功',
            'process start successfully'                    => '进程启动成功',
            'process stop successfully'                     => '进程停止成功',
            'process restart successfully'                  => '进程重启成功',
            'get process status successfully'               => '获取进程状态成功',
            'get all process status successfully'           => '获取所有进程状态成功',
            'get process\'s output successfully'            => '读取进程输出成功',
            'get process\'s memory usage successfully'      => '获取进程内存使用量成功',
            'get all process\'s memory usage successfully'  => '获取所有进程的内存使用量成功',
            'get process info successfully'                 => '获取进程信息成功',
            'get all process info successfully'             => '获取所有进程信息成功',
            'get server memory usage successfully'          => '获取服务器内存使用量成功',
            'get server output successfully'                => '读取服务器输出成功',
            'get auth enable status successfully'           => '获取授权启用状态成功',
            'set auth enable status successfully'           => '设置授权启用状态成功',
            'user add successfully'                         => '用户添加成功',
            'user update successfully'                      => '用户更新成功',
            'user delete successfully'                      => '用户删除成功',
            'get user info successfully'                    => '获取用户信息成功',
            'get all user info successfully'                => '获取所有用户信息成功',
            'list log dir successfully'                     => '获取日志目录列表成功',
            'list log file successfully'                    => '获取日志文件列表成功',
            'get log file content successfully'             => '读取日志文件内容成功',
            'list server log dir successfully'              => '获取服务器日志目录列表成功',
            'list server log file successfully'             => '获取服务器日志文件列表成功',
            'get server log file content successfully'      => '读取服务器日志文件内容成功',
            'schedule add successfully'                     => '进程调度配置添加成功',
            'schedule update successfully'                  => '进程调度配置更新成功',
            'schedule delete successfully'                  => '进程调度配置删除成功',
            'get schedule info successfully'                => '进程调度配置信息成功',
            'get all schedule info successfully'            => '获取所有进程调度信息成功',
            'get schedule log successfully'                 => '获取调度配置执行历史成功',
        ),
    );

    /**
     * @var array
     */
    protected $_fields = array(
        'zh_CN' => array(
            'jobname'       => '进程名称',
            'command'       => '程序路径',
            'enable'        => '是否启用',
            'username'      => '用户名',
            'password'      => '密码',
            'privileges'    => '权限',
            'dirname'       => '日志目录名',
            'filename'      => '日志文件名',
            'scheduleid'    => '进程调度ID',
        ),
    );

    /**
     * Constructor
     * @param  object $app
     */
    public function __construct($app)
    {
        parent::__construct($app);
        $this->auth();

        $this->parseServerAuthParams();
    }

    /**
     * Parser username and password to setting
     */
    private function parseServerAuthParams()
    {
        $authinfo = $this->req->get('auth');
        $this->setting = array(
            'auth' => array(
                'username' => isset($authinfo['username']) ? $authinfo['username'] : '',
                'password' => isset($authinfo['password']) ? $authinfo['password'] : '',
            ),
        );
    }

    /**
     * Add process
     */
    public function addAction()
    {
        $jobname = $this->requireNotEmptyParam('jobname');
        $command = $this->requireNotEmptyParam('command');
        $comment = $this->req->get('comment');
        $params = $this->req->get('params');
        $writelog = $this->req->get('writelog');
        $autostart = $this->req->get('autostart');
        $guard = $this->req->get('guard');

        $this->setting['comment'] = is_null($comment) ? '' : $comment;
        $this->setting['params'] = is_null($params) ? '' : $params;
        $this->setting['writelog'] = $writelog ? true : false;
        $this->setting['autostart'] = $autostart ? true : false;
        $this->setting['guard'] = $guard ? true : false;

        $result = $this->backend->add($jobname, $command, $this->setting);
        $this->processUnnormalBackendResult($result);

        $this->apiOk(null, 'process add successfully');
    }

    /**
     * Update process
     */
    public function updateAction()
    {
        $jobname = $this->requireNotEmptyParam('jobname');
        $command = $this->req->get('command');
        $comment = $this->req->get('comment');
        $params = $this->req->get('params');
        $writelog = $this->req->get('writelog');
        $autostart = $this->req->get('autostart');
        $guard = $this->req->get('guard');

        if (!is_null($command)) {
            $this->requireNotEmptyParam('command');
            $this->setting['command'] = $command;
        }
        if (!is_null($comment)) $this->setting['comment'] = $comment;
        if (!is_null($params)) $this->setting['params'] = $params;
        if (!is_null($writelog)) $this->setting['writelog'] = $writelog ? true : false;
        if (!is_null($autostart)) $this->setting['autostart'] = $autostart ? true : false;
        if (!is_null($guard)) $this->setting['guard'] = $guard ? true : false;

        if (!$this->setting) $this->apiErr('nothing to update');

        $result = $this->backend->update($jobname, $this->setting);
        $this->processUnnormalBackendResult($result);

        $this->apiOk(null, 'process update successfully');
    }

    /**
     * Delete process
     */
    public function deleteAction()
    {
        $jobname = $this->requireNotEmptyParam('jobname');

        $result = $this->backend->delete($jobname, $this->setting);
        $this->processUnnormalBackendResult($result);

        $this->apiOk(null, 'process delete successfully');
    }

    /**
     * Start process
     */
    public function startAction()
    {
        $jobname = $this->requireNotEmptyParam('jobname');

        $result = $this->backend->start($jobname, $this->setting);
        $this->processUnnormalBackendResult($result);

        $this->apiOk(null, 'process start successfully');
    }

    /**
     * Stop process
     */
    public function stopAction()
    {
        $jobname = $this->requireNotEmptyParam('jobname');

        $result = $this->backend->stop($jobname, $this->setting);
        $this->processUnnormalBackendResult($result);

        $this->apiOk(null, 'process stop successfully');
    }

    /**
     * Restart process
     */
    public function restartAction()
    {
        $jobname = $this->requireNotEmptyParam('jobname');

        $result = $this->backend->restart($jobname, $this->setting);
        $this->processUnnormalBackendResult($result);

        $this->apiOk(null, 'process restart successfully');
    }

    /**
     * Get process status
     */
    public function statusAction()
    {
        $jobname = $this->requireNotEmptyParam('jobname');

        $result = $this->backend->status($jobname, $this->setting);
        $this->processUnnormalBackendResult($result);

        $this->apiOk(array(
            'status' => $result['data'],
        ), 'get process status successfully');
    }

    /**
     * Get all process status
     */
    public function statusAllAction()
    {
        $result = $this->backend->statusall($this->setting);
        $this->processUnnormalBackendResult($result);

        $this->apiOk(array(
            'statuses' => $result['data'],
        ), 'get all process status successfully');
    }

    /**
     * Read process's output
     */
    public function readAction()
    {
        $jobname = $this->requireNotEmptyParam('jobname');

        $result = $this->backend->read($jobname, $this->setting);
        $this->processUnnormalBackendResult($result);

        $this->apiOk(array(
            'output' => $result['data'],
        ), 'get process\'s output successfully');
    }

    /**
     * Get process's memory usage
     */
    public function memAction()
    {
        $jobname = $this->requireNotEmptyParam('jobname');

        $result = $this->backend->mem($jobname, $this->setting);
        $this->processUnnormalBackendResult($result);

        $this->apiOk(array(
            'memory' => $result['data'],
        ), 'get process\'s memory usage successfully');
    }

    /**
     * Get all process's memory usage
     */
    public function memAllAction()
    {
        $result = $this->backend->memall($this->setting);
        $this->processUnnormalBackendResult($result);

        $this->apiOk(array(
            'memories' => $result['data'],
        ), 'get all process\'s memory usage successfully');
    }

    /**
     * Get process info
     */
    public function getAction()
    {
        $jobname = $this->requireNotEmptyParam('jobname');

        $result = $this->backend->get($jobname, $this->setting);
        $this->processUnnormalBackendResult($result);

        $this->apiOk(array(
            'process' => $result['data'],
        ), 'get process info successfully');
    }

    /**
     * Get all process info
     */
    public function getAllAction()
    {
        $result = $this->backend->getall($this->setting);
        $this->processUnnormalBackendResult($result);

        $this->apiOk(array(
            'processes' => $result['data'],
        ), 'get all process info successfully');
    }

    /**
     * Get server memory usage
     */
    public function serverMemAction()
    {
        $result = $this->backend->servermem($this->setting);
        $this->processUnnormalBackendResult($result);

        $this->apiOk(array(
            'memory' => $result['data'],
        ), 'get server memory usage successfully');
    }

    /**
     * Get server output
     */
    public function serverReadAction()
    {
        $result = $this->backend->serverread($this->setting);
        $this->processUnnormalBackendResult($result);

        $this->apiOk(array(
            'output' => $result['data'],
        ), 'get server output successfully');
    }

    /**
     * Get auth enable status
     */
    public function authGetEnableAction()
    {
        $result = $this->backend->auth_getenable($this->setting);
        $this->processUnnormalBackendResult($result);

        $this->apiOk(array(
            'enable' => $result['data'],
        ), 'get auth enable status successfully');
    }

    /**
     * Set auth enable status
     */
    public function authSetEnableAction()
    {
        $enable = $this->requireNotEmptyParam('enable') ? true : false;

        $result = $this->backend->auth_setenable($enable, $this->setting);
        $this->processUnnormalBackendResult($result);

        $this->apiOk(null, 'set auth enable status successfully');
    }

    /**
     * Add new user
     */
    public function authAddAction()
    {
        $username = $this->requireNotEmptyParam('username');
        $password = $this->requireNotEmptyParam('password');
        $privileges = $this->requireNotEmptyParam('privileges');
        $comment = $this->req->get('comment');

        if (!is_null($comment)) $this->setting['comment'] = $comment;

        $result = $this->backend->auth_add($username, $password, $privileges, $this->setting);
        $this->processUnnormalBackendResult($result);

        $this->apiOk(null, 'user add successfully');
    }

    /**
     * Update user
     */
    public function authUpdateAction()
    {
        $username = $this->requireNotEmptyParam('username');
        $password = $this->req->get('password');
        $privileges = $this->req->get('privileges');
        $comment = $this->req->get('comment');

        if (!is_null($password)) {
            $this->requireNotEmptyParam('password');
            $this->setting['password'] = $password;
        }
        if (!is_null($privileges)) {
            $this->requireNotEmptyParam('privileges');
            $this->setting['privileges'] = $privileges;
        }
        if (!is_null($comment)) {
            $this->setting['comment'] = $comment;
        }

        if (!$this->setting) $this->apiErr('nothing to update');

        $result = $this->backend->auth_update($username, $this->setting);
        $this->processUnnormalBackendResult($result);

        $this->apiOk(null, 'user update successfully');
    }

    /**
     * Delete user
     */
    public function authDeleteAction()
    {
        $username = $this->requireNotEmptyParam('username');

        $result = $this->backend->auth_delete($username, $this->setting);
        $this->processUnnormalBackendResult($result);

        $this->apiOk(null, 'user delete successfully');
    }

    /**
     * Get user info
     */
    public function authGetAction()
    {
        $username = $this->requireNotEmptyParam('username');

        $result = $this->backend->auth_get($username, $this->setting);
        $this->processUnnormalBackendResult($result);

        $this->apiOk(array(
            'user' => $result['data'],
        ), 'get user info successfully');
    }

    /**
     * Get all user info
     */
    public function authGetAllAction()
    {
        $result = $this->backend->auth_getall($this->setting);
        $this->processUnnormalBackendResult($result);

        $this->apiOk(array(
            'users' => $result['data'],
        ), 'get all user info successfully');
    }

    /**
     * List log dir
     */
    public function logListDirAction()
    {
        $jobname = $this->requireNotEmptyParam('jobname');

        $result = $this->backend->logexplorer_listdir($jobname, $this->setting);
        $this->processUnnormalBackendResult($result);

        $this->apiOk(array(
            'logdirs' => $result['data'],
        ), 'list log dir successfully');
    }

    /**
     * List log file
     */
    public function logListFileAction()
    {
        $jobname = $this->requireNotEmptyParam('jobname');
        $dirname = $this->requireNotEmptyParam('dirname');

        $result = $this->backend->logexplorer_listfile($jobname, $dirname, $this->setting);
        $this->processUnnormalBackendResult($result);

        $this->apiOk(array(
            'logfiles' => $result['data'],
        ), 'list log file successfully');
    }

    /**
     * Get log file content
     */
    public function logGetAction()
    {
        $jobname = $this->requireNotEmptyParam('jobname');
        $dirname = $this->requireNotEmptyParam('dirname');
        $filename = $this->requireNotEmptyParam('filename');

        $result = $this->backend->logexplorer_get($jobname, $dirname, $filename, $this->setting);
        $this->processUnnormalBackendResult($result);

        $this->apiOk(array(
            'log' => $result['data'],
        ), 'get log file content successfully');
    }

    /**
     * List server log dir
     */
    public function logServerListDirAction()
    {
        $result = $this->backend->logexplorer_serverlistdir($this->setting);
        $this->processUnnormalBackendResult($result);

        $this->apiOk(array(
            'logdirs' => $result['data'],
        ), 'list server log dir successfully');
    }

    /**
     * List server log file
     */
    public function logServerListFileAction()
    {
        $dirname = $this->requireNotEmptyParam('dirname');

        $result = $this->backend->logexplorer_serverlistfile($dirname, $this->setting);
        $this->processUnnormalBackendResult($result);

        $this->apiOk(array(
            'logfiles' => $result['data'],
        ), 'list server log file successfully');
    }

    /**
     * Get server log file content
     */
    public function logServerGetAction()
    {
        $dirname = $this->requireNotEmptyParam('dirname');
        $filename = $this->requireNotEmptyParam('filename');

        $result = $this->backend->logexplorer_serverget($dirname, $filename, $this->setting);
        $this->processUnnormalBackendResult($result);

        $this->apiOk(array(
            'log' => $result['data'],
        ), 'get server log file content successfully');
    }

    /**
     * Add new schedule
     */
    public function scheduleAddAction()
    {
        $jobname = $this->requireNotEmptyParam('jobname');
        $enable = $this->requireNotEmptyParam('enable');
        $condition = $this->requireNotEmptyParam('condition');

        $this->setting['enable'] = $enable;
        $this->setting['condition'] = $condition;

        $result = $this->backend->scheduler_add($jobname, $this->setting);
        $this->processUnnormalBackendResult($result);

        $this->apiOk(array(
            'scheduleid' => $result['data'],
        ), 'schedule add successfully');
    }

    /**
     * Update schedule
     */
    public function scheduleUpdateAction()
    {
        $jobname = $this->requireNotEmptyParam('jobname');
        $scheduleid = $this->requireNotEmptyParam('scheduleid');
        $enable = $this->req->get('enable');
        $condition = $this->req->get('condition');

        if (!is_null($enable)) {
            $this->requireNotEmptyParam('enable');
            $this->setting['enable'] = $enable;
        }
        if (!is_null($condition)) {
            $this->requireNotEmptyParam('condition');
            $this->setting['condition'] = $condition;
        }

        if (!$this->setting) $this->apiErr('nothing to update');

        $result = $this->backend->scheduler_update($jobname, $scheduleid, $this->setting);
        $this->processUnnormalBackendResult($result);

        $this->apiOk(null, 'schedule update successfully');
    }

    /**
     * Delete schedule
     */
    public function scheduleDeleteAction()
    {
        $jobname = $this->requireNotEmptyParam('jobname');
        $scheduleid = $this->requireNotEmptyParam('scheduleid');

        $result = $this->backend->scheduler_delete($jobname, $scheduleid, $this->setting);
        $this->processUnnormalBackendResult($result);

        $this->apiOk(null, 'schedule delete successfully');
    }

    /**
     * Get schedule info
     */
    public function scheduleGetAction()
    {
        $jobname = $this->requireNotEmptyParam('jobname');
        $scheduleid = $this->requireNotEmptyParam('scheduleid');

        $result = $this->backend->scheduler_get($jobname, $scheduleid, $this->setting);
        $this->processUnnormalBackendResult($result);

        $this->apiOk(array(
            'schedule' => $result['data'],
        ), 'get schedule info successfully');
    }

    /**
     * Get all schedule info
     */
    public function scheduleGetAllAction()
    {
        $result = $this->backend->scheduler_getall($this->setting);
        $this->processUnnormalBackendResult($result);

        $this->apiOk(array(
            'schedules' => $result['data'],
        ), 'get all schedule info successfully');
    }

    /**
     * Get schedule log
     */
    public function scheduleGetLogAction()
    {
        $jobname = $this->requireNotEmptyParam('jobname');
        $scheduleid = $this->requireNotEmptyParam('scheduleid');

        $result = $this->backend->scheduler_getlog($jobname, $scheduleid, $this->setting);
        $this->processUnnormalBackendResult($result);

        $log = $result['data'];
        foreach ($log as $i=>$time)
        {
            $log[$i] = date('Y-m-d H:i:s', $time);
        }

        $this->apiOk(array(
            'log' => $log,
        ), 'get schedule log successfully');
    }

}
