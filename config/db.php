<?php

// SQLite: the db file lives in runtime/ and is created by `php yii migrate`
return [
    'class' => \yii\db\Connection::class,
    'dsn' => 'sqlite:' . dirname(__DIR__) . '/runtime/tracker.sqlite',
    // sqlite ignores foreign keys unless this pragma is enabled for every connection
    'on afterOpen' => function ($event) {
        $event->sender->createCommand('PRAGMA foreign_keys = ON')->execute();
    },
];
