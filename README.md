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
- i     json缩进开关，当设置为true时，将会对返回值进程格式化输出（方便调试查看）
- f     数据格式，可选值：json|jsonp，默认为json
- cb    jsonp回调函数名，仅当 f 为 jsonp 时有效
- l     输出的消息语言，默认为英文，可选值为：zh_CN
```

示例：

    {API_GATEWAY}/?c=user&a=login&i=true&f=jsonp&cb=callback&username=admin&password=admin

#### 返回格式

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
- user            用户类接口
  - login         登录
  - logout        注销
  - update        更改用户名/密码

- server          服务器类接口
  - add           添加
  - update        更新
  - delete        删除
  - list          查询服务器列表
  - get           获取服务器信息

- backend         后台进程服务器控制类接口
  - add           添加进程
  - update        更新进程
  - delete        删除进程
  - get           获取进程信息
  - getAll        获取所有进程信息
  - start         启动进程
  - stop          停止进程
  - restart       重启进程
  - status        获取进程状态
  - statusAll     获取所有进程状态
  - read          读取进程输出缓冲
  - mem           查询进程内存使用量
  - memAll        查询所有进程的内存使用量
  - serverMem     查询进程服务器的内存使用量
  - serverRead    读取进程服务器的输出缓冲
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

    {API_GATEWAY}/?c=user&a=login&i=true&username=admin&password=admin


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

    {API_GATEWAY}/?c=user&a=logout&i=true


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


##### update - 更改用户名/密码

请求参数：

    username    (选填)用户名
    password    (选填)密码

返回数据：

    username    原用户名

示例请求：

    {API_GATEWAY}/?c=user&a=update&i=true&password=quyun

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
    username    (可选)服务器用户名
    password    (可选)服务器密码

返回数据：

    serverid    新添加的服务器ID

示例请求：

    {API_GATEWAY}/?c=server&a=add&i=true&servername=localhost&serverip=127.0.0.1&serverport=13123


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
    username    (可选)服务器用户名
    password    (可选)服务器密码

返回数据：

    serverid    服务器ID

示例请求：

    {API_GATEWAY}/?c=server&a=update&i=true&serverid=1&serverport=13123


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

    {API_GATEWAY}/?c=server&a=delete&i=true&serverid=1


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

    {API_GATEWAY}/?c=server&a=list&i=true


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

    {API_GATEWAY}/?c=server&a=get&i=true&serverid=1


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

    {API_GATEWAY}/?c=backend&a=add&i=true&serverid=1&jobname=test&command=/var/www/test.php


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

    {API_GATEWAY}/?c=backend&a=update&i=true&serverid=1&jobname=test&autostart=0


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

    {API_GATEWAY}/?c=backend&a=delete&i=true&serverid=1&jobname=test


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

    {API_GATEWAY}/?c=backend&a=get&i=true&serverid=1&jobname=test


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

    {API_GATEWAY}/?c=backend&a=getAll&i=true&serverid=1


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

    {API_GATEWAY}/?c=backend&a=start&i=true&serverid=1&jobname=test


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

    {API_GATEWAY}/?c=backend&a=stop&i=true&serverid=1&jobname=test


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

    {API_GATEWAY}/?c=backend&a=restart&i=true&serverid=1&jobname=test


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

    {API_GATEWAY}/?c=backend&a=status&i=true&serverid=1&jobname=test


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

    {API_GATEWAY}/?c=backend&a=statusAll&i=true&serverid=1


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

    {API_GATEWAY}/?c=backend&a=read&i=true&serverid=1&jobname=test


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

    {API_GATEWAY}/?c=backend&a=mem&i=true&serverid=1&jobname=test


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

    {API_GATEWAY}/?c=backend&a=memAll&i=true&serverid=1


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

    {API_GATEWAY}/?c=backend&a=serverMem&i=true&serverid=1


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

    {API_GATEWAY}/?c=backend&a=serverRead&i=true&serverid=1


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

    {API_GATEWAY}/?c=backend&a=authGetEnable&i=true&serverid=1


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

    {API_GATEWAY}/?c=backend&a=authSetEnable&i=true&serverid=1&enable=1


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
    privileges  (必须)权限，用逗号分隔，*表示所有权限

返回数据：

    无

示例请求：

    {API_GATEWAY}/?c=backend&a=authAdd&i=true&serverid=1&username=beadmin&password=quyun&privileges=*


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
    privileges  (可选)权限，用逗号分隔，*表示所有权限

返回数据：

    无

示例请求：

    {API_GATEWAY}/?c=backend&a=authUpdate&i=true&serverid=1&username=beadmin&password=quyun


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

    {API_GATEWAY}/?c=backend&a=authDelete&i=true&serverid=1&username=beadmin


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

    {API_GATEWAY}/?c=backend&a=authGet&i=true&serverid=1&username=beadmin


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

    {API_GATEWAY}/?c=backend&a=authGet&i=true&serverid=1


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