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
    /**
     * Зарегистрированные токены
     * @var array 
     */
    public static $registredTokens = [];

    /**
     * Ответы
     * @var array
     */
    public static $responses = [];


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
    public function commandChat(ConnectionInterface $client, $msg): void
    {
        $request = json_decode($msg, true);
var_dump('chat_rabbit. token: '.$request['token']);
var_dump(array_keys(self::$registredTokens));
        $token = $request['token'];
        if (array_key_exists($token, self::$registredTokens)) {
            // если токен зарегистрирован, отдадим клиенту данные
            var_dump('send_data_to_client');
            self::$registredTokens[$token]->send( json_encode([
                'type' => 'chat',
                'token' => $token,
                'message' => $request['message']
            ]));

            unset(self::$registredTokens[$token]);
        } else {
            // если токен еще не зарегистрирован, сохраним ответ во временное хранилище
            self::$responses[$token] = $request['message'];
        }
    }


    /**
     * Регистрация токена
     *
     * @param ConnectionInterface $client
     * @param string $msg
     */
    public function commandRegister(ConnectionInterface $client, string $msg): void
    {
        $request = json_decode($msg, true);
var_dump('register_client. token: '. $request['token'] );
        $token = $request['token'];

        if (array_key_exists($token, self::$responses)) {
            // для этого токена уже готов ответ, отдадим его
            $client->send( json_encode([
                'type' => 'chat',
                'token' => $token,
                'message' => self::$responses[$token]
            ]));

            unset(self::$responses[$token]);
        } else {
            // если ответ для токена не получен, будем ждать
            self::$registredTokens[$token] = $client;
        }
    }
}
