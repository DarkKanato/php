<?php

class TaskController extends Controller
{
    public function actionIndex(): void
    {
        $uid = $this->requireLogin();
        $this->render('task/index', ['tasks' => Task::allByUser($uid)]);
    }

    public function actionCreate(): void
    {
        $uid = $this->requireLogin();
        $title = trim($_POST['title'] ?? '');
        if ($this->isPost() && $title !== '') {
            Task::create($uid, $title);
        }
        $this->redirect('task/index');
    }

    public function actionDone(): void
    {
        $uid = $this->requireLogin();
        Task::markDone($uid, (int)($_GET['id'] ?? 0));
        $this->redirect('task/index');
    }

    public function actionDelete(): void
    {
        $uid = $this->requireLogin();
        Task::delete($uid, (int)($_GET['id'] ?? 0));
        $this->redirect('task/index');
    }
}
