<?php

namespace SwooleApp\SwooleAppMongoConnection\Tasks;

use Sidalex\SwooleApp\Classes\Tasks\Executors\AbstractTaskExecutor;
use Sidalex\SwooleApp\Classes\Tasks\TaskResulted;
use SwooleApp\SwooleAppMongoConnection\ConfigMongoGetter\MongoConfigGetter;
use SwooleApp\SwooleAppMongoConnection\Initializers\MongoStaticClientInitializer;
use SwooleApp\SwooleAppMongoConnection\Pool\Constants;
use SwooleApp\SwooleAppMongoConnection\Pool\MongoPool;

class MongoTasksExecutor extends AbstractTaskExecutor
{

    /**
     * @throws \Exception
     */
    public function execute(): TaskResulted
    {
        //todo добавить валидацию параметров dataStorage
        $mongoConfig = (new MongoConfigGetter())->getValidConfig($this->app);
        if ($mongoConfig->typeConnection === Constants::CONNECTION_POOL) {
            $result = $this->commandPoolExec();
        } elseif ($mongoConfig->typeConnection === Constants::CONNECTION_STATIC) {
            $result = $this->commandStaticExec();
        } else {
            //todo вывод ошибки
            $result = [];
        }


        return new TaskResulted($result, true);
    }

    /**
     * @return mixed[]
     * @throws \Exception
     */
    private function commandPoolExec(): array
    {
        $option = $this->dataStorage['option'] ?? [];
        switch ($this->dataStorage['method']) {
            case 'find':
                $result = $this->findPool($this->dataStorage['poolKey'], $this->dataStorage['collectionName'], $this->dataStorage['query'], $option);
                break;
            case 'findOne':
                $result = $this->findOnePool($this->dataStorage['poolKey'], $this->dataStorage['collectionName'], $this->dataStorage['query'], $option);
                break;
//            case 'insertOne':
//                $result = $this->insertOne($this->dataStorage['collectionName'], $this->dataStorage['data'], $option);
//                break;
//            case 'insertMany':
//                $result = $this->insertMany($this->dataStorage['collectionName'], $this->dataStorage['data'], $option);
//                break;
//            case 'updateOne':
//                $result = $this->updateOne($this->dataStorage['collectionName'], $this->dataStorage['query'], $this->dataStorage['updateData'], $option);
//                break;
//            case 'updateMany':
//                $result = $this->updateMany($this->dataStorage['collectionName'], $this->dataStorage['query'], $this->dataStorage['updateData'], $option);
//                break;
//            case 'deleteOne':
//                $result = $this->deleteOne($this->dataStorage['collectionName'], $this->dataStorage['query'], $option);
//                break;
//            case 'deleteMany':
//                $result = $this->deleteMany($this->dataStorage['collectionName'], $this->dataStorage['query'], $option);
//                break;
            default:
                throw new \Exception('Unsupported method: ' . $this->dataStorage['method']);
        }
        return $result;
    }

    /**
     * @param string $collectionName
     * @param string $poolKey
     * @param array<mixed>|object $query
     * @param array<mixed> $option
     * @return array<mixed>
     * @throws \Exception
     */
    protected
    function findPool(string $collectionName, string $poolKey, array|object $query, array $option = []): array
    {
        $this->checkContainerPool($poolKey);
        $mongoPool = $this->app->getStateContainer()->getContainer(Constants::CONTAINER_POOL_NAME)[$poolKey];
        $result = $mongoPool->find($collectionName, $query, $option);
        unset($mongoPool);
        return $result;
    }
    /**
     * @param string $poolKey
     * @param string $collectionName
     * @param array<mixed>|object $query
     * @param array<mixed> $option
     * @return array<mixed>
     * @throws \Exception
     */
    private function findOnePool(string $poolKey, string $collectionName, array|object $query, array $option): array
    {
        $this->checkContainerPool($poolKey);
        $mongoPool = $this->app->getStateContainer()->getContainer(Constants::CONTAINER_POOL_NAME)[$poolKey];
        $result = $mongoPool->findOne($collectionName, $query, $option);
        unset($mongoPool);
        return $result;
    }

