<?php

    /**
     *  Facades 外观模式的实现与理解
     *
     *  利用 Facades 实现什么 ?
     *      在 class2 中实现了, $user = $ioc->make('user'), $user->login(), 现在我们要以 User::login() 这种方式去实现
     *
     *  Facade 工作原理:
     *   Facade 核心实现原理就是在 UserFacade 提前注入 Ioc 容器。
     *   定义一个服务提供者的外观类，在该类定义一个类的变量，跟 ioc 容器绑定的 key 一样，
     *   通过静态魔术方法 __callStatic 可以得到当前想要调用的 login
     *   使用 static::$ioc->make ('user');
     *
     *  重载:
     *      https://www.php.net/manual/zh/language.oop5.overloading.php
     *
     *
     *  PHP所提供的重载（overloading）是指动态地创建 类属性 和 方法。
     *     实现原理 : 魔术方法
     *     通俗的来讲: 当调用当前环境下未定义或不可见的类属性或方法时，重载方法会被调用
     *     重载分类 :
     *          1. 属性   __get()，__set()，__isset() 和 __unset() 进行属性重载
     *          2. 方法
     *              public __call ( string $name , array $arguments ) : mixed
     *              public static __callStatic ( string $name , array $arguments ) : mixed
     *
     */
    class Ioc
    {
        public $binding = [];
        public function bind($abstract, $concrete)
        {
            if (!$concrete instanceof Closure) {
                $concrete = function ($ioc) use ($concrete) {
                    return $ioc->build($concrete);
                };
            }
            $this->binding[$abstract]['concrete'] = $concrete;
        }
        public function make($abstract)
        {
            $concrete = $this->binding[$abstract]['concrete'];
            return $concrete($this);
        }
        public function build($concrete) {
            $reflector = new ReflectionClass($concrete);
            $constructor = $reflector->getConstructor();
            if(is_null($constructor)) {
                return $reflector->newInstance();
            }else {
                $dependencies = $constructor->getParameters();
                $instances = $this->getDependencies($dependencies);
                return $reflector->newInstanceArgs($instances);
            }
        }
        protected function getDependencies($paramters) {
            $dependencies = [];
            foreach ($paramters as $paramter) {
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
            echo 'file log write...<br />';
        }
    }
    // 数据库记录日志
    class DatabaseLog implements Log
    {
        public function write(){
            echo 'database log write...<br />';
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
            echo 'login success...<br />';
            $this->log->write();
        }

        public function getUserInfo($name, $sex, $age)
        {

            echo '我的名字叫:'. $name, ', 性别:' . $sex, ', 今年:' . $age . '岁';
        }
    }

    class UserFacade
    {
        //  外观模式核心,要在本类中定义一个 静态属性
        protected static $ioc;

        //  将Ioc容器对象赋值给静态属性
        public static function setFacadeIoc($ioc)
        {
            static::$ioc = $ioc;
        }

        //  获取外观访问器
        protected static function getFacadeAccessor()
        {
            return 'user';
        }

        //  在静态上下文中调用一个不可访问方法时，__callStatic(string $name , array $arguments) 会被调用
        //  $name 参数是要调用的方法名称   $arguments 参数是一个索引型数组，包含着要传递给方法 $name 的参数
        public static function __callStatic($method, $args)
        {
            //  通过 Ioc 容器,生成 User 的实例
            $instance = static::$ioc->make(static::getFacadeAccessor());
            //  ... 可变参数列表(它是 php 中的一个语法糖), 它将用户输入的参数转换为一个数组
            //  User 实例去调用 login 方法
            return $instance->$method(...$args);
        }

    }

    //实例化IoC容器

    $ioc = new Ioc();
    $ioc->bind('log','FileLog');
    $ioc->bind('user','User');

    UserFacade::setFacadeIoc($ioc);

    UserFacade::login();

    UserFacade::getUserInfo('李四', '男', 12);

    exit;