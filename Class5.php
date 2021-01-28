<?php
    /**
     * 管道 与 面向切面编程 (可以理解为中间件)
     */


    interface Milldeware {
        public static function handle(Closure $next);
    }

    class VerfiyCsrfToekn implements Milldeware {

        public static function handle(Closure $next)
        {
            echo '验证csrf Token <br>';
            $next();
        }
    }

    class VerfiyAuth implements Milldeware {

        public static function handle(Closure $next)
        {
            echo '验证是否登录 <br>';
            $next();
        }
    }

    class SetCookie implements Milldeware {
        public static function handle(Closure $next)
        {
            $next();
            echo '设置cookie信息！';
        }
    }

    $handle = function() {
        echo '当前要执行的程序!';
    };


    $pipe_arr = [
        'VerfiyCsrfToekn',
        'VerfiyAuth',
        'SetCookie'
    ];

    /**
     *  array_reduce() 将回调函数 callback 迭代地作用到 array 数组中的每一个单元中，从而将数组简化为单一的值
     *
     *  array_reduce ( array $array , callable $callback , mixed $initial = null ) : mixed
     *  - $callback(mixed $carry, mixed $item)
     *     $carry 携带上次迭代里的值； 如果本次迭代是第一次，那么这个值是 initial
     *     $item  携带了本次迭代的值。
     *  - $initial
     *      如果指定了可选参数 initial，该参数将在处理开始前使用，或者当处理结束，数组为空时的最后一个结果
     */
    $callback = array_reduce($pipe_arr,function($stack,$pipe) {
        /**
         *  $pipe 就是 $pipe_arr 数组中每一项的 value
         *  $stack 迭代的执行顺序  1.$handle 2. VerfiyCsrfToekn->handle() 3.VerfiyAuth->handle()
         *  因为 array_reduce() 这个函数, 只要设置了第三个参数($initial), 那么在第一次迭代数组时,会先将 $initial 的值作用于回调函数, 即 $callback 的 $carry 的值就是 $handle
         */
        return function() use($stack,$pipe){
            return $pipe::handle($stack);
        };
    },$handle);


    //  call_user_func(callback $callback, $mixed $parameter)  callback 是被调用的回调函数，其余参数是回调函数的参数
    call_user_func($callback);