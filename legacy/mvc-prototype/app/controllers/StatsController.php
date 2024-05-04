<?php

class StatsController extends Controller
{
    public function actionIndex(): void
    {
        $uid = $this->requireLogin();

        $habits = Habit::allByUser($uid);
        foreach ($habits as &$h) {
            $h['streak'] = Habit::streak($h['id']);
            $h['rate'] = Habit::completionRate($h['id']);
        }
        unset($h);

        $this->render('stats/index', [
            'tasks' => Task::stats($uid),
            'habits' => $habits,
        ]);
    }
}
