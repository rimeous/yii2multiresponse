<?php
/**
 * Created by PhpStorm.
 * User: lars
 * Date: 16.05.18
 * Time: 21:11
 */

namespace vendor\larsnovikov\yii2multiresponse\widgets;

use WebSocket\Client;
use yii\queue\Queue;

/**
 * Class TestWidget
 * @package vendor\larsnovikov\yii2multiresponse\widgets
 */
class TestWidget extends AbstractWidget
{
    /**
     * @return string
     */
    public static function getUrl(): string
    {
        return 'ws://socket-test.loc:3081';
    }

    /**
     * @param array $data
     * @throws \WebSocket\BadOpcodeException
     */
    public static function operate(array $data): void
    {
        var_dump('operating');

        sleep(rand(0, 2));
        // TODO обработка данных
        $message = 'test1: '.$data['data']['test1'].' test2: '.$data['data']['test2'];
        echo $data['userKey'].' token '.$data['token'].'--- :'.$message;

        self::sendMessage($message, $data['token']);
    }

    /**
     * @return Queue
     */
    public static function getQueueComponent(): Queue
    {
        return \Yii::$app->testQueue;
    }
}
