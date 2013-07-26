php-backend-api
==================

基于 [php-backend-server](https://github.com/quyun/php-backend-server) 封装的 WEB API，可以通过 HTTP/HTTPS 协议来调用。

目前已用于后台进程管理控制中心 [php-backend-web](https://github.com/quyun/php-backend-web)。


## 环境依赖

- PHP 5.3+
- PHP SQLite 扩展

请确保 app/data/ 目录可写入。


## 接口规范

#### 接口地址

php-backend-api 部署的 URL 地址，下文中以 {API_GATEWAY} 代替。

#### 公用参数

```
- c     模块名，如：user
- a     操作名，如：login
- i     json缩进开关，当设置为 1 时，将会对返回值进程格式化输出（方便调试查看）
- f     数据格式，可选值：json|jsonp，默认为json
- cb    jsonp回调函数名，仅当 f 为 jsonp 时有效
- l     输出的消息语言，默认为英文，可选值为：zh_CN
```

示例：

    {API_GATEWAY}/?c=user&a=login&i=1&f=jsonp&cb=callback&username=admin&password=admin

#### 返回格式

除外部监控接口外，其它所有接口的返回格式如下：

成功返回：

```
{
  "code":0,
  "message":"成功提示信息",
  "data":{
    // 返回数据
  }
}
```

失败返回：

```
{
  "code":-1,
  "message":"失败提示信息"
}
```


## 接口快速参考

```
- user                  用户类接口
  - login               登录
  - logout              注销
  - get                 获取当前登录的用户信息
  - update              更改用户名/密码

- server                服务器类接口
  - add                 添加
  - update              更新
  - delete              删除
  - list                查询服务器列表
  - get                 获取服务器信息

- backend               后台进程服务器控制类接口
  - add                 添加进程
  - update              更新进程
  - delete              删除进程
  - get                 获取进程信息
  - getAll              获取所有进程信息
  - start               启动进程
  - stop                停止进程
  - restart             重启进程
  - status              获取进程状态
  - statusAll           获取所有进程状态
  - read                读取进程输出缓冲
  - mem                 查询进程内存使用量
  - memAll              查询所有进程的内存使用量
  - serverMem           查询进程服务器的内存使用量
  - serverRead          读取进程服务器的输出缓冲
  - authGetEnable       读取授权管理是否启用
  - authSetEnable       设置授权管理是否启用
  - authAdd             添加用户
  - authUpdate          更新用户
  - authDelete          删除用户
  - authGet             获取用户信息
  - authGetAll          获取所有用户信息
  - logListDir          获取日志目录列表
  - logListFile         获取日志目录文件
  - logGet              读取日志文件内容
  - logServerListDir    获取服务器日志目录列表
  - logServerListFile   获取服务器日志目录文件
  - logServerGet        读取服务器日志文件内容
  - scheduleAdd         添加调度配置
  - scheduleUpdate      更新调度配置
  - scheduleDelete      删除调度配置
  - scheduleGet         获取调度配置
  - scheduleGetAll      获取所有调度配置信息
  - scheduleGetLog      获取调度执行历史

- monitor               外部监控接口
```


## 接口文档


#### user - 用户类接口


##### login - 登录

请求参数：

    username    (必须)用户名
    password    (必须)密码

返回数据：

    username    用户名

示例请求：

    {API_GATEWAY}/?c=user&a=login&i=1&username=admin&password=admin


示例返回：

```
{
  "code":0,
  "message":"login successfully",
  "data":{
    "username":"admin"
  }
}
```


##### logout - 注销

请求参数：

    无

返回数据：

    username    用户名

示例请求：

    {API_GATEWAY}/?c=user&a=logout&i=1


示例返回：

```
{
  "code":0,
  "message":"logout successfully",
  "data":{
    "username":"admin"
  }
}
```


##### get - 获取当前登录的用户信息

请求参数：

    无

返回数据：

    username    用户名

示例请求：

    {API_GATEWAY}/?c=user&a=get&i=1


示例返回：

```
{
  "code":0,
  "message":"get user info successfully",
  "data":{
    "username":"admin"
  }
}
```


##### update - 更改用户名/密码

请求参数：

    username    (选填)用户名
    password    (选填)密码

返回数据：

    username    原用户名

示例请求：

    {API_GATEWAY}/?c=user&a=update&i=1&password=quyun

示例返回：

```
{
  "code":0,
  "message":"update successfully",
  "data":{
    "username":"admin"
  }
}
```


#### server - 服务器类接口

此处的服务器指的是 PHP 后台进程管理服务器（[php-backend-server](https://github.com/quyun/php-backend-server)）实例。


##### add - 添加

请求参数：

    servername  (必须)服务器名称
    serverip    (必须)服务器IP地址
    serverport  (必须)服务器端口
    serverkey   (可选)服务器监控密钥
    username    (可选)服务器用户名
    password    (可选)服务器密码

返回数据：

    serverid    新添加的服务器ID

示例请求：

    {API_GATEWAY}/?c=server&a=add&i=1&servername=localhost&serverip=127.0.0.1&serverport=13123


示例返回：

```
{
  "code":0,
  "message":"server add successfully",
  "data":{
    "serverid":1
  }
}
```


##### update - 更新

请求参数：

    serverid    (必须)服务器ID
    servername  (可选)服务器名称
    serverip    (可选)服务器IP地址
    serverport  (可选)服务器端口
    serverkey   (可选)服务器监控密钥
    username    (可选)服务器用户名
    password    (可选)服务器密码

返回数据：

    serverid    服务器ID

示例请求：

    {API_GATEWAY}/?c=server&a=update&i=1&serverid=1&serverport=13123


示例返回：

```
{
  "code":0,
  "message":"server update successfully",
  "data":{
    "serverid":"1"
  }
}
```


##### delete - 删除

请求参数：

    serverid    (必须)服务器ID

返回数据：

    serverid    被删除的服务器ID

示例请求：

    {API_GATEWAY}/?c=server&a=delete&i=1&serverid=1


示例返回：

```
{
  "code":0,
  "message":"server delete successfully",
  "data":{
    "serverid":"1"
  }
}
```


##### list - 查询服务器列表

请求参数：

    无

返回数据：

    servers    服务器列表

示例请求：

    {API_GATEWAY}/?c=server&a=list&i=1


示例返回：

```
{
  "code":0,
  "message":"server list fetch successfully",
  "data":{
    "servers":[
      {
        "serverid":1,
        "servername":"localhost",
        "serverip":"127.0.0.1",
        "serverport":13123,
        "serverkey":null,
        "username":null
      }
    ]
  }
}
```


##### get - 获取服务器信息

请求参数：

    serverid    (必须)服务器ID

返回数据：

    server      服务器信息

示例请求：

    {API_GATEWAY}/?c=server&a=get&i=1&serverid=1


示例返回：

```
{
  "code":0,
  "message":"get server info successfully",
  "data":{
    "server":{
      "serverid":1,
      "servername":"localhost",
      "serverip":"127.0.0.1",
      "serverport":13123,
      "serverkey":null,
      "username":null
    }
  }
}
```


#### backend - 后台进程服务器控制类接口

此处的服务器指的是 PHP 后台进程管理服务器（[php-backend-server](https://github.com/quyun/php-backend-server)）实例。


##### add - 添加进程

请求参数：

    serverid    (必须)服务器ID
    jobname     (必须)进程名称
    command     (必须)程序路径
    params      (可选)程序参数
    comment     (可选)进程备注
    writelog    (可选)是否将进程输出写入日志，取值：0|1，默认为0
    autostart   (可选)是否随服务器启动，取值：0|1，默认为0
    guard       (可选)是否监控该进程，非人为退出后自动启动，取值：0|1，默认为0

返回数据：

    无

示例请求：

    {API_GATEWAY}/?c=backend&a=add&i=1&serverid=1&jobname=test&command=/var/www/test.php


示例返回：

```
{
  "code":0,
  "message":"process add successfully",
  "data":null
}
```


##### update - 更新进程

请求参数：

    serverid    (必须)服务器ID
    jobname     (必须)进程名称
    command     (可选)程序路径
    params      (可选)程序参数
    comment     (可选)进程备注
    writelog    (可选)是否将进程输出写入日志，取值：0|1，默认为0
    autostart   (可选)是否随服务器启动，取值：0|1，默认为0
    guard       (可选)是否监控该进程，非人为退出后自动启动，取值：0|1，默认为0

返回数据：

    无

示例请求：

    {API_GATEWAY}/?c=backend&a=update&i=1&serverid=1&jobname=test&autostart=0


示例返回：

```
{
  "code":0,
  "message":"process update successfully",
  "data":null
}
```


##### delete - 删除进程

请求参数：

    serverid    (必须)服务器ID
    jobname     (必须)进程名称

返回数据：

    无

示例请求：

    {API_GATEWAY}/?c=backend&a=delete&i=1&serverid=1&jobname=test


示例返回：

```
{
  "code":0,
  "message":"process delete successfully",
  "data":null
}
```


##### get - 获取进程信息

请求参数：

    serverid    (必须)服务器ID
    jobname     (必须)进程名称

返回数据：

    process     进程信息

示例请求：

    {API_GATEWAY}/?c=backend&a=get&i=1&serverid=1&jobname=test


示例返回：

```
{
  "code":0,
  "message":"get process info successfully",
  "data":{
    "process":{
      "command":"\/var\/www\/php-backend-server\/client\/example\/scripts\/test.php",
      "params":"",
      "buffersize":20,
      "writelog":true,
      "autostart":false,
      "guard":false
    }
  }
}
```


##### getAll - 获取所有进程信息

请求参数：

    serverid    (必须)服务器ID

返回数据：

    processes   所有进程信息

示例请求：

    {API_GATEWAY}/?c=backend&a=getAll&i=1&serverid=1


示例返回：

```
{
  "code":0,
  "message":"get all process info successfully",
  "data":{
    "processes":{
      "test":{
        "command":"\/var\/www\/php-backend-server\/client\/example\/scripts\/test.php",
        "params":"",
        "buffersize":20,
        "writelog":true,
        "autostart":false,
        "guard":true
      },
      "testproc":{
        "command":"\/work\/www\/test.php",
        "params":"",
        "buffersize":20,
        "writelog":true
      }
    }
  }
}
```


##### start - 启动进程

请求参数：

    serverid    (必须)服务器ID
    jobname     (必须)进程名称

返回数据：

    无

示例请求：

    {API_GATEWAY}/?c=backend&a=start&i=1&serverid=1&jobname=test


示例返回：

```
{
  "code":0,
  "message":"process start successfully",
  "data":null
}
```


##### stop - 停止进程

请求参数：

    serverid    (必须)服务器ID
    jobname     (必须)进程名称

返回数据：

    无

示例请求：

    {API_GATEWAY}/?c=backend&a=stop&i=1&serverid=1&jobname=test


示例返回：

```
{
  "code":0,
  "message":"process stop successfully",
  "data":null
}
```


##### restart - 重启进程

请求参数：

    serverid    (必须)服务器ID
    jobname     (必须)进程名称

返回数据：

    无

示例请求：

    {API_GATEWAY}/?c=backend&a=restart&i=1&serverid=1&jobname=test


示例返回：

```
{
  "code":0,
  "message":"process restart successfully",
  "data":null
}
```


##### status - 获取进程状态

请求参数：

    serverid    (必须)服务器ID
    jobname     (必须)进程名称

返回数据：

    status      进程状态

示例请求：

    {API_GATEWAY}/?c=backend&a=status&i=1&serverid=1&jobname=test


示例返回：

```
{
  "code":0,
  "message":"get process status successfully",
  "data":{
    "status":"UP"
  }
}
```


##### statusAll - 获取所有进程状态

请求参数：

    serverid    (必须)服务器ID

返回数据：

    statuses    所有进程状态

示例请求：

    {API_GATEWAY}/?c=backend&a=statusAll&i=1&serverid=1


示例返回：

```
{
  "code":0,
  "message":"get all process status successfully",
  "data":{
    "statuses":{
      "test":"UP"
    }
  }
}
```


##### read - 读取进程输出缓冲

请求参数：

    serverid    (必须)服务器ID
    jobname     (必须)进程名称

返回数据：

    output      进程输出缓冲区的内容

示例请求：

    {API_GATEWAY}/?c=backend&a=read&i=1&serverid=1&jobname=test


示例返回：

```
{
  "code":0,
  "message":"get process's output successfully",
  "data":{
    "output":"0\n[13-07-05 01:14:02] 1\n[13-07-05 01:14:03] 2\n[13-07-05 01:14:04] "
  }
}
```


##### mem - 查询进程内存使用量

请求参数：

    serverid    (必须)服务器ID
    jobname     (必须)进程名称

返回数据：

    memory     进程的内存使用量，单位为 kB

示例请求：

    {API_GATEWAY}/?c=backend&a=mem&i=1&serverid=1&jobname=test


示例返回：

```
{
  "code":0,
  "message":"get process's memory usage successfully",
  "data":{
    "memory":"10332"
  }
}
```


##### memAll - 查询所有进程内存使用量

请求参数：

    serverid    (必须)服务器ID

返回数据：

    memories    所有进程的内存使用量，单位为 kB

示例请求：

    {API_GATEWAY}/?c=backend&a=memAll&i=1&serverid=1


示例返回：

```
{
  "code":0,
  "message":"get all process's memory usage successfully",
  "data":{
    "memories":"{\"test\":\"10332\"}"
  }
}
```


##### serverMem - 查询进程服务器的内存使用量

请求参数：

    serverid    (必须)服务器ID

返回数据：

    memory      内存使用量，单位为 kB

示例请求：

    {API_GATEWAY}/?c=backend&a=serverMem&i=1&serverid=1


示例返回：

```
{
  "code":0,
  "message":"get server memory usage successfully",
  "data":{
    "memory":"7160"
  }
}
```


##### serverRead - 读取进程服务器的输出缓冲

请求参数：

    serverid    (必须)服务器ID

返回数据：

    output      服务器输出缓冲内容

示例请求：

    {API_GATEWAY}/?c=backend&a=serverRead&i=1&serverid=1


示例返回：

```
{
  "code":0,
  "message":"get server output successfully",
  "data":{
    "output":"\n[13-07-04 19:56:06] Plugin \"logcleaner\" loaded.\n[13-07-04 19:56:06] Plugin \"auth\" loaded.\n[13-07-04 19:56:06] Plugin \"autostart\" loaded.\n[13-07-04 19:56:06] Plugin \"logexplorer\" loaded.\n[13-07-04 19:56:06] Plugin \"scheduler\" loaded.\n[13-07-04 19:56:06] Plugin \"guarder\" loaded.\n[13-07-04 19:56:06] Backend server starting, binding 127.0.0.1:13123.\n[13-07-04 19:56:06] Waiting for new command...\n[13-07-04 19:56:08] SERVERREAD"
  }
}
```


##### authGetEnable - 读取授权管理是否启用

请求参数：

    serverid    (必须)服务器ID

返回数据：

    enable      是否启用授权管理，可选值：true|false

示例请求：

    {API_GATEWAY}/?c=backend&a=authGetEnable&i=1&serverid=1


示例返回：

```
{
  "code":0,
  "message":"get auth enable status successfully",
  "data":{
    "enable":false
  }
}
```


##### authSetEnable - 设置授权管理是否启用

请求参数：

    serverid    (必须)服务器ID
    enable      (必须)是否启用授权管理，可选值：0|1

返回数据：

    无

示例请求：

    {API_GATEWAY}/?c=backend&a=authSetEnable&i=1&serverid=1&enable=1


示例返回：

```
{
  "code":0,
  "message":"set auth enable status successfully",
  "data":null
}
```


##### authAdd - 添加用户

请求参数：

    serverid    (必须)服务器ID
    username    (必须)用户名
    password    (必须)密码
    privileges  (必须)权限，用逗号分隔，*表示所有权限，权限列表请参考附录
    comment     (可选)用户备注

返回数据：

    无

示例请求：

    {API_GATEWAY}/?c=backend&a=authAdd&i=1&serverid=1&username=beadmin&password=quyun&privileges=*


示例返回：

```
{
  "code":0,
  "message":"user add successfully",
  "data":null
}
```


##### authUpdate - 更新用户

请求参数：

    serverid    (必须)服务器ID
    username    (必须)用户名
    password    (可选)密码
    privileges  (可选)权限，用逗号分隔，*表示所有权限，权限列表请参考附录
    comment     (可选)用户备注

返回数据：

    无

示例请求：

    {API_GATEWAY}/?c=backend&a=authUpdate&i=1&serverid=1&username=beadmin&password=quyun


示例返回：

```
{
  "code":0,
  "message":"user update successfully",
  "data":null
}
```


##### authDelete - 删除用户

请求参数：

    serverid    (必须)服务器ID
    username    (必须)用户名

返回数据：

    无

示例请求：

    {API_GATEWAY}/?c=backend&a=authDelete&i=1&serverid=1&username=beadmin


示例返回：

```
{
  "code":0,
  "message":"user delete successfully",
  "data":null
}
```


##### authGet - 获取用户信息

请求参数：

    serverid    (必须)服务器ID
    username    (必须)用户名

返回数据：

    user        用户信息

示例请求：

    {API_GATEWAY}/?c=backend&a=authGet&i=1&serverid=1&username=beadmin


示例返回：

```
{
  "code":0,
  "message":"get user info successfully",
  "data":{
    "user":{
      "password":"quyun",
      "privileges":"*"
    }
  }
}
```


##### authGetAll - 获取所有用户信息

请求参数：

    serverid    (必须)服务器ID

返回数据：

    user        所有用户信息

示例请求：

    {API_GATEWAY}/?c=backend&a=authGet&i=1&serverid=1


示例返回：

```
{
  "code":0,
  "message":"get all user info successfully",
  "data":{
    "users":{
      "beadmin":{
        "password":"quyun",
        "privileges":"*"
      }
    }
  }
}
```


##### logListDir - 获取日志目录列表

请求参数：

    serverid    (必须)服务器ID
    jobname     (必须)进程名称

返回数据：

    logdirs     日志目录列表

示例请求：

    {API_GATEWAY}/?c=backend&a=logListDir&i=1&serverid=1&jobname=test


示例返回：

```
{
  "code":0,
  "message":"list log dir successfully",
  "data":{
    "logdirs":[
      "20130705",
      "20130704",
      "20130702",
      "20130701"
    ]
  }
}
```


##### logListFile - 获取日志文件列表

请求参数：

    serverid    (必须)服务器ID
    jobname     (必须)进程名称
    dirname     (必须)日志目录名称

返回数据：

    logfiles    日志文件列表

示例请求：

    {API_GATEWAY}/?c=backend&a=logListFile&i=1&serverid=1&jobname=test&dirname=20130705


示例返回：

```
{
  "code":0,
  "message":"list log file successfully",
  "data":{
    "logfiles":[
      "2013070510.log",
      "2013070509.log",
      "2013070508.log",
      "2013070507.log",
      "2013070506.log",
      "2013070505.log",
      "2013070504.log",
      "2013070503.log",
      "2013070502.log",
      "2013070501.log",
      "2013070500.log"
    ]
  }
}
```


##### logGet - 读取日志文件内容

请求参数：

    serverid    (必须)服务器ID
    jobname     (必须)进程名称
    dirname     (必须)日志目录名称
    filename    (必须)日志文件名称

返回数据：

    log         日志文件内容

示例请求：

    {API_GATEWAY}/?c=backend&a=logGet&i=1&serverid=1&jobname=test&dirname=20130705&filename=2013070510.log


示例返回：

```
{
  "code":0,
  "message":"get log file content successfully",
  "data":{
    "log":"31549\n[13-07-05 10:00:00] 31550\n[13-07-05 10:00:01] 31551\n[13-07-05 10:00:02]"
  }
}
```


##### logServerListDir - 获取服务器日志目录列表

请求参数：

    serverid    (必须)服务器ID

返回数据：

    logdirs     日志目录列表

示例请求：

    {API_GATEWAY}/?c=backend&a=logServerListDir&i=1&serverid=1


示例返回：

```
{
  "code":0,
  "message":"list server log dir successfully",
  "data":{
    "logdirs":[
      "error",
      "20130705",
      "20130704",
      "20130702",
      "20130701"
    ]
  }
}
```


##### logServerListFile - 获取服务器日志文件列表

请求参数：

    serverid    (必须)服务器ID
    dirname     (必须)日志目录名称

返回数据：

    logfiles    日志文件列表

示例请求：

    {API_GATEWAY}/?c=backend&a=logServerListFile&i=1&serverid=1&jobname=test&dirname=20130705


示例返回：

```
{
  "code":0,
  "message":"list server log file successfully",
  "data":{
    "logfiles":[
      "2013070510.log",
      "2013070509.log",
      "2013070508.log",
      "2013070507.log",
      "2013070506.log",
      "2013070505.log",
      "2013070504.log",
      "2013070503.log",
      "2013070502.log",
      "2013070501.log",
      "2013070500.log"
    ]
  }
}
```


##### logServerGet - 读取服务器日志文件内容

请求参数：

    serverid    (必须)服务器ID
    dirname     (必须)日志目录名称
    filename    (必须)日志文件名称

返回数据：

    log         日志文件内容

示例请求：

    {API_GATEWAY}/?c=backend&a=logServerGet&i=1&serverid=1&jobname=test&dirname=20130705&filename=2013070510.log


示例返回：

```
{
  "code":0,
  "message":"get server log file content successfully",
  "data":{
    "log":"\n[13-07-05 10:01:07] [scheduler] starting \"test\"...\n[13-07-05 10:01:07] FAILED. (process \"test\"(3248) has already exist.)\n[13-07-05 10:06:07] [scheduler] starting \"test\"...\n[13-07-05 10:06:07] FAILED. (process \"test\"(3248) has already exist.)\n[13-07-05 10:11:07] "
  }
}
```


##### scheduleAdd - 添加调度配置

请求参数：

    serverid    (必须)服务器ID
    jobname     (必须)进程名称
    enable      (必须)是否启用该配置，可选值：0|1
    condition   (必须)调度条件，条件字段请参考附录

返回数据：

    scheduleid  进程调度配置ID

示例请求：

    {API_GATEWAY}/?c=backend&a=scheduleAdd&i=1&serverid=1&jobname=test&enable=1&condition[U]=300


示例返回：

```
{
  "code":0,
  "message":"schedule add successfully",
  "data":{
    "scheduleid":"4fe1e4a9-a6e9-4eed-8809-397fc1bfd042"
  }
}
```


##### scheduleUpdate - 更新调度配置

请求参数：

    serverid    (必须)服务器ID
    jobname     (必须)进程名称
    scheduleid  (必须)调度配置ID
    enable      (可选)是否启用该配置，可选值：0|1
    condition   (可选)调度条件，条件字段请参考附录

返回数据：

    无

示例请求：

    {API_GATEWAY}/?c=backend&a=scheduleUpdate&i=1&serverid=1&jobname=test&scheduleid=4fe1e4a9-a6e9-4eed-8809-397fc1bfd042&enable=0


示例返回：

```
{
  "code":0,
  "message":"schedule update successfully",
  "data":null
}
```


##### scheduleDelete - 删除调度配置

请求参数：

    serverid    (必须)服务器ID
    jobname     (必须)进程名称
    scheduleid  (必须)调度配置ID

返回数据：

    无

示例请求：

    {API_GATEWAY}/?c=backend&a=scheduleDelete&i=1&serverid=1&jobname=test&scheduleid=4fe1e4a9-a6e9-4eed-8809-397fc1bfd042


示例返回：

```
{
  "code":0,
  "message":"schedule delete successfully",
  "data":null
}
```


##### scheduleGet - 获取调度配置

请求参数：

    serverid    (必须)服务器ID
    jobname     (必须)进程名称
    scheduleid  (必须)调度配置ID

返回数据：

    schedule    调度配置信息

示例请求：

    {API_GATEWAY}/?c=backend&a=scheduleGet&i=1&serverid=1&jobname=test&scheduleid=4fe1e4a9-a6e9-4eed-8809-397fc1bfd042


示例返回：

```
{
  "code":0,
  "message":"get schedule info successfully",
  "data":{
    "schedule":{
      "enable":true,
      "condition":{
        "U":300
      }
    }
  }
}
```


##### scheduleGetAll - 获取所有调度配置信息

请求参数：

    serverid    (必须)服务器ID

返回数据：

    schedules   所有调度配置信息

示例请求：

    {API_GATEWAY}/?c=backend&a=scheduleGetAll&i=1&serverid=1


示例返回：

```
{
  "code":0,
  "message":"get all schedule info successfully",
  "data":{
    "schedules":{
      "test":{
        "4fe1e4a9-a6e9-4eed-8809-397fc1bfd042":{
          "enable":true,
          "condition":{
            "U":300
          }
        }
      }
    }
  }
}
```


##### scheduleGetLog - 获取调度执行历史

请求参数：

    serverid    (必须)服务器ID
    jobname     (必须)进程名称
    scheduleid  (必须)调度配置ID

返回数据：

    log         调度执行历史

示例请求：

    {API_GATEWAY}/?c=backend&a=scheduleGetLog&i=1&serverid=1&jobname=test&scheduleid=4fe1e4a9-a6e9-4eed-8809-397fc1bfd042


示例返回：

```
{
  "code":0,
  "message":"get schedule log successfully",
  "data":{
    "log":[
      "2013-07-05 13:58:16",
      "2013-07-05 13:53:16",
      "2013-07-05 13:48:16",
      "2013-07-05 13:43:16",
      "2013-07-05 13:38:16",
      "2013-07-05 13:33:16",
      "2013-07-05 13:28:16",
      "2013-07-05 13:23:16",
      "2013-07-05 13:18:16",
      "2013-07-05 13:13:16"
    ]
  }
}
```


#### monitor - 外部监控接口


请求参数：

    serverid    (必须)服务器ID
    jobnames    (必须)进程名称，多个进程以逗号分隔

示例请求：

    {API_GATEWAY}/?c=monitor&serverid=1&jobnames=test1,test2,test3

以上示例表示同时监控 ID 为 1 的服务器上 test1,test2,test3 三个进程的状态：
* 如果三个进程状态均为 UP，则 HTTP 返回码为 200
* 如果有一个进程状态不为 DOWN，则 HTTP 返回码为 500

监控页面中同时会显示各个被监控的进程的状态列表。



## 附录

##### 权限列表

```
 - 核心类
   - ADD                         添加进程
   - DELETE                      删除进程
   - UPDATE                      更新进程
   - GET                         查看进程
   - GETALL                      查看所有进程
   - START                       开启进程
   - STOP                        结束进程
   - RESTART                     重启进程
   - STATUS                      查询后台进程状态
   - STATUSALL                   查询所有后台进程状态
   - READ                        读取进程输出缓冲
   - MEM                         查询进程的内存使用量
   - MEMALL                      查询所有进程的内存使用量
   - SERVERMEM                   查询服务器的内存使用量
   - SERVERREAD                  读取服务器的输出

 - 授权管理类
   - AUTH.GETENABLE              获取授权启用状态
   - AUTH.SETENABLE              设置授权启用状态
   - AUTH.ADD                    添加用户
   - AUTH.DELETE                 删除用户
   - AUTH.UPDATE                 更新用户信息
   - AUTH.GET                    查询用户信息
   - AUTH.GETALL                 查询所有用户信息

 - 日志浏览类
   - LOGEXPLORER.LISTDIR         查询日志目录列表
   - LOGEXPLORER.LISTFILE        查询日志文件列表
   - LOGEXPLORER.GET             读取日志文件内容
   - LOGEXPLORER.SERVERLISTDIR   查询服务器日志目录列表
   - LOGEXPLORER.SERVERLISTFILE  查询服务器日志文件列表
   - LOGEXPLORER.SERVERGET       读取服务器日志文件内容

 - 进程调度类
   - SCHEDULER.ADD               添加新的进程调度配置
   - SCHEDULER.DELETE            删除进程调度配置
   - SCHEDULER.UPDATE            更新进程调度配置
   - SCHEDULER.GET               查询进程调度配置信息
   - SCHEDULER.GETALL            查询所有的进程调度配置信息
   - SCHEDULER.GETLOG            查询进程调度执行历史
```

##### 进程调度条件

以下是进程调度条件中的 condition 参数可以使用的字段组合规则：

```
* Y + m + d + H + i + U
  年 + 月 + 日 + 时 + 分 + 每间隔
  如：
  - 2013-10-01 当天，每5分钟执行1次
    condition[Y]=2013&condition[m]=10&condition[d]=1&condition[U]=300

* Y + W + N + H + i + U
  年 + 周 + 周几 + 时 + 分 + 每间隔
  如：
  - 每周5晚上8点执行1次
    condition[N]=5&condition[H]=20

* Y + z + H + i + U
  年 + 日 + 时 + 分 + 每间隔
  如：
  - 每年第180天执行1次
    condition[z]=179
```

各个时间条件字段的说明及取值范围：

```
日：
d - 月份中的第几天，取值：01-31，或 @t（月份中的最后一天）
N - 星期中的第几天，取值：1-7
z - 年份中的第几天，取值：0-365

星期：
W - 年份中的第几周，取值：1-53

月：
m - 月份，取值：01-12

年：
Y - 年份，取值：2013-2099

时间：
H - 小时，取值：00-23
i - 分钟，取值：00-59

每间隔：
U - 时间戳，取值：60的倍数，并小于86400*365，表示时间间隔（从服务器启动开始，每间隔多少秒执行一次）
```