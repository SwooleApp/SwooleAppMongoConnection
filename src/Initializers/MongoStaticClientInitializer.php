<?php

namespace SwooleApp\SwooleAppMongoConnection\Initializers;

use Sidalex\SwooleApp\Application;

class MongoStaticClientInitializer
{
    public function getMongoClient(\stdClass $connectionCredential): \MongoDB\Database
    {
        $connectionString = sprintf(
            'mongodb://%s:%s@%s:%s',
            $connectionCredential->username,
            $connectionCredential->password,
            $connectionCredential->host,
            $connectionCredential->port
        );

        $mongoClient = new \MongoDB\Client($connectionString, [], [
            'typeMap' => [
                'array' => 'array',
                'document' => 'array',
                'root' => 'array',
            ],
        ]);

        return $mongoClient->selectDatabase($connectionCredential->db_name);
    }
}