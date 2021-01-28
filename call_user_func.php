<?php

    //  3. 命名空间的使用
    namespace Foo;
    class Super
    {
        public static function index()
        {
            return '我是'.__NAMESPACE__.'命名空间下的方法';
        }
    }
    echo call_user_func(__NAMESPACE__."\\".'Super::index');

    //  1. 调用普通函数
    function test(...$parameter) {
        $number = 0;

        if (is_array($parameter)) {
            foreach ($parameter as $number) {
                $number += $number;
            }

            return $number;
        }

        return $parameter;
    }

    $data = call_user_func('Foo\test', [1,2,3]);

    print_r($data.'<br />');


    //  2. 调用本类中的方法 有三种方法
    class Person
    {
        public static function show($name)
        {
            return '我是' . $name . '打印的';
        }
    }
    //  1. [类名, 方法名]
    echo call_user_func(['Foo\Person', 'show'], '[类名, 方法名]') . "<br />";
    //  2. 类::静态方法
    echo call_user_func('Foo\Person::show', '类::静态方法'). "<br />";
    //  3. 实例化对象传入
    $obj = new Person();
    echo call_user_func([$obj, 'show'], '实例化对象传入');





