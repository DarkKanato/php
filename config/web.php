<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

// Secret for cookie validation. Instead of keeping it in git the key is generated
// once on the first request and stored in runtime/ (which is git-ignored).
$cookieKeyFile = dirname(__DIR__) . '/runtime/cookie.key';
if (!is_file($cookieKeyFile)) {
    file_put_contents($cookieKeyFile, bin2hex(random_bytes(32)));
}

$config = [
    'id' => 'habit-tracker',
    'name' => 'Task & Habit Tracker',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'defaultRoute' => 'site/index',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            'cookieValidationKey' => trim(file_get_contents($cookieKeyFile)),
            'enableCsrfValidation' => true,
        ],
        'cache' => [
            'class' => \yii\caching\FileCache::class,
        ],
        'user' => [
            'identityClass' => \app\models\User::class,
            'enableAutoLogin' => true,
            'loginUrl' => ['site/login'],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => \yii\log\FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
    ],
    'params' => $params,
];

// debug toolbar and gii are require-dev packages, so they may be missing
if (YII_ENV_DEV && class_exists(\yii\debug\Module::class)) {
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => \yii\debug\Module::class,
    ];
}
if (YII_ENV_DEV && class_exists(\yii\gii\Module::class)) {
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => \yii\gii\Module::class,
    ];
}

return $config;
