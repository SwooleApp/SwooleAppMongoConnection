<?php

namespace SwooleApp\SwooleAppMongoConnection\Pool;

use Sidalex\SwooleApp\Classes\Tasks\Data\BasicTaskData;
use Sidalex\SwooleApp\Classes\Tasks\TaskException;
use Sidalex\SwooleApp\Classes\Tasks\TaskResulted;
use Swoole\Http\Server;
use SwooleApp\SwooleAppMongoConnection\Pool\Exception\DataException;

class MongoDBWrapper
{

    protected Server $server;

    public function __construct(Server $server)
    {
        $this->server = $server;
    }

    /**
     * @param string $collectionName
     * @param string|null $poolKey
     * @param array<mixed>|object $data
     * @param array<mixed> $option
     * @param float $timeout time
     * @return array<mixed>
     * @throws DataException
     * @phpstan-ignore-next-line
     * @throws TaskException
     */
    public function insertOne(string $collectionName, string|null $poolKey, array|object $data, array $option = [], float $timeout = 0): array
    {
        $taskData = new BasicTaskData('Sidalex\TestSwoole\Tasks\TestTaskExecutorPool', [
            'method' => 'insertOne',
            'poolKey' => $poolKey,
            'collectionName' => $collectionName,
            'data' => $data,
            'option' => $option,
        ]);
        $taskResult = $this->server->taskwait($taskData,$timeout);
        if (
            ($taskResult instanceof TaskResulted) &&
            is_array($taskResult->getResult())
        ) {
            //todo реализовать проверку валидности ответа
            return $taskResult->getResult();
        } else {
            throw new DataException('Error in task execution: data is not array');
        }
    }

    /**
     * @param string $collectionName
     * @param string|null $poolKey
     * @param array<mixed>|object $data
     * @param array<mixed> $option
     * @return int
     * @throws DataException
     */
    public function insertOneAsync(string $collectionName, string|null $poolKey, array|object $data, array $option = []) :int
    {
        $taskData = new BasicTaskData('Sidalex\TestSwoole\Tasks\TestTaskExecutorPool', [
            'method' => 'insertOne',
            'poolKey' => $poolKey,
            'collectionName' => $collectionName,
            'data' => $data,
            'option' => $option,
        ]);
        $taskResult = $this->server->task($taskData);
        if (
            (is_int($taskResult))
        ) {
            return $taskResult;
        } else {
            throw new DataException('Error in task async execution: $taskResult='.$taskResult);
        }
    }

//    public function insertMany(string $collectionName, array|object $data, array $option = []):array
//    {
//    }
//    public function insertManyAsync(string $collectionName, array|object $data, array $option = []): int
//    {
//    }
//
//    public function updateOne(string $collectionName, array|object $query, array|object $updateData, array $option = []):array
//    {
//    }
//    public function updateMany(string $collectionName, array|object $query, array|object $updateData, array $option = []):array
//    {
//    }
//
//    public function deleteOne(string $collectionName, array|object $query, array $option = []):array
//    {
//    }
//    public function deleteMany(string $collectionName, array|object $query, array $option = []):array
//    {
//    }
//
//    public function findOne(string $collectionName, array|object $query, array $option = []):array
//    {
//    }
//
//    public function find(string $collectionName, array|object $query, array $option = []):array
//    {
//    }

}