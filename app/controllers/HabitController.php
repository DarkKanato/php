<?php

class HabitController extends Controller
{
    public function actionIndex(): void
    {
        $uid = $this->requireLogin();
        $this->render('habit/index', ['habits' => Habit::allByUser($uid)]);
    }

    public function actionCreate(): void
    {
        $uid = $this->requireLogin();
        $title = trim($_POST['title'] ?? '');
        if ($this->isPost() && $title !== '') {
            Habit::create($uid, $title);
        }
        $this->redirect('habit/index');
    }

    public function actionCheckin(): void
    {
        $uid = $this->requireLogin();
        Habit::checkin($uid, (int)($_GET['id'] ?? 0));
        $this->redirect('habit/index');
    }

    public function actionDelete(): void
    {
        $uid = $this->requireLogin();
        Habit::delete($uid, (int)($_GET['id'] ?? 0));
        $this->redirect('habit/index');
    }
}
