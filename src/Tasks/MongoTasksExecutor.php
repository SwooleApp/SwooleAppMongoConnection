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
    private \stdClass $mongoConfig;

    /**
     * @throws \Exception
     */
    public function execute(): TaskResulted
    {
        //todo добавить валидацию параметров dataStorage
        $this->mongoConfig = (new MongoConfigGetter())->getValidConfig($this->app);
        if ($this->mongoConfig->typeConnection === Constants::CONNECTION_POOL) {
            $result = $this->commandPoolExec();
        } elseif ($this->mongoConfig->typeConnection === Constants::CONNECTION_STATIC) {
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
            case 'insertOne':
                $result = $this->insertOnePool($this->dataStorage['poolKey'], $this->dataStorage['collectionName'], $this->dataStorage['document'], $option);
                break;
            case 'insertMany':
                $result = $this->insertManyPool($this->dataStorage['poolKey'], $this->dataStorage['collectionName'], $this->dataStorage['documents'], $option);
                break;
            case 'updateOne':
                $result = $this->updateOnePool($this->dataStorage['poolKey'], $this->dataStorage['collectionName'], $this->dataStorage['filter'], $this->dataStorage['update'], $option);
                break;
            case 'updateMany':
                $result = $this->updateManyPool($this->dataStorage['poolKey'], $this->dataStorage['collectionName'], $this->dataStorage['filter'], $this->dataStorage['update'], $option);
                break;
            case 'deleteOne':
                $result = $this->deleteOnePool($this->dataStorage['poolKey'], $this->dataStorage['collectionName'], $this->dataStorage['filter'], $option);
                break;
            case 'deleteMany':
                $result = $this->deleteManyPool($this->dataStorage['poolKey'], $this->dataStorage['collectionName'], $this->dataStorage['filter'], $option);
                break;
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
        if ($mongoPool instanceof MongoPool) {
            $result = $mongoPool->find($collectionName, $query, $option);
            unset($mongoPool);
            return $result;
        } else {
            throw new \Exception('Pool not found');
        }
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
        if ($mongoPool instanceof MongoPool) {
            $result = $mongoPool->findOne($collectionName, (array)$query, $option);
            unset($mongoPool);
            return $result;
        } else {
            throw new \Exception('Pool not found');
        }
    }

    /**
     * @param string $poolKey
     * @param string $collectionName
     * @param array<mixed>|object $document
     * @param mixed[] $option
     * @return mixed[]
     * @throws \Exception
     */
    private function insertOnePool(string $poolKey, string $collectionName, array|object $document, array $option)
    {
        $this->checkContainerPool($poolKey);
        $mongoPool = $this->app->getStateContainer()->getContainer(Constants::CONTAINER_POOL_NAME)[$poolKey];
        if ($mongoPool instanceof MongoPool) {
            $result = $mongoPool->insertOne($collectionName, (array)$document, $option);
            unset($mongoPool);
            return $result;
        } else {
            throw new \Exception('Pool not found');
        }
    }

    /**
     *
     * @param string $collectionName
     * @param string $poolKey
     * @param array<array<mixed>|object> $documents
     * @param mixed[] $option
     * @return mixed
     * @throws \Exception
     */
    private function insertManyPool(string $poolKey, string $collectionName, array $documents, array $option)
    {
        $this->checkContainerPool($poolKey);
        $mongoPool = $this->app->getStateContainer()->getContainer(Constants::CONTAINER_POOL_NAME)[$poolKey];
        if ($mongoPool instanceof MongoPool) {
            $result = $mongoPool->insertMany($collectionName, $documents, $option);
            unset($mongoPool);
            return $result;
        } else {
            throw new \Exception('Pool not found');
        }
    }

    /**
     *
     * @param string $poolKey
     * @param string $collectionName
     * @param array<mixed>|object $filter
     * @param array<mixed>|object $update
     * @param array<mixed> $options
     * @return mixed
     * @throws \Exception
     */
    private function updateOnePool(string $poolKey, string $collectionName, array|object $filter, array|object $update, array $options = [])
    {
        $this->checkContainerPool($poolKey);
        $mongoPool = $this->app->getStateContainer()->getContainer(Constants::CONTAINER_POOL_NAME)[$poolKey];
        if ($mongoPool instanceof MongoPool) {
            $result = $mongoPool->updateOne($collectionName, $filter, $update, $options);
            unset($mongoPool);
            return $result;
        } else {
            throw new \Exception('Pool not found');
        }
    }

    /**
     * Обновляет несколько документов в коллекции.
     *
     * @param string $poolKey
     * @param string $collectionName
     * @param array<mixed>|object $filter
     * @param array<mixed>|object $update
     * @param array<mixed> $options
     * @return mixed
     * @throws \Exception
     */
    private function updateManyPool(string $poolKey, string $collectionName, array|object $filter, array|object $update, array $options = [])
    {
        $this->checkContainerPool($poolKey);
        $mongoPool = $this->app->getStateContainer()->getContainer(Constants::CONTAINER_POOL_NAME)[$poolKey];
        if ($mongoPool instanceof MongoPool) {
            $result = $mongoPool->updateMany($collectionName, $filter, $update, $options);
            unset($mongoPool);
            return $result;
        } else {
            throw new \Exception('Pool not found');
        }
    }

    /**
     * Удаляет один документ из коллекции.
     *
     * @param string $poolKey
     * @param string $collectionName
     * @param array<mixed>|object $filter
     * @param array<mixed> $options
     * @return mixed
     * @throws \Exception
     */
    private function deleteOnePool(string $poolKey, string $collectionName, array|object $filter, array $options = [])
    {
        $this->checkContainerPool($poolKey);
        $mongoPool = $this->app->getStateContainer()->getContainer(Constants::CONTAINER_POOL_NAME)[$poolKey];
        if ($mongoPool instanceof MongoPool) {
            $result = $mongoPool->deleteOne($collectionName, $filter, $options);
            unset($mongoPool);
            return $result;
        } else {
            throw new \Exception('Pool not found');
        }
    }

    /**
     * Удаляет несколько документов из коллекции.
     *
     * @param string $poolKey
     * @param string $collectionName
     * @param array<mixed>|object $filter
     * @param array<mixed> $options
     * @return mixed
     * @throws \Exception
     */
    private function deleteManyPool(string $poolKey, string $collectionName, array|object $filter, array $options = [])
    {
        $this->checkContainerPool($poolKey);
        $mongoPool = $this->app->getStateContainer()->getContainer(Constants::CONTAINER_POOL_NAME)[$poolKey];
        if ($mongoPool instanceof MongoPool) {
            $result = $mongoPool->deleteMany($collectionName, $filter, $options);
            unset($mongoPool);
            return $result;
        } else {
            throw new \Exception('Pool not found');
        }
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
                $result = $this->findStatic($this->dataStorage['collectionName'], $this->dataStorage['query'], $option);
                break;

            case 'findOne':
                $result = $this->findOneStatic($this->dataStorage['collectionName'], $this->dataStorage['query'], $option);
                break;

            case 'insertOne':
                $result = $this->insertOneStatic($this->dataStorage['collectionName'], $this->dataStorage['data'], $option);
                break;

            case 'insertMany':
                $result = $this->insertManyStatic($this->dataStorage['collectionName'], $this->dataStorage['data'], $option);
                break;

            case 'updateOne':
                $result = $this->updateOneStatic($this->dataStorage['collectionName'], $this->dataStorage['filter'], $this->dataStorage['update'], $option);
                break;

            case 'updateMany':
                $result = $this->updateManyStatic($this->dataStorage['collectionName'], $this->dataStorage['filter'], $this->dataStorage['update'], $option);
                break;

            case 'deleteOne':
                $result = $this->deleteOneStatic($this->dataStorage['collectionName'], $this->dataStorage['filter'], $option);
                break;

            case 'deleteMany':
                $result = $this->deleteManyStatic($this->dataStorage['collectionName'], $this->dataStorage['filter'], $option);
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
    protected function findStatic(string $collectionName, array|object $query, array $option = []): array
    {
        $mongoClient = (new MongoStaticClientInitializer)->getMongoClient($this->mongoConfig->connectionCredential);

        // Find multiple documents
        $result = $mongoClient->selectCollection($collectionName)->find($query, $option)->toArray();

        unset($mongoClient);


        return $result;
    }

    /**
     * @param string $collectionName
     * @param array<mixed>|object $query
     * @param array<mixed> $option
     * @return mixed|null
     * @throws \Exception
     */
    protected function findOneStatic(string $collectionName, array|object $query, array $option = []): mixed
    {
        $mongoClient = (new MongoStaticClientInitializer)->getMongoClient($this->mongoConfig->connectionCredential);

        // Find a single document
        $result = $mongoClient->selectCollection($collectionName)->findOne($query, $option);

        unset($mongoClient);


        return $result;
    }

    /**
     * @param string $collectionName
     * @param array<mixed>|object $data
     * @param array<mixed> $option
     * @return array<mixed>
     * @throws \Exception
     */
    protected function insertOneStatic(string $collectionName, array|object $data, array $option = []): array
    {
        $mongoClient = (new MongoStaticClientInitializer)->getMongoClient($this->mongoConfig->connectionCredential);

        // Insert the document
        $result = $mongoClient->selectCollection($collectionName)->insertOne($data, $option);

        unset($mongoClient);


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
    protected function insertManyStatic(string $collectionName, array $data, array $option = []): array
    {
        $mongoClient = (new MongoStaticClientInitializer)->getMongoClient($this->mongoConfig->connectionCredential);

        // Insert multiple documents
        $result = $mongoClient->selectCollection($collectionName)->insertMany($data, $option);

        unset($mongoClient);


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
    protected function updateOneStatic(string $collectionName, array $filter, array $update, array $option = []): array
    {
        $mongoClient = (new MongoStaticClientInitializer)->getMongoClient($this->mongoConfig->connectionCredential);

        // Update a single document
        $result = $mongoClient->selectCollection($collectionName)->updateOne($filter, $update, $option);

        unset($mongoClient);


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
    protected function updateManyStatic(string $collectionName, array $filter, array $update, array $option = []): array
    {
        $mongoClient = (new MongoStaticClientInitializer)->getMongoClient($this->mongoConfig->connectionCredential);

        // Update multiple documents
        $result = $mongoClient->selectCollection($collectionName)->updateMany($filter, $update, $option);

        unset($mongoClient);


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
    protected function deleteOneStatic(string $collectionName, array $filter, array $option = []): array
    {
        $mongoClient = (new MongoStaticClientInitializer)->getMongoClient($this->mongoConfig->connectionCredential);

        // Delete a single document
        $result = $mongoClient->selectCollection($collectionName)->deleteOne($filter, $option);

        unset($mongoClient);


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
    protected function deleteManyStatic(string $collectionName, array $filter, array $option = []): array
    {
        $mongoClient = (new MongoStaticClientInitializer)->getMongoClient($this->mongoConfig->connectionCredential);

        // Delete multiple documents
        $result = $mongoClient->selectCollection($collectionName)->deleteMany($filter, $option);

        unset($mongoClient);

        // Return the result as an array
        return [
            'deletedCount' => $result->getDeletedCount(),
            'acknowledged' => $result->isAcknowledged(),
        ];
    }

}