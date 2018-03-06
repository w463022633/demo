<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/1/9
 * Time: 下午1:04
 */

namespace EasySwoole;

use App\Process\Inotify;
use App\Process\Test;
use App\Sock\Parser\WebSock;
use \EasySwoole\Core\AbstractInterface\EventInterface;
use EasySwoole\Core\Component\Logger;
use EasySwoole\Core\Swoole\Process\ProcessManager;
use \EasySwoole\Core\Swoole\ServerManager;
use \EasySwoole\Core\Swoole\EventRegister;
use \EasySwoole\Core\Http\Request;
use \EasySwoole\Core\Http\Response;

Class EasySwooleEvent implements EventInterface {

    public function frameInitialize(): void
    {
        // TODO: Implement frameInitialize() method.
        date_default_timezone_set('Asia/Shanghai');
    }

    public function mainServerCreate(ServerManager $server,EventRegister $register): void
    {
        // TODO: Implement mainServerCreate() method.
        //注册worker start 事件
        $register->add($register::onWorkerStart,function (\swoole_server $server,$workerId){
            //为workerId为0的进程添加定时器
            if($workerId == 0){
               Core\Swoole\Time\Timer::loop(2000,function (){
                   Logger::getInstance()->console('timer run');
                   //给自定义进程发送数据
                   ProcessManager::getInstance()->writeByProcessName('test',time());
               });
            }
        });

        ProcessManager::getInstance()->addProcess('test',Test::class);

        ProcessManager::getInstance()->addProcess('autoReload',Inotify::class);

        $register->registerDefaultOnMessage(new WebSock());
    }

    public function onRequest(Request $request,Response $response): void
    {
        // TODO: Implement onRequest() method.
    }

    public function afterAction(Request $request,Response $response): void
    {
        // TODO: Implement afterAction() method.
    }
}