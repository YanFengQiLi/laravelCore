<?php

    /**
     *  控制反转 依赖注入 反射的理解
     *
     *  reflect 反射类 https://www.php.net/manual/zh/class.reflectionclass.php
     */

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
        public function __construct(FileLog $log)
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

    //  反射通俗的来讲,就是可以根据类名,拿到任何我们所需要的类的信息, 比如类中的方法/参数/变量等等
    function make($concrete){
        //  获取类的有关信息    ReflectionClass Object ( [name] => User ) ReflectionClass Object ( [name] => FileLog )
        $reflector = new ReflectionClass($concrete);
        //  获取类的构造函数    ReflectionMethod Object ( [name] => __construct [class] => User )
        $constructor = $reflector->getConstructor();
        // 如果没有构造函数，则直接创建对象
        if(is_null($constructor)) {
            return $reflector->newInstance();
        }else {
            // 构造函数依赖的参数    Array ( [0] => ReflectionParameter Object ( [name] => log ) )
            $dependencies = $constructor->getParameters();
            // 递归组装 当前创建实例所需要的参数列表
            $instances = getDependencies($dependencies);
            // 依据参数,创建新的实例     newInstanceArgs 从给出的参数创建一个新的类实例
            return $reflector->newInstanceArgs($instances);
        }
    }

    //  递归获取, 传入类的参数列表
    function getDependencies($paramters) {
        $dependencies = [];
        foreach ($paramters as $paramter) {
            // ReflectionParameter 的 getClass() 方法, 返回的是一个 ReflectionClass 对象   即 ReflectionClass Object ( [name] => FileLog )
            $dependencies[] = make($paramter->getClass()->name);
        }
        return $dependencies;
    }

    $user = make('User');
    $user->login();
    exit;