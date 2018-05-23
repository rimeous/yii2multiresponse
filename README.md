Multiresponse for Yii2
=========
Компонент для дозагрузки контента через websocket

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist larsnovikov/yii2multiresponse "*"
```

or add

```
"larsnovikov/yii2multiresponse": "*"
```

to the require section of your `composer.json` file.


Usage
-----

Просто унаследуй свой виджет от `vendor\larsnovikov\yii2multiresponse\widgets\AbstractWidget`

Чтобы все заработало, добавь в конфиг очередь, например:
```
'testQueue' => array_merge(
    [
        'class' => \yii\queue\amqp_interop\Queue::class,
        'queueName' => 'test.queue'
    ],
    [
        'port' => 5672,
        'user' => 'public',
        'password' => 'public',
        'driver' => yii\queue\amqp_interop\Queue::ENQUEUE_AMQP_LIB,
        'dsn' => 'amqp://public:public@172.17.0.1:5672/%2F',
    ]
),
```

Запуск
-----
1. Запусти WSServer `php yii yii2multiresponse/server/start <port>`, где `<port>` - номер порта
2. Запусти слушателей очередей, например: `php yii test-queue/listen`
