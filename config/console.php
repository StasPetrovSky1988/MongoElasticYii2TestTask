<?php

use app\services\ElasticComponent;
use yii\mongodb\Connection;

$params = require __DIR__ . '/params.php';

$config = [
    'id' => 'basic-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'app\commands',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
        '@core'  => dirname(__DIR__),
    ],
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => [
            'class' => \yii\db\Connection::class,
            'dsn' => 'sqlite:@app/runtime/db.sqlite',
        ],
        'mongodb' => [
            'class' => Connection::class,
            'dsn' => 'mongodb://admin:secret@localhost:27017/dbname?authSource=admin',
        ],
        'elastic' => [
            'class' => ElasticComponent::class,
            'base_uri' => 'http://localhost:9200'
        ],
    ],
    'params' => $params,
];

return $config;
