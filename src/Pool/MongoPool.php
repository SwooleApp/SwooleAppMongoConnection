<?php

namespace SwooleApp\SwooleAppMongoConnection\Pool;

use MongoDB\Database;

class MongoPool extends ConnectionPool
{
    /**
     * @var array<Database>
     */
    protected array $pool = [];

    public function insertOne(string $collectionName, array|object $data, array $option = []): array
    {
        $key = $this->searchFreeResource();
        $result = $this->pool[$key]->selectCollection($collectionName)->insertOne($data, $option)->getInsertedId();
        $this->freePull[$key] = true;
        return (array)$result;
    }

    public function insertMany(string $collectionName, array|object $data,array $option=[]): array
    {
        $key = $this->searchFreeResource();
        $result = $this->pool[$key]->selectCollection($collectionName)->insertMany($data,$option)->getInsertedIds();
        $this->freePull[$key] = true;
        return (array)$result;
    }

    public function find(string $collectionName, array|object $query, array $option): array
    {
        $key = $this->searchFreeResource();
        $result = $this->pool[$key]->selectCollection($collectionName)->find($query, $option)->toArray();
        $this->freePull[$key] = true;
        return $result;
    }

    public function findOne(string $collectionName, mixed $query): array
    {
        $key = $this->searchFreeResource();
        $result = $this->pool[$key]->selectCollection($collectionName)->findOne($query)->toArray();
        $this->freePull[$key] = true;
        return $result;
    }

    public function updateOne(string $collectionName, array|object $query, array|object $updateData, array $option = []): array
    {
        $key = $this->searchFreeResource();
        $result = $this->pool[$key]->selectCollection($collectionName)->updateOne($query, $updateData, $option)->getUpsertedId();
        $this->freePull[$key] = true;
        return (array)$result;
    }

    public function updateMany(string $collectionName, array|object $query, array|object $updateData, array $option = []): array
    {
        $key = $this->searchFreeResource();
        $result = $this->pool[$key]->selectCollection($collectionName)->updateMany($query, $updateData, $option)->getUpsertedId();
        $this->freePull[$key] = true;
        return (array)($result);
    }

    public function deleteOne(string $collectionName, array|object $query, array $option = []): array
    {
        $key = $this->searchFreeResource();
        $result = $this->pool[$key]->selectCollection($collectionName)->deleteOne($query, $option)->getDeletedCount();
        $this->freePull[$key] = true;
        return (array)$result;
    }

    public function deleteMany(string $collectionName, array|object $query, array $option = []): array
    {
        $key = $this->searchFreeResource();
        $result = $this->pool[$key]->selectCollection($collectionName)->deleteMany($query, $option)->getDeletedCount();
        $this->freePull[$key] = true;
        return (array)$result;
    }

}