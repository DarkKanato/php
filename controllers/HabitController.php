<?php

declare(strict_types=1);

namespace app\controllers;

use app\models\HabitLog;
use app\models\Task;
use app\services\StatsService;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Habits are Task records with type = TYPE_HABIT, check-ins are stored as HabitLog.
 */
class HabitController extends Controller
{
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    ['allow' => true, 'roles' => ['@']],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['post'],
                    'checkin' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex(): string
    {
        $stats = new StatsService((int)Yii::$app->user->id);

        return $this->render('index', ['habits' => $stats->habitSummary()]);
    }

    public function actionCreate(): string|Response
    {
        $model = new Task(['scenario' => Task::SCENARIO_HABIT]);
        $model->user_id = Yii::$app->user->id;
        $model->type = Task::TYPE_HABIT;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Habit created. Time to start the streak!');
            return $this->redirect(['index']);
        }

        return $this->render('create', ['model' => $model]);
    }

    public function actionUpdate(int $id): string|Response
    {
        $model = $this->findModel($id);
        $model->scenario = Task::SCENARIO_HABIT;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Habit updated.');
            return $this->redirect(['index']);
        }

        return $this->render('update', ['model' => $model]);
    }

    /**
     * Toggles today's check-in: first click checks in, second one cancels it.
     */
    public function actionCheckin(int $id): Response
    {
        $habit = $this->findModel($id);
        $today = date('Y-m-d');

        $log = HabitLog::findOne(['task_id' => $habit->id, 'log_date' => $today]);
        if ($log !== null) {
            $log->delete();
        } else {
            $log = new HabitLog(['task_id' => $habit->id, 'log_date' => $today]);
            if (!$log->save()) {
                Yii::$app->session->setFlash('error', 'Could not save the check-in.');
            }
        }

        return $this->redirect(Yii::$app->request->referrer ?: ['index']);
    }

    public function actionDelete(int $id): Response
    {
        $this->findModel($id)->delete();
        Yii::$app->session->setFlash('info', 'Habit deleted together with its history.');

        return $this->redirect(['index']);
    }

    protected function findModel(int $id): Task
    {
        $model = Task::findOwned($id, (int)Yii::$app->user->id, Task::TYPE_HABIT);
        if ($model === null) {
            throw new NotFoundHttpException('Habit not found.');
        }
        return $model;
    }
}
