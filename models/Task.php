<?php

declare(strict_types=1);

namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Task model. Used for both one-off tasks and habits (see $type).
 *
 * @property int $id
 * @property int $user_id
 * @property int $type
 * @property string $title
 * @property string|null $description
 * @property int $priority
 * @property int $status
 * @property string|null $due_date
 * @property int $created_at
 * @property int $updated_at
 *
 * @property User $user
 * @property HabitLog[] $habitLogs
 */
class Task extends ActiveRecord
{
    public const TYPE_TASK = 0;
    public const TYPE_HABIT = 1;

    public const STATUS_OPEN = 0;
    public const STATUS_DONE = 1;

    public const PRIORITY_LOW = 0;
    public const PRIORITY_NORMAL = 1;
    public const PRIORITY_HIGH = 2;

    // a task has due date and priority, a habit is just a title + description
    public const SCENARIO_TASK = 'task';
    public const SCENARIO_HABIT = 'habit';

    public static function tableName(): string
    {
        return '{{%task}}';
    }

    public function behaviors(): array
    {
        return [
            TimestampBehavior::class,
        ];
    }

    public function scenarios(): array
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_TASK] = ['title', 'description', 'priority', 'due_date'];
        $scenarios[self::SCENARIO_HABIT] = ['title', 'description'];
        return $scenarios;
    }

    public function rules(): array
    {
        return [
            ['title', 'trim'],
            ['title', 'required'],
            ['title', 'string', 'max' => 255],
            ['description', 'string', 'max' => 2000],
            ['priority', 'in', 'range' => array_keys(self::priorityList())],
            ['priority', 'default', 'value' => self::PRIORITY_NORMAL],
            ['due_date', 'date', 'format' => 'php:Y-m-d'],
            ['due_date', 'default', 'value' => null],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'title' => 'Title',
            'description' => 'Description',
            'priority' => 'Priority',
            'status' => 'Status',
            'due_date' => 'Due date',
        ];
    }

    public static function priorityList(): array
    {
        return [
            self::PRIORITY_LOW => 'Low',
            self::PRIORITY_NORMAL => 'Normal',
            self::PRIORITY_HIGH => 'High',
        ];
    }

    public function getPriorityLabel(): string
    {
        return self::priorityList()[$this->priority] ?? 'Unknown';
    }

    public function isDone(): bool
    {
        return $this->status === self::STATUS_DONE;
    }

    public function isHabit(): bool
    {
        return $this->type === self::TYPE_HABIT;
    }

    /**
     * Returns the record only if it belongs to the given user and has the given type.
     */
    public static function findOwned(int $id, int $userId, int $type): ?self
    {
        return static::findOne(['id' => $id, 'user_id' => $userId, 'type' => $type]);
    }

    // ---- relations ----

    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getHabitLogs(): ActiveQuery
    {
        return $this->hasMany(HabitLog::class, ['task_id' => 'id']);
    }

    // ---- habit helpers ----

    public function isCheckedOn(string $date): bool
    {
        return $this->getHabitLogs()->where(['log_date' => $date])->exists();
    }

    /**
     * Number of days in a row (up to today) when the habit was checked in.
     */
    public function getStreak(): int
    {
        $dates = array_flip(
            $this->getHabitLogs()->select('log_date')->column()
        );

        $streak = 0;
        $day = new \DateTimeImmutable('today');
        while (isset($dates[$day->format('Y-m-d')])) {
            $streak++;
            $day = $day->modify('-1 day');
        }
        return $streak;
    }
}
