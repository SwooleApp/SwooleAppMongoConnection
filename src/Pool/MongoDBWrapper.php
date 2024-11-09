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
    public function insertOne(string|null $poolKey, string $collectionName, array|object $data, array $option = [], float $timeout = 0): array
    {
        $taskData = new BasicTaskData('SwooleApp\SwooleAppMongoConnection\Tasks\MongoTasksExecutor', [
            'method' => 'insertOne',
            'poolKey' => $poolKey,
            'collectionName' => $collectionName,
            'data' => $data,
            'option' => $option,
        ]);
        $taskResult = $this->server->taskwait($taskData, $timeout);
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
    public function insertOneAsync(string|null $poolKey, string $collectionName, array|object $data, array $option = []): int
    {
        $taskData = new BasicTaskData('SwooleApp\SwooleAppMongoConnection\Tasks\MongoTasksExecutor', [
            'method' => 'insertOne',
            'poolKey' => $poolKey,
            'collectionName' => $collectionName,
            'data' => $data,
            'option' => $option,
        ]);
        /**
         * @phpstan-ignore-next-line
         */
        $taskResult = $this->server->task($taskData, finishCallback: function () {
        });
        if (
            (is_int($taskResult))
        ) {
            return $taskResult;
        } else {
            throw new DataException('Error in task async execution: $taskResult=' . $taskResult);
        }
    }

    /**
     * @param string $collectionName
     * @param string|null $poolKey
     * @param array<mixed> $filter
     * @param array<mixed> $update
     * @param array<mixed> $option
     * @param float $timeout time
     * @return array<mixed>
     * @throws DataException
     */
    public function updateOne(string|null $poolKey, string $collectionName, array $filter, array $update, array $option = [], float $timeout = 0): array
    {
        $taskData = new BasicTaskData('SwooleApp\SwooleAppMongoConnection\Tasks\MongoTasksExecutor', [
            'method' => 'updateOne',
            'poolKey' => $poolKey,
            'collectionName' => $collectionName,
            'filter' => $filter,
            'update' => $update,
            'option' => $option,
        ]);
        $taskResult = $this->server->taskwait($taskData, $timeout);
        if (
            ($taskResult instanceof TaskResulted) &&
            is_array($taskResult->getResult())
        ) {
            return $taskResult->getResult();
        } else {
            throw new DataException('Error in task execution: data is not array');
        }
    }

    /**
     * @param string $collectionName
     * @param string|null $poolKey
     * @param array<mixed> $filter
     * @param array<mixed> $update
     * @param array<mixed> $option
     * @return int
     * @throws DataException
     */
    public function updateOneAsync(string|null $poolKey, string $collectionName, array $filter, array $update, array $option = []): int
    {
        $taskData = new BasicTaskData('SwooleApp\SwooleAppMongoConnection\Tasks\MongoTasksExecutor', [
            'method' => 'updateOne',
            'poolKey' => $poolKey,
            'collectionName' => $collectionName,
            'filter' => $filter,
            'update' => $update,
            'option' => $option,
        ]);
        /**
         * @phpstan-ignore-next-line
         */
        $taskResult = $this->server->task($taskData, finishCallback: function () {
        });
        if (
            (is_int($taskResult))
        ) {
            return $taskResult;
        } else {
            throw new DataException('Error in task async execution: $taskResult=' . $taskResult);
        }
    }

    /**
     * @param string $collectionName
     * @param string|null $poolKey
     * @param array<mixed> $filter
     * @param array<mixed> $option
     * @param float $timeout time
     * @return array<mixed>
     * @throws DataException
     */
    public function deleteOne(string|null $poolKey, string $collectionName, array $filter, array $option = [], float $timeout = 0): array
    {
        $taskData = new BasicTaskData('SwooleApp\SwooleAppMongoConnection\Tasks\MongoTasksExecutor', [
            'method' => 'deleteOne',
            'poolKey' => $poolKey,
            'collectionName' => $collectionName,
            'filter' => $filter,
            'option' => $option,
        ]);
        $taskResult = $this->server->taskwait($taskData, $timeout);
        if (
            ($taskResult instanceof TaskResulted) &&
            is_array($taskResult->getResult())
        ) {
            return $taskResult->getResult();
        } else {
            throw new DataException('Error in task execution: data is not array');
        }
    }

    /**
     * @param string $collectionName
     * @param string|null $poolKey
     * @param array<mixed> $filter
     * @param array<mixed> $option
     * @return int
     * @throws DataException
     */
    public function deleteOneAsync(string|null $poolKey, string $collectionName, array $filter, array $option = []): int
    {
        $taskData = new BasicTaskData('SwooleApp\SwooleAppMongoConnection\Tasks\MongoTasksExecutor', [
            'method' => 'deleteOne',
            'poolKey' => $poolKey,
            'collectionName' => $collectionName,
            'filter' => $filter,
            'option' => $option,
        ]);
        /**
         * @phpstan-ignore-next-line
         */
        $taskResult = $this->server->task($taskData, finishCallback: function () {
        });
        if (
            (is_int($taskResult))
        ) {
            return $taskResult;
        } else {
            throw new DataException('Error in task async execution: $taskResult=' . $taskResult);
        }
    }


    /**
     * @param string $collectionName
     * @param string|null $poolKey
     * @param array<array<mixed>|object> $data
     * @param array<mixed> $option
     * @param float $timeout time
     * @return array<mixed>
     * @throws DataException
     */
    public function insertMany(string|null $poolKey, string $collectionName, array $data, array $option = [], float $timeout = 0): array
    {
        $taskData = new BasicTaskData('SwooleApp\SwooleAppMongoConnection\Tasks\MongoTasksExecutor', [
            'method' => 'insertMany',
            'poolKey' => $poolKey,
            'collectionName' => $collectionName,
            'data' => $data,
            'option' => $option,
        ]);
        $taskResult = $this->server->taskwait($taskData, $timeout);
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
     * @param array<array<mixed>|object> $data
     * @param array<mixed> $option
     * @return int
     * @throws DataException
     */
    public function insertManyAsync(string|null $poolKey, string $collectionName, array $data, array $option = []): int
    {
        $taskData = new BasicTaskData('SwooleApp\SwooleAppMongoConnection\Tasks\MongoTasksExecutor', [
            'method' => 'insertMany',
            'poolKey' => $poolKey,
            'collectionName' => $collectionName,
            'data' => $data,
            'option' => $option,
        ]);
        /**
         * @phpstan-ignore-next-line
         */
        $taskResult = $this->server->task($taskData, finishCallback: function () {
        });
        if (
            (is_int($taskResult))
        ) {
            return $taskResult;
        } else {
            throw new DataException('Error in task async execution: $taskResult=' . $taskResult);
        }
    }

    /**
     * @param string $collectionName
     * @param string|null $poolKey
     * @param array<mixed> $filter
     * @param array<mixed> $update
     * @param array<mixed> $option
     * @param float $timeout time
     * @return array<mixed>
     * @throws DataException
     */
    public function updateMany(string|null $poolKey, string $collectionName, array $filter, array $update, array $option = [], float $timeout = 0): array
    {
        $taskData = new BasicTaskData('SwooleApp\SwooleAppMongoConnection\Tasks\MongoTasksExecutor', [
            'method' => 'updateMany',
            'poolKey' => $poolKey,
            'collectionName' => $collectionName,
            'filter' => $filter,
            'update' => $update,
            'option' => $option,
        ]);
        $taskResult = $this->server->taskwait($taskData, $timeout);
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
     * @param array<mixed> $filter
     * @param array<mixed> $update
     * @param array<mixed> $option
     * @return int
     * @throws DataException
     */
    public function updateManyAsync(string|null $poolKey, string $collectionName, array $filter, array $update, array $option = []): int
    {
        $taskData = new BasicTaskData('SwooleApp\SwooleAppMongoConnection\Tasks\MongoTasksExecutor', [
            'method' => 'updateMany',
            'poolKey' => $poolKey,
            'collectionName' => $collectionName,
            'filter' => $filter,
            'update' => $update,
            'option' => $option,
        ]);
        /**
         * @phpstan-ignore-next-line
         */
        $taskResult = $this->server->task($taskData, finishCallback: function () {
        });
        if (
            (is_int($taskResult))
        ) {
            return $taskResult;
        } else {
            throw new DataException('Error in task async execution: $taskResult=' . $taskResult);
        }
    }

    /**
     * @param string $collectionName
     * @param string|null $poolKey
     * @param array<mixed> $filter
     * @param array<mixed> $option
     * @param float $timeout time
     * @return array<mixed>
     * @throws DataException
     */
    public function deleteMany(string|null $poolKey, string $collectionName, array $filter, array $option = [], float $timeout = 0): array
    {
        $taskData = new BasicTaskData('SwooleApp\SwooleAppMongoConnection\Tasks\MongoTasksExecutor', [
            'method' => 'deleteMany',
            'poolKey' => $poolKey,
            'collectionName' => $collectionName,
            'filter' => $filter,
            'option' => $option,
        ]);
        $taskResult = $this->server->taskwait($taskData, $timeout);
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
     * @param array<mixed> $filter
     * @param array<mixed> $option
     * @return int
     * @throws DataException
     */
    public function deleteManyAsync(string|null $poolKey, string $collectionName, array $filter, array $option = []): int
    {
        $taskData = new BasicTaskData('SwooleApp\SwooleAppMongoConnection\Tasks\MongoTasksExecutor', [
            'method' => 'deleteMany',
            'poolKey' => $poolKey,
            'collectionName' => $collectionName,
            'filter' => $filter,
            'option' => $option,
        ]);
        /**
         * @phpstan-ignore-next-line
         */
        $taskResult = $this->server->task($taskData, finishCallback: function () {
        });
        if (
            (is_int($taskResult))
        ) {
            return $taskResult;
        } else {
            throw new DataException('Error in task async execution: $taskResult=' . $taskResult);
        }
    }

    /**
     * @param string $collectionName
     * @param string|null $poolKey
     * @param array<mixed> $filter
     * @param array<mixed> $option
     * @param float $timeout time
     * @return array<mixed>
     * @throws DataException
     */
    public function findOne(string|null $poolKey, string $collectionName, array $filter, array $option = [], float $timeout = 0): array
    {
        $taskData = new BasicTaskData('SwooleApp\SwooleAppMongoConnection\Tasks\MongoTasksExecutor', [
            'method' => 'findOne',
            'poolKey' => $poolKey,
            'collectionName' => $collectionName,
            'filter' => $filter,
            'option' => $option,
        ]);
        $taskResult = $this->server->taskwait($taskData, $timeout);
        if (
            ($taskResult instanceof TaskResulted) &&
            is_array($taskResult->getResult())
        ) {
            return $taskResult->getResult();
        } else {
            throw new DataException('Error in task execution: data is not array');
        }
    }

    /**
     * @param string $collectionName
     * @param string|null $poolKey
     * @param array<mixed> $query
     * @param array<mixed> $option
     * @param float $timeout time
     * @return array<array<mixed>>
     * @throws DataException
     */
    public function find(string|null $poolKey, string $collectionName, array $query = [], array $option = [], float $timeout = 0): array
    {
        $taskData = new BasicTaskData('SwooleApp\SwooleAppMongoConnection\Tasks\MongoTasksExecutor', [
            'method' => 'find',
            'poolKey' => $poolKey,
            'collectionName' => $collectionName,
            'query' => $query,
            'option' => $option,
        ]);
        $taskResult = $this->server->taskwait($taskData, $timeout);
        if (
            ($taskResult instanceof TaskResulted) &&
            is_array($taskResult->getResult())
        ) {
            return $taskResult->getResult();
        } else {
            throw new DataException('Error in task execution: data is not array');
        }
    }

}