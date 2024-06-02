<?php

declare(strict_types=1);

namespace app\commands;

use app\models\HabitLog;
use app\models\Task;
use app\models\User;
use yii\console\Controller;
use yii\console\ExitCode;

/**
 * Fills the database with a demo user, tasks and habit history.
 *
 *     php yii seed
 */
class SeedController extends Controller
{
    private const USERNAME = 'demo';
    private const PASSWORD = 'demo1234';

    public function actionIndex(): int
    {
        if (User::findByUsername(self::USERNAME) !== null) {
            $this->stdout("User '" . self::USERNAME . "' already exists, nothing to do.\n");
            return ExitCode::OK;
        }

        $user = new User(['scenario' => User::SCENARIO_REGISTER]);
        $user->username = self::USERNAME;
        $user->email = 'demo@example.com';
        $user->password = $user->password_repeat = self::PASSWORD;
        if (!$user->save()) {
            $this->stderr('Cannot create user: ' . json_encode($user->errors) . "\n");
            return ExitCode::DATAERR;
        }

        $this->seedTasks($user);
        $this->seedHabits($user);

        $this->stdout("Done. Log in as " . self::USERNAME . ' / ' . self::PASSWORD . "\n");
        return ExitCode::OK;
    }

    private function seedTasks(User $user): void
    {
        $tasks = [
            ['Finish the Yii2 guide, chapter about ActiveRecord', Task::PRIORITY_HIGH, '+2 days', true],
            ['Write migrations for the pet project', Task::PRIORITY_HIGH, '-1 day', true],
            ['Buy a notebook', Task::PRIORITY_LOW, null, false],
            ['Refactor controllers (make them thin)', Task::PRIORITY_NORMAL, '+5 days', false],
            ['Fix the layout on mobile', Task::PRIORITY_NORMAL, '-3 days', false],
            ['Add README to the repository', Task::PRIORITY_LOW, '+7 days', false],
        ];

        foreach ($tasks as [$title, $priority, $due, $done]) {
            $task = new Task(['scenario' => Task::SCENARIO_TASK]);
            $task->attributes = [
                'title' => $title,
                'priority' => $priority,
                'due_date' => $due ? date('Y-m-d', strtotime($due)) : null,
            ];
            $task->user_id = $user->id;
            $task->type = Task::TYPE_TASK;
            $task->status = $done ? Task::STATUS_DONE : Task::STATUS_OPEN;
            $task->save();
        }
    }

    private function seedHabits(User $user): void
    {
        // [title, how often (0..1) the habit was done during the last 30 days]
        $habits = [
            ['Read 20 pages', 0.8],
            ['Workout', 0.5],
            ['Practice PHP for 1 hour', 0.9],
        ];

        mt_srand(42); // same history on every run
        foreach ($habits as [$title, $chance]) {
            $habit = new Task(['scenario' => Task::SCENARIO_HABIT]);
            $habit->title = $title;
            $habit->user_id = $user->id;
            $habit->type = Task::TYPE_HABIT;
            $habit->save();
            // the habit "exists" for 30 days already
            $habit->updateAttributes(['created_at' => strtotime('-30 days')]);

            for ($i = 29; $i >= 0; $i--) {
                if (mt_rand() / mt_getrandmax() <= $chance) {
                    $log = new HabitLog(['task_id' => $habit->id, 'log_date' => date('Y-m-d', strtotime("-$i days"))]);
                    $log->save();
                }
            }
        }
    }
}
