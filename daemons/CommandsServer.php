<?php
namespace vendor\larsnovikov\yii2multiresponse\daemons;

use consik\yii2websocket\events\WSClientEvent;
use consik\yii2websocket\WebSocketServer;
use Ratchet\ConnectionInterface;

/**
 * Class CommandsServer
 * @package vendor\larsnovikov\yii2multiresponse\daemons
 */
class CommandsServer extends WebSocketServer
{
    public static $onlineClients = [];

    public static $responses = [];


    public function init()
    {
        parent::init();

        $this->on(self::EVENT_CLIENT_CONNECTED, function(WSClientEvent $e) {

            var_dump('connected');
        });

        $this->on(self::EVENT_CLIENT_DISCONNECTED, function(WSClientEvent $e) {

            var_dump('disconnected');
        });
    }

    /**
     * @param ConnectionInterface $from
     * @param $msg
     * @return null|string
     */
    protected function getCommand(ConnectionInterface $from, $msg)
    {
        $request = json_decode($msg, true);
        return !empty($request['action']) ? $request['action'] : parent::getCommand($from, $msg);
    }

    /**
     * Получение данных от кролика
     * @param ConnectionInterface $client
     * @param $msg
     */
    public function commandChat(ConnectionInterface $client, $msg)
    {
        $request = json_decode($msg, true);
var_dump('chat_rabbit. userKey: '.$request['userKey']);
        $userKey = $request['userKey'];
        if (array_key_exists($userKey, self::$onlineClients)) {
            var_dump('send_data_to_client');
            self::$onlineClients[$userKey]->send( json_encode([
                'type' => 'chat',
                'token' => $request['token'],
                'message' => $request['message']
            ]));
        }
    }


    /**
     * Регистрация человека клиента
     *
     * @param ConnectionInterface $client
     * @param $msg
     */
    public function commandRegister(ConnectionInterface $client, $msg)
    {
        $request = json_decode($msg, true);
var_dump('register_client. userkey: '. $request['userKey'] );
        $userKey = $request['userKey'];

        // сохраним клиента человека по его токену
        self::$onlineClients[$userKey] = $client;
    }
}
