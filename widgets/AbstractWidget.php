<?php
/**
 * Created by PhpStorm.
 * User: lars
 * Date: 16.05.18
 * Time: 21:11
 */

namespace vendor\larsnovikov\yii2multiresponse\widgets;

use vendor\larsnovikov\yii2multiresponse\assets\ContainerAsset;
use WebSocket\Client;
use yii\base\Widget;

/**
 * Class AbstractWidget
 * @package vendor\larsnovikov\yii2multiresponse\widgets
 */
abstract class AbstractWidget extends Widget
{
    /**
     * WSServer
     * @var Client|null
     */
    public static $wsServer = null;

    /**
     * Конфиг для фронтенда
     * @var array
     */
    public static $config = [];

    /**
     * данные
     * @var array
     */
    public $data = [];

    /**
     * Вью пустого контейнера
     * @var string
     */
    public $view = '@vendor/larsnovikov/yii2multiresponse/widgets/views/empty_container';

    /**
     * Url для посылки запросов
     * @return string
     */
    abstract public static function getUrl(): string;

    /**
     * Обработка данных в очереди
     * @param array $data
     */
    abstract public static function operate(array $data): void;

    /**
     * Js функция которая будет вызвана после получения контента от WS server
     * @return string
     */
    public function getCallbackFunction(): string
    {
        return <<<JS
function (response) {
             console.log(response.token);
             console.log(response.message);
             $('#afterload_'+response.token).html(response.message);
        }
JS;
    }

    /**
     * Регистрация ассета
     */
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

        // создадим токен доступа к контейнеру
        $token = \Yii::$app->security->generateRandomString();
        return $this->createEmptyContainer($token);
    }

    /**
     * Послать в очередь
     * @param string $token
     */
    public function sendToQueue(string $token): void
    {
        // положить в очередь данные для обработки
        \vendor\larsnovikov\yii2multiresponse\queues\Queue::putInQueue(static::class, [
            'view' => $this->view,
            'data' => $this->data,
            'token' => $token,
            'widgetClass' => $this::className()
        ]);
    }

    /**
     * Имя текущего классы
     * @return string
     */
    public function getClassName(): string
    {
        return (substr(static::class, strrpos(static::class, '\\') + 1));
    }

    /**
     * Создание пустого контейнера
     * @param $token
     * @return string
     */
    public function createEmptyContainer(string $token): string
    {
        // положим в очередь данные этого виджета
        $this->sendToQueue($token);

        // если это первая обработка виджета, создадим обработчик на добавление конфигурации
        if (self::$config === []) {
            $this->registerEvent();
        }

        $this->registerContainer($token);

        // создание заглушки
        return $this->render($this->view, array_merge($this->data, [
            'token' => $token
        ]));
    }

    /**
     * Регистрация события
     */
    public function registerEvent(): void
    {
        \Yii::$app->response->on(\yii\web\Response::EVENT_BEFORE_SEND, function (\yii\base\Event $event) {
            $response = $event->sender;
            if ($response->format === \yii\web\Response::FORMAT_HTML) {
                $configData = $this->render('@vendor/larsnovikov/yii2multiresponse/widgets/views/config', [
                    'config' => json_encode(self::$config)
                ]);
                $response->data = str_replace('<head>', "<head>$configData", $response->data);
            }
        });
    }

    /**
     * Регистрация контейнера
     * @param string $token
     */
    public function registerContainer(string $token): void
    {
        if (!array_key_exists($this->getClassName(), self::$config)) {
            // если нет данных об этом виджете, создадим
            self::$config[$this->getClassName()] = [
                'containers' => [],
                'url' => static::getUrl(),
                'callback' => '/Function('.trim($this->getCallbackFunction()).')/'
            ];
        }
        // добавим информацию о текущем токене
        self::$config[$this->getClassName()]['containers'][] = $token;
    }

    /**
     * @param $message
     * @param string $token
     * @throws \WebSocket\BadOpcodeException
     */
    public static function sendMessage($message, string $token): void
    {
        if (!self::$wsServer instanceof Client) {
            self::$wsServer = new Client(static::getUrl());
        }

        self::$wsServer->send(json_encode([
            'action' => 'chat',
            'message' => $message,
            'token' => $token
        ]));
    }
}
