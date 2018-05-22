<?php

namespace vendor\larsnovikov\yii2multiresponse\queues;

use vendor\larsnovikov\yii2multiresponse\widgets\AbstractWidget;
use yii\base\BaseObject;

/**
 * Class Queue
 * @package vendor\larsnovikov\yii2multiresponse\queues
 */
class Queue extends BaseObject implements \yii\queue\JobInterface
{
    /**
     * @var AbstractWidget|null
     */
    public $widgetClass = null;

    /**
     * @var
     */
    public $data;

    /**
     * @return mixed
     */
    public function getQueueComponent()
    {
        return $this->widgetClass::getQueueName();
    }

    /**
     * Обработчик очереди
     * @param string $queue
     */
    public function execute($queue): void
    {
        $this->widgetClass::operate($this->data);
    }

    /**
     * @param $widgetClass
     * @param array $data
     */
    public static function putInQueue($widgetClass, array $data): void
    {
        $widgetClass::getQueueComponent()->push(new self([
            'widgetClass' => $widgetClass,
            'data' => $data
        ]));
    }
}
