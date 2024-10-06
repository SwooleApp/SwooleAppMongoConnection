<?php

namespace SwooleApp\SwooleAppMongoConnection\Pool;

class Constants
{
    public const CONNECTION_POOL = 'pool';
    public const CONNECTION_STATIC = 'staticInit';
    public const CONNECTION_STATIC_REQUIRED_KEY = ['host', 'port', 'db_name', 'username', 'password'];
    public const CONNECTION_POOL_REQUIRED_KEY = ['container_key', 'host', 'port', 'db_name', 'username', 'password', 'connection_count'];

    public const CONTAINER_POOL_NAME = 'mongoPool';
}