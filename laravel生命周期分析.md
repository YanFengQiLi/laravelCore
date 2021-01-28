## 入口文件
laravel 应用的所有请求入口都是 public/index.php 文件, 如下所示
```
//  定义了laravel一个请求的开始时间
define('LARAVEL_START', microtime(true));

//  引入 composer 提供的自动加载机制
require __DIR__.'/../vendor/autoload.php';

//  引入 Ioc 容器, 查看 laravel 文档我们不难发现 $app 就是一个全局对象,同 app() 助手函数
$app = require_once __DIR__.'/../bootstrap/app.php';


/*
*  打开 /bootstrap/app.php 你会发现这段代码，绑定了Illuminate\Contracts\Http\Kernel::class，
*  singleton 你可以理解成 class2.php 中实现的 Ioc 容器的 $ioc->bind() 方法 
*  打开 App\Http\Kernel 发现这就走到了 laravel 的中间件部分 
*  此时,我们在看一下 App\Http\Kernel 继承的父类 Illuminate\Foundation\Http\Kernel 发现它实现了我们在这里绑定的接口 
*  这个父类方法中,我们发现有个 $bootstrappers 这个属性, 这里就是 laravel 里启动要做的事情
/
// $app->singleton(
//    Illuminate\Contracts\Http\Kernel::class, //  绑定接口 Kernel 
//    App\Http\Kernel::class    //  绑定 Kernel 的真正的具体实现(Illuminate\Foundation\Http\Kernel)
// );


// 这个相当于我们创建了Kernel::class的服务提供者
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

//  获取一个 Request ，返回一个 Response。以把该内核想象作一个代表整个应用的大黑盒子，输入 HTTP 请求，返回 HTTP 响应
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

//  把我们服务器的结果返回给浏览器
$response->send();

//  执行比较耗时的请求
$kernel->terminate($request, $response);
```