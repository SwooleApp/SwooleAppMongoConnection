<?php

namespace SwooleApp\SwooleAppMongoConnection\ConfigMongoGetter;

use SwooleApp\SwooleAppMongoConnection\Pool\Constants;

class MongoConfigGetter
{
    public function getValidConfig(\Sidalex\SwooleApp\Application $app): \stdClass
    {
        $mongoConfig = $app->getConfig()->getConfigFromKey('mongoDB');

        if (is_null($mongoConfig)) {
            throw new \Error("Config not set key mongoDB");
        }

        $this->validateConnectionType($mongoConfig);
        $this->validateConnectionConfig($mongoConfig);

        return $mongoConfig;
    }

    private function validateConnectionType(\stdClass $mongoConfig): void
    {
        if (!isset($mongoConfig->typeConnection)) {
            throw new \Error("Config mongoDB is not valid: typeConnection not set");
        }

        if (!in_array($mongoConfig->typeConnection, [Constants::CONNECTION_POOL, Constants::CONNECTION_STATIC])) {
            throw new \Error("Config mongoDB has invalid value in typeConnection");
        }
    }

    private function validateConnectionConfig(\stdClass $mongoConfig): void
    {
        if ($mongoConfig->typeConnection === Constants::CONNECTION_POOL) {
            $this->validatePoolConfig($mongoConfig->pool);
        } elseif ($mongoConfig->typeConnection === Constants::CONNECTION_STATIC) {
            $this->validateStaticConfig($mongoConfig->connectionCredential);
        }
    }

    private function validatePoolConfig(mixed $pool): void
    {
        if (!isset($pool) || !is_array($pool)) {
            throw new \Error('Invalid mongoDB config: pool not set or not an array');
        }

        foreach ($pool as $connectionConfig) {
            $this->checkConnectionConfigPool($connectionConfig);
        }
    }

    private function validateStaticConfig(mixed $connectionCredential): void
    {
        if (!$connectionCredential instanceof \stdClass) {
            throw new \Error('Invalid mongoDB config: connectionCredential not set or not an object');
        }

        $this->checkConnectionConfigStatic($connectionCredential);
    }

    private function checkConnectionConfigPool(\stdClass $connectionConfig): void
    {
        foreach (Constants::CONNECTION_POOL_REQUIRED_KEY as $key) {
            if (!isset($connectionConfig->$key)) {
                throw new \Error("Invalid mongoDB config item in pool: missing key $key");
            }
        }
    }

    private function checkConnectionConfigStatic(\stdClass $connectionCredential): void
    {
        foreach (Constants::CONNECTION_STATIC_REQUIRED_KEY as $key) {
            if (!isset($connectionCredential->$key)) {
                throw new \Error("Invalid mongoDB config in connectionCredential: missing key $key");
            }
        }
    }

}