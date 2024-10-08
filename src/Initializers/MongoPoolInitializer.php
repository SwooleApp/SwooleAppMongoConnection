<?php

namespace SwooleApp\SwooleAppMongoConnection\Initializers;

use Sidalex\SwooleApp\Classes\Initiation\AbstractContainerInitiator;
use SwooleApp\SwooleAppMongoConnection\ConfigMongoGetter\MongoConfigGetter;
use SwooleApp\SwooleAppMongoConnection\Pool\Constants;
use SwooleApp\SwooleAppMongoConnection\Pool\MongoPool;

class MongoPoolInitializer extends AbstractContainerInitiator
{
    protected MongoConfigGetter $mongoConfigGetter;
    public function __construct()
    {
        $this->mongoConfigGetter = new MongoConfigGetter();
    }

    public function init(\Sidalex\SwooleApp\Application $param): void
    {
        $mongoConfig = $this->mongoConfigGetter->getValidConfig($param);

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

}
