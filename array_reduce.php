<?php
    /**
     *  array_reduce() 将回调函数 callback 迭代地作用到 array 数组中的每一个单元中，从而将数组简化为单一的值
     *
     *  array_reduce ( array $array , callable $callback , mixed $initial = null ) : mixed
     *  - $callback(mixed $carry, mixed $item)
     *     $carry 携带上次迭代里的值； 如果本次迭代是第一次，那么这个值是 initial
     *     $item  携带了本次迭代的值。
     *  - $initial
     *      如果指定了可选参数 initial，该参数将在处理开始前使用，或者当处理结束，数组为空时的最后一个结果
     *
     *
     *  手册: https://www.php.net/manual/zh/function.array-reduce.php
     */

    function sum($arr, $item)
    {
        $arr += $item;

        return $arr;
    }

    function product($carry, $item)
    {
        $carry *= $item;

        return $carry;
    }

    $arr1 = [1,2,3,4,5];

    $arr2 = [];

    //  结果: 15
    echo array_reduce($arr1, 'sum')."<br />";

    //  结果: 1200  原因 : 10*1*2*3*4*5
    echo array_reduce($arr1, 'product',10)."<br />";