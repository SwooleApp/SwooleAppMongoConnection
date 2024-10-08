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
     * @param string $poolKey
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
        $taskResult = $this->server->taskwait($taskData);
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

}