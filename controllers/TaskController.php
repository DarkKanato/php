<?php

declare(strict_types=1);

namespace app\controllers;

use app\models\Task;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * CRUD for one-off tasks (habits live in HabitController).
 */
class TaskController extends Controller
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
                    'toggle' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex(?string $status = null): string
    {
        $query = Task::find()
            ->where(['user_id' => Yii::$app->user->id, 'type' => Task::TYPE_TASK])
            ->orderBy(['status' => SORT_ASC, 'priority' => SORT_DESC, 'due_date' => SORT_ASC, 'id' => SORT_DESC]);

        if ($status === 'open') {
            $query->andWhere(['status' => Task::STATUS_OPEN]);
        } elseif ($status === 'done') {
            $query->andWhere(['status' => Task::STATUS_DONE]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 15],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'status' => $status,
        ]);
    }

    public function actionCreate(): string|Response
    {
        $model = new Task(['scenario' => Task::SCENARIO_TASK]);
        $model->user_id = Yii::$app->user->id;
        $model->type = Task::TYPE_TASK;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Task created.');
            return $this->redirect(['index']);
        }

        return $this->render('create', ['model' => $model]);
    }

    public function actionUpdate(int $id): string|Response
    {
        $model = $this->findModel($id);
        $model->scenario = Task::SCENARIO_TASK;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Task updated.');
            return $this->redirect(['index']);
        }

        return $this->render('update', ['model' => $model]);
    }

    public function actionToggle(int $id): Response
    {
        $model = $this->findModel($id);
        $model->status = $model->isDone() ? Task::STATUS_OPEN : Task::STATUS_DONE;
        $model->save(false, ['status', 'updated_at']);

        return $this->redirect(Yii::$app->request->referrer ?: ['index']);
    }

    public function actionDelete(int $id): Response
    {
        $this->findModel($id)->delete();
        Yii::$app->session->setFlash('info', 'Task deleted.');

        return $this->redirect(['index']);
    }

    /**
     * Finds a task of the *current user*. Somebody else's task (or a habit) gives the same 404
     * as a missing one, so ids can't be guessed (it was possible to edit/delete any task by id before).
     */
    protected function findModel(int $id): Task
    {
        $model = Task::findOwned($id, (int)Yii::$app->user->id, Task::TYPE_TASK);
        if ($model === null) {
            throw new NotFoundHttpException('Task not found.');
        }
        return $model;
    }
}
