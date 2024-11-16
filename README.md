# Swoole MongoDB Package

[en](#overview) | [ru](#обзор)

Поддержать проект

https://yoomoney.ru/to/410013242088802


## Overview

The **Swoole MongoDB Package for [SwooleApp](https://github.com/SwooleApp/SwooleApp)**  provides a seamless integration of MongoDB with the Swoole framework, enabling developers to leverage the power of asynchronous programming while interacting with MongoDB databases. This package is designed for high-performance applications that require efficient database operations.

## Features

- **Asynchronous Database Operations**: Perform non-blocking CRUD operations with MongoDB.
- **Connection Pooling**: Efficiently manage multiple database connections.
- **Flexible Configuration**: Easily configure connection settings for different environments.
- **Support for UUIDs**: Automatically generate UUIDs for document identifiers.
- **Batch Processing**: Insert and delete multiple documents in a single operation.

## Installation

To install the package, use Composer:

```bash
composer require swooleapp/swoole-mongo-connection
```
## Getting Started
### Configuration

Before using the package, you need to configure your MongoDB connection settings. Create a configuration file (e.g., mongo_config.php) with the following structure:
```json
config example
```
## Usage  with  [SwooleApp](https://github.com/SwooleApp/SwooleApp)
### Initializing the MongoDB Connection
тут будет описание как настраивается подключениие для Пула коннекшенов и для статической инициализации

### Performing CRUD Operations (статические операции с встроеной инициализацией)
You can perform CRUD operations using the MongoDBWrapper class. Here’s an example of inserting a document:

### Performing CRUD Operations (использование Пула подключений)

здесь будет описаниие использовния пула подключений

## Usage  without [SwooleApp](https://github.com/SwooleApp/SwooleApp)
### Initializing the MongoDB Connection
тут будет описание как настраивается подключениие для Пула коннекшенов и для статической инициализации

### Performing CRUD Operations (статические операции с встроеной инициализацией)
You can perform CRUD operations using the MongoDBWrapper class. Here’s an example of inserting a document:

### Performing CRUD Operations (использование Пула подключений)


## Contributing
Contributions are welcome! Please fork the repository and submit a pull request for any improvements or bug fixes.
## License
This package is open-sourced software licensed under the MIT license.

## Обзор

Пакет **Swoole MongoDB для [SwooleApp](https://github.com/SwooleApp/SwooleApp)** 
обеспечивает асинхронный клиент для MongoDB со Swoole framework, позволяя
разработчикам использовать возможности асинхронного программирования
при взаимодействии с базами данных MongoDB.

## Особенности

- **Асинхронные операции с базами данных**: Выполнение неблокирующих CRUD-операций с MongoDB.
- ** Объединение подключений**: Эффективное управление несколькими подключениями к базе данных.
- **Гибкая настройка**: Простая настройка параметров подключения для различных сред.
- **Поддержка UUID**: Автоматическое создание UUID для идентификаторов документов.
- **Пакетная обработка**: Вставка и удаление нескольких документов за одну операцию.
- **Стратегия подключения к базе**: Использование коннекшен пула или
создание отдельного подключения для каждого запроса.

## Установка

Чтобы установить пакет, используйте Composer:

```bash
composer require swooleapp/swoole-mongo-connection
```

## Начало работы
### Настройка
Для подключения и использования базы данных требуется инициализация пакета 
[SwooleApp](https://github.com/SwooleApp/SwooleApp) микро-фреймворк для организации REST API сервисов
на Swoole.

Перед использованием пакета вам необходимо настроить параметры подключения к MongoDB. 
Для этого требуется добавить в конфигурационный json файл следующие ключи.
```json
{
  "mongoDB": {
    "typeConnection": "pool|staticInit",
    "pool": [ 
      {
        "container_key": "имя для стейт контейнера",
        "host": "mongo",
        "port": "27017",
        "db_name": "test",
        "username": "root",
        "password": "rootpassword",
        "connection_count": 20
      }
    ],
    "staticConnections": {
      "ConnectionKey1": {
        "host": "mongo",
        "port": "27017",
        "db_name": "test",
        "username": "root",
        "password": "rootpassword",
      }
    },
    "connectionCredential": {
      "host": "mongo",
      "port": "27017",
      "db_name": "test",
      "username": "root",
      "password": "rootpassword"
    }
  }
}
```
typeConnection - определяет тип подключения (Коннекшен пулл или инициализация при запросе)

pool -  массив объектов настройки подключений различных пуллов подключений.

## Использование с помощью [SwooleApp](https://github.com/SwooleApp/SwooleApp)
### Инициализация соединения с MongoDB
тут будет описание как настраивается подключениие для Пула коннекшенов и для статической инициализации

### Выполнение CRUD-операций (статические операции со встроенной инженерией)
Вы можете выполнять CRUD-операции, используя класс MongoDBWrapper. Вот пример вставки документа:

### Выполнение CRUD-операций (использование для добавления)

здесь будет описаниие использовния пула подключений

## Использование без [SwooleApp](https://github.com/SwooleApp/SwooleApp)
### Инициализация соединения с MongoDB
тут будет описание как настраивается подключениие для Пула коннекшенов и для статической инициализации

### Выполнение CRUD-операций (статические операции со встроенной инженерией)
Вы можете выполнять CRUD-операции, используя класс MongoDBWrapper. Вот пример вставки документа:

### Выполнение CRUD-операций (использование для добавления)


## Внесение вклада
Приветствуется внесение вклада! Пожалуйста, запустите репозиторий и отправьте запрос на обновление для любых улучшений или исправлений ошибок.
## Лицензия
Данный пакет представляет собой программное обеспечение с открытым исходным кодом, лицензируемое по лицензии MIT.