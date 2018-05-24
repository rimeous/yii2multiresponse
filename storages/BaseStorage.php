<?php
/**
 * Created by PhpStorm.
 * User: novikov
 * Date: 24.05.18
 * Time: 15:25
 */

namespace vendor\larsnovikov\yii2multiresponse\storages;

use Ratchet\ConnectionInterface;

/**
 * Class BaseStorage
 * @package vendor\larsnovikov\yii2multiresponse\storages
 */
class BaseStorage implements StorageInterface
{
    /**
     * Зарегистрированные токены
     * @var array
     */
    private static $registeredTokens = [];

    /**
     * Ответы
     * @var array
     */
    private static $responses = [];

    /**
     * Регистрация токена
     * token => Client
     * @param string $token
     * @param ConnectionInterface $client
     */
    public static function registerToken(string $token, ConnectionInterface $client): void
    {
        self::$registeredTokens[$token] = $client;
    }

    /**
     * Регистрация ответа
     * token => response
     * @param string $token
     * @param string $response
     */
    public static function registerResponse(string $token, string $response): void
    {
        self::$responses[$token] = $response;
    }

    /**
     * Получение объекта клиента по токену
     * @param string $token
     * @param bool $once
     * @return null|ConnectionInterface
     */
    public static function getClientByToken(string $token, bool $once = true): ?ConnectionInterface
    {
        if (array_key_exists($token, self::$registeredTokens)) {
            $client = self::$registeredTokens[$token];
            unset(self::$registeredTokens[$token]);
            return $client;
        }

        return null;
    }

    /**
     * Получение ответа по токену
     * @param string $token
     * @param bool $once
     * @return null|string
     */
    public static function getResponseByToken(string $token, bool $once = true): ?string
    {
        if (array_key_exists($token, self::$responses)) {
            $response = self::$responses[$token];
            unset(self::$responses[$token]);
            return $response;
        }

        return null;
    }
}
