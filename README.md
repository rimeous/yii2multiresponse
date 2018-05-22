Afterload
=========
afterload

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist larsnovikov/yii2-afterload "*"
```

or add

```
"larsnovikov/yii2-afterload": "*"
```

to the require section of your `composer.json` file.


Usage
-----

Once the extension is installed, simply use it in your code by  :

```php
<?= \larsnovikov\yii2multiresponse\AutoloadExample::widget(); ?>```


\Yii::$app->response->on(\yii\web\Response::EVENT_BEFORE_SEND, function (\yii\base\Event $Event) {
            $Response = $Event->sender;
            if ($Response->format === \yii\web\Response::FORMAT_HTML) {
                $Response->content .= json_encode(self::$containers);
            }
        });
