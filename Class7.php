<?php

    /**
     *  使用 Trait 产生单例
     *  业务场景: 如我们定义类的时候，很多都要做成单例模式, 则可以使用这种方式
     *
     *  Trait 类的产生, 解决了 PHP 无法多继承的问题
     */

    trait Singleton {
        protected static $_instance;

        final public static function getInstance() {
            if(!isset(self::$_instance)) {
                self::$_instance = new static();
            }

            return self::$_instance;
        }

        private function __construct() {
            $this->init();
        }

        protected function init() {}

    }


    class Db {
        use Singleton;
        protected function init() {
        }
    }

    //  实例化 Db 类
    $db = Db::getInstance();

    phpinfo();
