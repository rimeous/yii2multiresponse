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
class SuperTestWidget extends AbstractWidget
{
    /**
     * @return string
     */
    public static function getUrl(): string
    {
        return 'ws://socket-test.loc:5008';
    }

    /**
     * @param array $data
     * @throws \WebSocket\BadOpcodeException
     */
    public static function operate(array $data): void
    {
        var_dump('operating');

        sleep(rand(1, 4));
        // TODO обработка данных
        $message = 'super test widget';

        self::sendMessage($message, $data['token']);
    }
}
