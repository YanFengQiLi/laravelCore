<?php
    /**
     *  观察者模式 (Observer), 当一个对象的状态发生改变时，依赖他的对象会全部收到通知，并自动更新。
     *   场景：一个事件发生后，要执行一连串更新操作。传统的编程方式，就是在事件的代码之后直接加入处理逻辑，
     *   当更新的逻辑增多之后，代码会变得难以维护。这种方式是耦合的，侵入式的，增加新的逻辑需要改变事件主题的代码
     *   观察者模式实现了低耦合，非侵入式的通知与更新机制
     */


	/**
	 * 观察者接口类
	 * Interface ObServer
	 */
	interface ObServer
	{
	    public function update($event_info = null);
	}

	/**
	 * 观察者1
	 */
	class ObServer1 implements ObServer
	{
	    public function update($event_info = null)
	    {
	        echo "观察者1 收到执行通知 执行完毕！\n";
	    }

	    public function insert()
        {
            echo '观察者1,插入了';
        }

        public function del()
        {
            echo '观察者1,被删除了';
        }
	}

	/**
	 * 观察者2
	 */
	class ObServer2 implements ObServer
	{
	    public function update($event_info = null)
	    {
	        echo "观察者2 收到执行通知 执行完毕！\n";
	    }

        public function insert()
        {
            echo '观察者2,插入了';
        }

        public function del()
        {
            echo '观察者1,被删除了';
        }
	}



	/**
	 * 事件
	 * Class Event
	 */
	class Event
	{

	    protected $ObServers;

	    //增加观察者
//	    public function add(ObServer $ObServer)
//	    {
//	        $this->ObServers[] = $ObServer;
//	    }

        public function add($array)
        {
            $this->ObServers = $array;
        }

	    //事件通知
	    public function notify()
	    {
	        foreach ($this->ObServers as $func => $ObServer) {
	            $ObServer->$func();
	        }
	    }

	    /**
	     * 触发事件
	     */
	    public function trigger()
	    {
	        //通知观察者
	        $this->notify();
	    }
	}


	//  创建一个事件
	$event = new Event();
	$event->add([
	    'insert' => new ObServer1(),
        'del' => new ObServer2()
    ]);
	//  执行事件对应的方法
	$event->trigger();