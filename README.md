### 为什么用
* 不过度设计，简单粗暴好用。学习像数学之美里提到过的 Google 的杰出工程师阿米特.辛格博士那样的前辈们的编码。
* 提供最简单基础的 MVC 框架，将性能损耗降到最低
* 按 Module 进行资源分离，以便对业务进行微服务化隔离和后期的服务便捷迁移
* 提供简单好用的单元测试框架
* 提供便捷的接口参数合法性验证服务
* 提供简单好用的 Mysql 控制服务
* 提供PSR-3规范的日志类，额外提供 log buffer 功能（性能提升） 和 全局日志标记码（一个进程一个标记码，方便定位问题）的功能
* 所有开发基于 PHP7 环境，未做低版本运行验证和兼容

### 文件目录
```
|- Framework	框架
	|- config	框架全局配置
	|- Libraries	框架类库
	|- Entities	数据实体类
	|- Models	框架逻辑
	|- Tests	单元测试
|- public  框架公开访问位置
	|- index.php 	入口文件
	|- cli.php 	命令行入口
	|- run_test.php 	单元测试入口
|- Demo	样例应用
	|- Cache	缓存管理文件
	|- config	应用配置文件（mysql、redis等资源配置；api接口配置等;不能变动）
	|- Controllers	控制器（不能变动）
	|- Models	业务逻辑类 
	|- Data 	数据访问类
	|- Entities 业务实体类
	|- Views	视图文件（不能变动）
	|- Plugins	插件文件
	|- Libraries	类库文件
	|- Tests	单元测试文件（不能变动）
	|- Bootstrap.php 	应用启动初始化文件（不能变动,可以没有）
```

### 开始使用

#### MVC框架
1. 配置 Web Server 服务器重定向到入口文件。Nginx 样例如下：
```
if ( !-f $request_filename ) { 
	#这里 /index.php/$1 路径要不要带 public 主要依赖配置的 root 路径是什么
    rewrite "^/(.*)" /index.php/$1 last;
}
#下边是控制静态资源访问路径，可以不要
location ~* .(css|js|img)$ {
    root /data1/www/htdocs/service.movie.weibo.com/public/;
    if (-f $request_filename) {
        expires off;
        break;
    }   
}
```
1. 路由解析规则：域名后第一个用'/'分离的部分为 module 名，最后一部分为 action 名，中间部分解析为 controller。如:
```
http://service.movie.weibo.com:8183/demo/demo/a/index?a=test&b=12  
Module： Demo  
Controller： Demo\Controllers\Demo\A  
Action: index  
参数: a 和 b
```
1. 每个 module 可以有自己的 Bootstrap.php 在自己的根目录里，在框架初始化时会顺序执行'_init'开头的成员方法。
1. 每个 module 有自己的路由插件在 Plugins 文件夹内，可以在 Bootstrap 类中调用 Dispatcher 类的 registerPlugin 方法进行插件注册。
插件包含routerStartup、routerShutdown、dispatchStartup、dispatchShutdown、preResponse几个部分。分别为:
```
routerStartup ： 路由规则解析前  
routerShutdown ：路由规则解析后  
dispatchStartup ： 控制器分发前  
dispatchShutdown ： 控制器分发后  
preResponse ： 页面渲染结果输出前
```  
1. 每个 Controller 类必须继承 Framework\Models\Controller 。Controller 中的 Action 后缀类成员方法为可以调用的接口。  
每个接口可以定义一个对应的参数合法性检验的静态变量，静态变量名的对应规则为： "全大写的接口名_PARAM_RULES"。如 'indexAction' 的参数定义如下:
```
 $INDEX_PARAM_RULES = array(
        'a' => 'requirement', //必须有a参数
        'b' => 'number&max:15&min:10', //b参数如果存在则必须为数字且范围在10-15之间
        'c' => 'timestamp', // c参数若存在则必须为合法时间戳
        'd' => 'enum:a,1,3,5,b,12345' //d参数若存在则必须为枚举项
    );//具体参见 Framework\Libraries\Validator 类的定义
```
1. 每个 Action 若返回结果不为 False ，则会加载相应的 View 视图，视图可以混写 PHP 代码。  
在Action内可以调用 $this->assign() 方法注册渲染变量。如：$this->assign(array('text' => 'Hello,world!'));  
相应的视图加载规则： Controller名\Action名.phtml。如:
```
http://service.movie.weibo.com:8183/demo/demo/a/index?a=test&b=12 
Controller 文件路径： MODULE_ROOT\Controllers\Demo\A.php
View 文件路径：MODULE_ROOT\Views\Demo\A\index.phtml
```

#### 如何方便迁移 Module
1. 在框架层做了 Module 之间的资源隔离，不同 Module 之间无法通过 new 关键字来进行数据交换；
1. 框架提供了 LocalCurl 类，可以模拟 HTTP 网络调用，其实是在内存中完成了不同 Module 之间的数据交互；
1. 迁移的时候，执行 全局替换 LocalCurl 为 Curl 即可完成框架部分的迁移，当然业务里域名修改的地方还需要业务技术另行修改；

#### 如何进行自动化测试（单元测试）
1. 在各自 Module 下的 Tests 文件夹内创建单元测试文件，需要继承框架 Framework\Libraries\TestSuite 类；
1. 命令行下执行 php public/run_test.php 即可完成全部单元测试文件的执行。也可指定要执行的单元测试文件或 Module。如： php public/run_test.php Framework TestPDOManager.php

#### 