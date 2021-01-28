<?php
    /**
     * 契约就是所谓的面向接口编程
     *
     * 为了增加程序的健壮性 和 容易扩展性
     *
     * 从以下例子我们不难看出, 我们想采用任何一种日志的写入的方式, 只要我们去实现 Log 的接口就可以了, 比如我们日志要改为 monolog 或者 redis 去存储,
     * 此时我们只需要新定义一个类, 去实现 Log 的接口, 且将依赖注入改为对象的 日志存储 方式即可
     *
     * 在 Laravel 中契约
     *   比如 Cache，定义的契约规范在 Illuminate\Contracts\Cache\Repository 文件中。
     *   我们可以写多种缓存方式如 file,redis,memcached 实现这个契约，编写契约中的 set,get,remove 之类的方法
     *   在使用上，跟上面的例子一样，构造函数或者方法只需要传入对应的契约接口，使用的时候可以随意更换 file,redis,memcached
     */

	// 定义日志的接口规范
	interface log
	{
	    public function write();   
	}

	// 文件记录日志
	class FileLog implements Log
	{
	    public function write(){
	        echo 'file log write...';
	    }   
	}

	// 数据库记录日志
	class DatabaseLog implements Log
	{
	    public function write(){
	        echo 'database log write...';
	    }   
	}

	class User 
	{
	    protected $log;

	    public function __construct(Log $log)
	    {
	        $this->log = $log;   
	    }

	    public function login()
	    {
	        // 登录成功，记录登录日志
	        echo 'login success...';
	        $this->log->write();
	    }

	}

	$user = new User(new DatabaseLog());
	$user->login();