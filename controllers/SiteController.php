<?php

declare(strict_types=1);

namespace app\controllers;

use app\models\LoginForm;
use app\models\User;
use app\services\StatsService;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ErrorAction;
use yii\web\Response;

class SiteController extends Controller
{
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['login', 'signup', 'logout'],
                'rules' => [
                    [
                        'actions' => ['login', 'signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actions(): array
    {
        return [
            'error' => [
                'class' => ErrorAction::class,
            ],
        ];
    }

    public function actionIndex(): string|Response
    {
        if (Yii::$app->user->isGuest) {
            return $this->render('index');
        }

        $stats = new StatsService((int)Yii::$app->user->id);
        $habits = $stats->habitSummary();

        return $this->render('dashboard', [
            'tasks' => $stats->taskSummary(),
            'habits' => $habits,
            'bestStreak' => $habits ? max(array_column($habits, 'streak')) : 0,
            'habitsToday' => count(array_filter(array_column($habits, 'today'))),
        ]);
    }

    public function actionSignup(): string|Response
    {
        $model = new User(['scenario' => User::SCENARIO_REGISTER]);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->user->login($model);
            Yii::$app->session->setFlash('success', 'Welcome aboard, ' . $model->username . '!');
            return $this->redirect(['/site/index']);
        }

        // don't send the passwords back to the browser
        $model->password = $model->password_repeat = null;
        return $this->render('signup', ['model' => $model]);
    }

    public function actionLogin(): string|Response
    {
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->redirect(['/site/index']);
        }

        $model->password = '';
        return $this->render('login', ['model' => $model]);
    }

    public function actionLogout(): Response
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }
}
