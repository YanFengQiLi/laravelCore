<?php
    /**
     *  在 class5 管道实现之前, 先实现中间件
     *  理解: 就是都需要经过一些步骤，不去修改自己的代码，以此来扩展或者处理一些功能
     *
     *  代码解释 :
     *  第一步执行 call_middware 函数 会执行 SetCookie::handle。
     *   当执行 SetCookie::handle 的时候会发现要先执行 $next (); 再 echo ' 设置 cookie 信息！';
     *   所以就先执行了 VerfiyAuth::handle，这时候会先执行 echo ' 验证是否登录 '; 然后执行 $next ();
     *   执行 VerfiyCsrfToekn::handle 这时候会先执行 echo ' 验证 csrf Token '; 然后执行 $next ();
     *   执行 $handle ();
     *   最后 在 echo ' 设置 cookie 信息！'。
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
            //  先执行匿名函数, 将请求传递给下一个匿名函数, 即代码中 VerfiyAuth::handle()
            $next();
            echo '设置cookie信息！';
        }
    }

    function call_middware() {
        SetCookie::handle(function (){
            VerfiyAuth::handle(function() {
                $handle = function() {
                    echo '当前要执行的程序!<br />';
                };
                VerfiyCsrfToekn::handle($handle);
            });
        });
    }


    /**
     *   调用 call_middware() 函数打印结果如下 :
     *    验证是否登录
     *    验证csrf Token
     *    当前要执行的程序!
     *    设置cookie信息！
     */
    call_middware();
    exit;