    /**
     * @param string $poolKey
     * @return void
     * @throws \Exception
     */
    private function checkContainerPool(string $poolKey)
    {
        if (!$this->app->getStateContainer()->getContainer(Constants::CONTAINER_POOL_NAME)[$poolKey] instanceof MongoPool) {
            throw new \Exception('container with key ' . $poolKey . 'is not a MongoPool instance');
        }
    }


    /**
     * @return mixed[]
     */
    private function commandStaticExec(): array
    {
        $option = $this->dataStorage['option'] ?? [];
        switch ($this->dataStorage['method']) {
            case 'find':
                $result = $this->find($this->dataStorage['collectionName'], $this->dataStorage['query'], $option);
                break;

            case 'findOne':
                $result = $this->findOne($this->dataStorage['collectionName'], $this->dataStorage['query'], $option);
                break;

            case 'insertOne':
                $result = $this->insertOne($this->dataStorage['collectionName'], $this->dataStorage['data'], $option);
                break;

            case 'insertMany':
                $result = $this->insertMany($this->dataStorage['collectionName'], $this->dataStorage['data'], $option);
                break;

            case 'updateOne':
                $result = $this->updateOne($this->dataStorage['collectionName'], $this->dataStorage['filter'], $this->dataStorage['update'], $option);
                break;

            case 'updateMany':
                $result = $this->updateMany($this->dataStorage['collectionName'], $this->dataStorage['filter'], $this->dataStorage['update'], $option);
                break;

            case 'deleteOne':
                $result = $this->deleteOne($this->dataStorage['collectionName'], $this->dataStorage['filter'], $option);
                break;

            case 'deleteMany':
                $result = $this->deleteMany($this->dataStorage['collectionName'], $this->dataStorage['filter'], $option);
                break;

            default:
                throw new \Exception('Unsupported method: ' . $this->dataStorage['method']);
        }
        return $result;
    }

    /**
     * @param string $collectionName
     * @param array<mixed>|object $query
     * @param array<mixed> $option
     * @return array<mixed>
     * @throws \Exception
     */
    protected function find(string $collectionName, array|object $query, array $option = []): array
    {
        $mongoConfig = (new MongoConfigGetter())->getValidConfig($this->app);
        $mongoClient = (new MongoStaticClientInitializer)->getMongoClient($mongoConfig->connectionCredential);

        // Find multiple documents
        $result = $mongoClient->selectCollection($collectionName)->find($query, $option)->toArray();

        unset($mongoClient);
        unset($mongoConfig);

        return $result;
    }

    /**
     * @param string $collectionName
     * @param array<mixed>|object $query
     * @param array<mixed> $option
     * @return mixed|null
     * @throws \Exception
     */
    protected function findOne(string $collectionName, array|object $query, array $option = []): mixed
    {
        $mongoConfig = (new MongoConfigGetter())->getValidConfig($this->app);
        $mongoClient = (new MongoStaticClientInitializer)->getMongoClient($mongoConfig->connectionCredential);

        // Find a single document
        $result = $mongoClient->selectCollection($collectionName)->findOne($query, $option);

        unset($mongoClient);
        unset($mongoConfig);

        return $result;
    }
    /**
     * @param string $collectionName
     * @param array<mixed>|object $data
     * @param array<mixed> $option
     * @return array<mixed>
     * @throws \Exception
     */
    protected function insertOne(string $collectionName, array|object $data, array $option = []): array
    {
        $mongoConfig = (new MongoConfigGetter())->getValidConfig($this->app);
        $mongoClient = (new MongoStaticClientInitializer)->getMongoClient($mongoConfig->connectionCredential);

        // Insert the document
        $result = $mongoClient->selectCollection($collectionName)->insertOne($data, $option);

        unset($mongoClient);
        unset($mongoConfig);

        // Return the result as an array
        return [
            'insertedId' => (string)$result->getInsertedId(),
            'acknowledged' => $result->isAcknowledged(),
        ];
    }

