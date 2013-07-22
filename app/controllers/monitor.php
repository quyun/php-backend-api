<?php

Class MonitorController extends Controller
{
    public function indexAction()
    {
        $serverid = $this->requireNotEmptyParam('serverid');
        $stmt = $this->db->prepare('SELECT serverkey FROM servers WHERE serverid=:serverid');
        $stmt->bindValue(':serverid', $serverid, SQLITE3_INTEGER);
        $result = $stmt->execute();
        $server = $result->fetchArray(SQLITE3_ASSOC);
        if (!$server) {
            $this->apiErr('server does not exist');
        }

        // check if the server key match
        if ($server['serverkey'] != null) {
            $serverkey = $this->req->get('serverkey');
            if ($serverkey != $server['serverkey']) {
                $this->apiErr('server key does not match');
            }   
        }

        $authinfo = $this->req->get('auth');
        $setting = array(
            'auth' => array(
                'username' => isset($authinfo['username']) ? $authinfo['username'] : '',
                'password' => isset($authinfo['password']) ? $authinfo['password'] : '',
            ),
        );

        $jobnames = $this->requireNotEmptyParam('jobnames');

        $jobnames = array_unique(explode(',', $jobnames));
        $statuses = array();
        $errorExists = false;

        foreach ($jobnames as $jobname) {
            $result = $this->backend->status($jobname, $setting);
            if (!$result) {
                $this->apiErr('unable to connect to backend server');
            }
            if ($result['code'] != 'OK') {
                $statuses[$jobname] = 'ERR';
                $errorExists = true;
                continue;
            }

            $statuses[$jobname] = $result['data'];
            if ($result['data'] != 'UP') $errorExists = true;
        }

        if ($errorExists) $this->app->response()->status(500);

        echo '<table style="margin:50px auto;font-size:36px;font-weight:bold;line-height:50px;">';
        echo '<tr><td style="border-bottom:1px solid gray;width:300px">Job</td><td style="border-bottom:1px solid #000;width:120px">Stat</td></tr>';
        foreach ($statuses as $jobname => $status) {
            echo '<tr>';
            echo "<td>$jobname</td>";
            echo '<td style="color:'.($status=='UP' ? 'green' : 'red').'">'.$status.'</td>';
            echo '</tr>';
        }
        echo '</table>';
    }

    protected function apiErr($message)
    {
        $this->app->response()->status(500);
        echo '<p style="color:red;text-align:center;margin-top:50px;font-size:36px;font-weight:bold;">';
        echo $message;
        echo '</p>';
        $this->app->stop();
    }
}
