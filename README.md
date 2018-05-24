Multiresponse for Yii2
=========
Компонент для дозагрузки контента через websocket

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
composer require larsnovikov/yii2multiresponse:^1.0
```

or add

```
"larsnovikov/yii2multiresponse": "*"
```

to the require section of your `composer.json` file.


Usage
-----

1. Унаследуй свой виджет от `vendor\larsnovikov\yii2multiresponse\widgets\AbstractWidget`

2. Добавь в конфиге в `modules`:
```
'yii2multiresponse' => [
    'class' => vendor\larsnovikov\yii2multiresponse\Module::class
],
```

3. Добавь в конфиге в `components` очередь:

```
'multiResponseQueue' => array_merge(
    [
        'class' => \yii\queue\amqp_interop\Queue::class,
        'queueName' => 'multiresponse.queue'
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
4. Добавь в конфиге в `bootstrap` название компонента очереди

Запуск
-----
1. Запусти WSServer `php yii yii2multiresponse/server/start <port>`, где `<port>` - номер порта
2. Запусти слушателей очередей, например: `php yii multi-response-queue/listen`
