# Task & Habit Tracker

Мой первый проект на PHP: список дел + трекер привычек.
Всё на чистом PHP без фреймворков, база - SQLite.

Недавно переписал на свой маленький MVC (Router / Controller / Model),
потому что в куче php-файлов с кусками SQL уже начал путаться.

## Структура

```
app/core         Router, Controller, Model, Database (singleton)
app/controllers  Auth, Task, Habit, Stats
app/models       User, Task, Habit
app/views        шаблоны + layout.php
public           front controller (index.php) и css
```

## Как запустить

```
php -S localhost:8000 -t public
```

и открыть http://localhost:8000

## Update (May 2024): переезжаю на Yii2

Решил, что пора учить настоящий фреймворк. Самописный MVC перенесён в `legacy/mvc-prototype`,
новое приложение будет на Yii2 Basic. План: миграции, ActiveRecord-модели, авторизация, аналитика.
