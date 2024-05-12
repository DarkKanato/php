<?php

declare(strict_types=1);

namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * One row = one check-in of a habit on a given day.
 *
 * @property int $id
 * @property int $task_id
 * @property string $log_date
 * @property int $created_at
 *
 * @property Task $task
 */
class HabitLog extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%habit_log}}';
    }

    public function behaviors(): array
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'updatedAtAttribute' => false,
            ],
        ];
    }

    public function rules(): array
    {
        return [
            [['task_id', 'log_date'], 'required'],
            ['task_id', 'integer'],
            ['task_id', 'exist', 'targetClass' => Task::class, 'targetAttribute' => 'id'],
            ['log_date', 'date', 'format' => 'php:Y-m-d'],
            ['log_date', 'unique', 'targetAttribute' => ['task_id', 'log_date'],
                'message' => 'This habit is already checked in for that day.'],
        ];
    }

    public function getTask(): ActiveQuery
    {
        return $this->hasOne(Task::class, ['id' => 'task_id']);
    }
}
