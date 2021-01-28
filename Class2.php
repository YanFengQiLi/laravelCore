<?php

    /**
     * Ioc 容器 和 服务提供者的实现
     * 基于 class1 , 我们还没有做到完全解耦, 因为 user 类的构造方法中已经绑定了, 日志的具体实现,当我们要更换日志的记录方式时就会非常麻烦,
     * 此时我们可以借助一个容器，提前把 log,user 都绑定到 Ioc 容器中。User 的创建交给这个容器去做。
     *
     * Ioc 容器实现思路:
     *      1.Ioc 容器维护 binding 数组记录 bind 方法传入的键值对如:log=>FileLog, user=>User
     *      2.在 ioc->make ('user') 的时候，通过反射拿到 User 的构造函数，拿到构造函数的参数，发现参数是 User 的构造函数参数 log, 然后根据 log 得到 FileLog。
     *      3.这时候我们只需要通过反射机制创建 $filelog = new FileLog ();
     *      4.通过 newInstanceArgs 然后再去创建 new User ($filelog);
     *
     *
     * laravel 中的服务提供者:
     *      可以在 config 目录找到 app.php 中 providers, 这个数组定义的都是已经写好的服务提供者
     *      随便打开一个类比如 CacheServiceProvider，这个服务提供者都是通过调用 register 方法注册到 ioc 容器中，
     *      其中的 app 就是 Ioc 容器。singleton可以理解成我们的上面例子中的bind方法。只不过这里singleton指的是单例模式
     */

    require_once './ReportError.php';

	class Ioc
	{
	    public $binding = [];

	    public function bind($abstract, $concrete)
	    {
	        // 这里为什么要返回一个closure呢 因为bind的时候还不需要创建User对象，所以采用 closure 等 make 的时候再创建FileLog;
	        if (!$concrete instanceof Closure) {
	            //  这里的 $ioc 就是当前类对象, 此处可以理解理解为: 我们定义了一个匿名函数 $obj = function ($arg) use ($var) {}, 只不过我们传递 $arg 参数是一个当前类的对象, 即 $concrete($this), 使用 use 引入父作用域变量 $var
                //  此处所使用的关键知识点就是, 匿名函数: 1. 会自动绑定 $this (当前类) 2. use 使用父作用域变量
	            $concrete = function ($ioc) use ($concrete) {
	                //  使用当前类对象, 调用 build 方法, 创建 User 对象
	                return $ioc->build($concrete);
	            };
	        }

	        $this->binding[$abstract]['concrete'] = $concrete;
	    }

	    public function make($abstract)
	    {
            // 根据key获取binding的值
	        $concrete = $this->binding[$abstract]['concrete'];
            //  调用bind() 方法中的 $concrete() 匿名函数,  利用 build() 生成 User 对象
	        return $concrete($this);
	    }

        // 创建对象
	    public function build($concrete) {
	        //  通过反射拿到 User 类
	        $reflector = new ReflectionClass($concrete);
	        //  拿到 User 构造函数的参数
	        $constructor = $reflector->getConstructor();
	        if(is_null($constructor)) {
	            return $reflector->newInstance();
	        }else {
	            //  获取 User 类构造函数的参数列表, 即 log
	            $dependencies = $constructor->getParameters();
	            //  根据 log 参数, 获取 FileLog
	            $instances = $this->getDependencies($dependencies);
                //  最后将实例化后的 FileLog 对象, 当做参数传入, 因为 $reflector 拿到是 User 类, 此时通过 newInstanceArgs() 生成的就是一个完整的 User 实例: new User(FileLog $log)
	            return $reflector->newInstanceArgs($instances);
	        }
	    }

        // 获取参数的依赖
	    protected function getDependencies($paramters) {
	        $dependencies = [];
	        foreach ($paramters as $paramter) {
	            //  创建 FileLog 实例 即 $this->make('log'), 此时根据 log 这个key, 找到与之绑定的值 FileLog, 此时通过匿名函数 $concrete($this)
                //  调用 build 方法, 通过反射类 (ReflectionClass), 获取到 FileLog 类, 此时 $reflector->getConstructor() = Null , 则直接使用 newInstance 生成 FileLog 类的实例
	            $dependencies[] = $this->make($paramter->getClass()->name);
	        }
	        return $dependencies;
	    }

	}


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

	//实例化IoC容器
	$ioc = new Ioc();
	$ioc->bind('log','FileLog');
	$ioc->bind('user','User');
	$user = $ioc->make('user');
	$user->login();
	exit;