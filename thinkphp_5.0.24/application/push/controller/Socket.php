<?php

namespace app\push\controller;
use think\Session;
use Workerman\Lib\Timer;

class Socket extends Server
{
    protected $socket = 'websocket://0.0.0.0:9090';
    protected $global_uid = 0;

    public function onWorkerStart(){

//        $time_interval = 2.5;
//        Timer::add($time_interval,
//            function()
//            {
//                echo "22222\n";
//            }
//        );
//
//        Timer::add($time_interval, function(){
//
//                $new_message = array(
//                    'type' => rand(1,100),
//                    'head_img_url'=>'/Public/main/img/kefu.jpg',
//                    'from_client_name' => 'GM管理员',
//                    'content'=>'期号:22222222222',
//                    'time'=>date('H:i:s')
//                );
//                foreach ($this->worker->connections as $conn) {
//                    $conn->send(json_encode($new_message));
//                }
//        });
//
//        //ping 统计人数
//        Timer::add($time_interval, function(){
//            //ping客户端(获取房间内所有用户列表 )
//            $clients_list = $this->worker->connections;
//            $num = count($clients_list);
//
//            $new_message = array(
//                'type' => 'ping',
//                'content' => $num,
//                'time' => date('H:i:s')
//            );
//            //if($num!=F('online')){
//            //F('online',$num);
//            foreach ($this->worker->connections as $conn) {
//                $conn->send(json_encode($new_message));
//            }
//            //}
//        });
    }
    /*
     * 客户端连接时
     *
     * */
    public function onConnect($connection){
//        global $worker,$global_uid;

//        $connection->uid = ++$global_uid;
//        $connection->onWebSocketConnect = function($connection , $http_header)
//        {
//
//        };
    }
    /*
     * 客户端断开时
     *
     * */
    public function onClose($connection){

    }
    /**
     * onMessage
     * @access public
     * 转发客户端消息
     * @param  void
     * @param  void
     * @return void
     */

    public function onMessage($connection, $data) {
        global $worker,$global_uid;
        // 客户端传递的是json数据
        $message_data = json_decode($data, true);
        if (!$message_data) {
            return;
        }

        // 1:表示执行登陆操作 2:表示执行说话操作 3:表示执行退出操作
        // 根据类型执行不同的业务
        switch($message_data['type']){
            // 客户端回应服务端的心跳
            case 'pong' :
                break;
            case 'login' :
                // 把昵称放到session中
                $client_name = htmlspecialchars($message_data['client_name']);
                $connection->uid = ++$global_uid;
                //邀请朋友加入  和   随机匹配加入
                if ($message_data['login_type'] == 1){
                    $connection->room = $message_data['room'];
                }else{
                    $room = 1;
                    if ($connection->uid % 6 == 0){
                        $room += 1;
                    }
                    $connection->room = $room;
                }
//                print_r($connection);
                $this->worker->uidConnections[$connection->uid] = $connection;
                $this->worker->uidConnections[$connection->room] = $connection;
                $return = [];
                $return['uid'] = $connection->uid;
                $return['room'] = $connection->room;
                $return['type'] = $message_data['type'];
                $return['nickname'] = $client_name;
                $return['content'] = '欢迎进入海洋大亨聊天室';
                $connection->send(json_encode($return));
                break;
            case 'say' :
//                $userid = $connection->uid;
//                $client_name = htmlspecialchars($message_data['client_name']);
//                $connection->send('用户'.$userid.'说:'.$message_data['content']);
                $message_data['uid'] = $connection->uid;
                foreach ($this->worker->connections as $conn) {
//                    if ($conn->room == $message_data['room']){                //组队聊天时将这行注释删掉
                        $conn->send(json_encode($message_data));
//                    }
                }
                break;

            case 'robot':

                break;
        }
    }

}

