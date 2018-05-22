<?php
/**
 * Created by PhpStorm.
 * User: lars
 * Date: 16.05.18
 * Time: 21:11
 */

namespace vendor\larsnovikov\yii2multiresponse\widgets;

use vendor\larsnovikov\yii2multiresponse\assets\ContainerAsset;
use vendor\larsnovikov\yii2multiresponse\queues\BaseQueue;
use vendor\larsnovikov\yii2multiresponse\queues\Queue;
use WebSocket\Client;
use yii\base\Widget;
use yii\helpers\Html;

/**
 * Class AbstractWidget
 * @package vendor\larsnovikov\yii2multiresponse\widgets
 */
abstract class AbstractWidget extends Widget
{
    private static $userKey = null;
    
    public static $containers = [];

    public $data = [];

    public $view = 'empty_container';

    abstract public static function getQueueComponent();

    public function init()
    {
        parent::init();
    }

    public function registerAsset(): void
    {
        ContainerAsset::register($this->getView());
    }

    /**
     * @return string
     * @throws \yii\base\Exception
     */
    public function run(): string
    {
        $this->registerAsset();

        if (self::$userKey === null) {
          //  self::$userKey = \Yii::$app->security->generateRandomString();
            self::$userKey = '3456345';
        }

        // создадим токен доступа к контейнеру
        $token = \Yii::$app->security->generateRandomString();
        return $this->createEmptyContainer($token);
    }

    /**
     * @param string $token
     */
    public function sendToQueue(string $token): void
    {
        // положить в очередь данные для обработки
        Queue::putInQueue(static::class, [
            'view' => $this->view,
            'data' => $this->data,
            'token' => $token,
            'userKey' => self::$userKey
        ]);
    }

    /**
     * @param $token
     * @return string
     */
    public function createEmptyContainer(string $token): string
    {
        echo self::$userKey.' token '.$token.'--- test1:'.$this->data['test1'].' test2: '.$this->data['test2'];
        // положим в очередь данные этого виджета
        $this->sendToQueue($token);
        
        self::$containers[self::class] = $token;

        // создание заглушки
        return $this->render($this->view, array_merge($this->data, [
            'token' => $token
        ]));
    }

    abstract public static function operate(array $data);

    public static function sendMessage($message, $token, $userKey)
    {
        $client = new Client('ws://socket-test.loc:3066');
        $client->send(json_encode([
            'action' => 'chat',
            'message' => $message,
            'token' => $token,
            'userKey' => $userKey
        ]));
    }
}