    /**
     * @param string $collectionName
     * @param array<array<mixed>|object> $data
     * @param array<mixed> $option
     * @return array<mixed>
     * @throws \Exception
     */
    protected function insertMany(string $collectionName, array $data, array $option = []): array
    {
        $mongoConfig = (new MongoConfigGetter())->getValidConfig($this->app);
        $mongoClient = (new MongoStaticClientInitializer)->getMongoClient($mongoConfig->connectionCredential);

        // Insert multiple documents
        $result = $mongoClient->selectCollection($collectionName)->insertMany($data, $option);

        unset($mongoClient);
        unset($mongoConfig);

        // Return the result as an array
        $insertedIds = array_map(fn($id) => (string)$id, $result->getInsertedIds());

        return [
            'insertedIds' => $insertedIds,
            'acknowledged' => $result->isAcknowledged(),
        ];
    }

    /**
     * @param string $collectionName
     * @param array<mixed> $filter
     * @param array<mixed> $update
     * @param array<mixed> $option
     * @return array<mixed>
     * @throws \Exception
     */
    protected function updateOne(string $collectionName, array $filter, array $update, array $option = []): array
    {
        $mongoConfig = (new MongoConfigGetter())->getValidConfig($this->app);
        $mongoClient = (new MongoStaticClientInitializer)->getMongoClient($mongoConfig->connectionCredential);

        // Update a single document
        $result = $mongoClient->selectCollection($collectionName)->updateOne($filter, $update, $option);

        unset($mongoClient);
        unset($mongoConfig);

        // Return the result as an array
        return [
            'matchedCount' => $result->getMatchedCount(),
            'modifiedCount' => $result->getModifiedCount(),
            'acknowledged' => $result->isAcknowledged(),
        ];
    }

    /**
     * @param string $collectionName
     * @param array<mixed> $filter
     * @param array<mixed> $update
     * @param array<mixed> $option
     * @return array<mixed>
     * @throws \Exception
     */
    protected function updateMany(string $collectionName, array $filter, array $update, array $option = []): array
    {
        $mongoConfig = (new MongoConfigGetter())->getValidConfig($this->app);
        $mongoClient = (new MongoStaticClientInitializer)->getMongoClient($mongoConfig->connectionCredential);

        // Update multiple documents
        $result = $mongoClient->selectCollection($collectionName)->updateMany($filter, $update, $option);

        unset($mongoClient);
        unset($mongoConfig);

        // Return the result as an array
        return [
            'matchedCount' => $result->getMatchedCount(),
            'modifiedCount' => $result->getModifiedCount(),
            'acknowledged' => $result->isAcknowledged(),
        ];
    }

    /**
     * @param string $collectionName
     * @param array<mixed> $filter
     * @param array<mixed> $option
     * @return array<mixed>
     * @throws \Exception
     */
    protected function deleteOne(string $collectionName, array $filter, array $option = []): array
    {
        $mongoConfig = (new MongoConfigGetter())->getValidConfig($this->app);
        $mongoClient = (new MongoStaticClientInitializer)->getMongoClient($mongoConfig->connectionCredential);

        // Delete a single document
        $result = $mongoClient->selectCollection($collectionName)->deleteOne($filter, $option);

        unset($mongoClient);
        unset($mongoConfig);

        // Return the result as an array
        return [
            'deletedCount' => $result->getDeletedCount(),
            'acknowledged' => $result->isAcknowledged(),
        ];
    }

    /**
     * @param string $collectionName
     * @param array<mixed> $filter
     * @param array<mixed> $option
     * @return array<mixed>
     * @throws \Exception
     */
    protected function deleteMany(string $collectionName, array $filter, array $option = []): array
    {
        $mongoConfig = (new MongoConfigGetter())->getValidConfig($this->app);
        $mongoClient = (new MongoStaticClientInitializer)->getMongoClient($mongoConfig->connectionCredential);

        // Delete multiple documents
        $result = $mongoClient->selectCollection($collectionName)->deleteMany($filter, $option);

        unset($mongoClient);
        unset($mongoConfig);

        // Return the result as an array
        return [
            'deletedCount' => $result->getDeletedCount(),
            'acknowledged' => $result->isAcknowledged(),
        ];
    }

}