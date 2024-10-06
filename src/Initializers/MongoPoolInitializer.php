<?php

namespace SwooleApp\SwooleAppMongoConnection\Initializers;

use Sidalex\SwooleApp\Classes\Initiation\AbstractContainerInitiator;
use SwooleApp\SwooleAppMongoConnection\Pool\Constants;
use SwooleApp\SwooleAppMongoConnection\Pool\MongoPool;

class MongoPoolInitializer extends AbstractContainerInitiator
{
    public function init(\Sidalex\SwooleApp\Application $param): void
    {
        $mongoConfig = $this->getValidConfig($param);

        if ($mongoConfig->typeConnection === Constants::CONNECTION_POOL) {
            $this->initializePool($mongoConfig->pool);
        }
    }

    /**
     * @param array<\stdClass> $poolConfigs
     * @return void
     */
    private function initializePool(array $poolConfigs): void
    {
        $poolList = [];

        foreach ($poolConfigs as $itemConnectionConfig) {
            $pool = new MongoPool($itemConnectionConfig->connection_count);
            $pool->init(fn() => $this->createMongoDatabase($itemConnectionConfig));
            $poolList[$itemConnectionConfig->container_key] = $pool;
        }

        $this->result = $poolList;
        $this->key = Constants::CONTAINER_POOL_NAME;
    }

    private function createMongoDatabase(\stdClass $itemConnectionConfig): \MongoDB\Database
    {
        $connectionString = sprintf(
            'mongodb://%s:%s@%s:%s',
            $itemConnectionConfig->username,
            $itemConnectionConfig->password,
            $itemConnectionConfig->host,
            $itemConnectionConfig->port
        );

        $mongoClient = new \MongoDB\Client($connectionString, [], [
            'typeMap' => [
                'array' => 'array',
                'document' => 'array',
                'root' => 'array',
            ],
        ]);

        return $mongoClient->selectDatabase($itemConnectionConfig->db_name);
    }

    private function getValidConfig(\Sidalex\SwooleApp\Application $param): \stdClass
    {
        $mongoConfig = $param->getConfig()->getConfigFromKey('mongoDB');

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
