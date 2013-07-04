php-backend-api
==================

基于 [php-backend-server](https://github.com/quyun/php-backend-server) 封装的 WEB API，可以通过 HTTP/HTTPS 协议来调用。

目前已用于后台进程管理控制中心 [php-backend-web](https://github.com/quyun/php-backend-web)。


## 环境依赖

- PHP 5.3+
- PHP SQLite 扩展

请确保 app/data/ 目录可写入。


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
```

## 接口规范

#### 接口地址

php-backend-api 部署的 URL 地址，下文中以 {API_GATEWAY} 代替。

#### 公用参数

```
- c：模块名，如：user
- a：操作名，如：login
- i：json缩进开关，当设置为true时，将会对返回值进程格式化输出（方便调试查看）
- f：数据格式，可选值：json|jsonp，默认为json
- cb：jsonp回调函数名，仅当 f 为 jsonp 时有效
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
  "message":"失败提示信息",
  "data":{
    // 返回数据
  }
}
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
