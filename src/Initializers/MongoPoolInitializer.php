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
        if ($mongoConfig->typeConnection == Constants::CONNECTION_POOL) {
            $poolList = [];
            foreach ($mongoConfig->pool as $itemConnectionConfig) {
                $pool = new MongoPool($itemConnectionConfig->connection_count);
                $pool->init(function () use ($itemConnectionConfig) {
                    $username_password = $itemConnectionConfig->username . ':' . $itemConnectionConfig->password . '@';
                    $host_and_port = $itemConnectionConfig->host . ':' . $itemConnectionConfig->port;
                    $connectionString = 'mongodb://' . $username_password . $host_and_port;
                    $mongoClient = new \MongoDB\Client($connectionString, [], [
                        'typeMap' => [
                            'array' => 'array',
                            'document' => 'array',
                            'root' => 'array',
                        ],
                    ]);
                    $dm = $mongoClient->selectDatabase($itemConnectionConfig->db_name ?? 'Blog');
                    return $dm;
                });
                $poolList[$itemConnectionConfig->containerKey] = $pool;
            }
            $this->result = $poolList;
            $this->key = Constants::CONTAINER_POOL_NAME;
        }
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
            if (!isset($mongoConfig->pool) || !is_array($mongoConfig->pool)) {
                throw new \Error('Invalid mongoDB config: pool not set or not an array');
            }
            foreach ($mongoConfig->pool as $connectionConfig) {
                $this->checkConnectionConfigPool($connectionConfig);
            }
        } elseif ($mongoConfig->typeConnection === Constants::CONNECTION_STATIC) {
            if (!isset($mongoConfig->connectionCredential) || !is_object($mongoConfig->connectionCredential)) {
                throw new \Error('Invalid mongoDB config: connectionCredential not set or not an object');
            }
            $this->checkConnectionConfigStatic($mongoConfig->connectionCredential);
        }
    }

    private function checkConnectionConfigPool(mixed $connectionConfig): void
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