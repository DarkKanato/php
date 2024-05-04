<?php

class AuthController extends Controller
{
    public function actionLogin(): void
    {
        $error = '';
        if ($this->isPost()) {
            $user = User::verify($_POST['username'] ?? '', $_POST['password'] ?? '');
            if ($user) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $this->redirect('task/index');
            }
            $error = 'Wrong username or password';
        }
        $this->render('auth/login', ['error' => $error]);
    }

    public function actionRegister(): void
    {
        $error = '';
        if ($this->isPost()) {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            if (strlen($username) < 3) {
                $error = 'Username must be at least 3 characters';
            } elseif (strlen($password) < 6) {
                $error = 'Password must be at least 6 characters';
            } elseif (!User::create($username, $password)) {
                $error = 'This username is already taken';
            } else {
                $this->redirect('auth/login');
            }
        }
        $this->render('auth/register', ['error' => $error]);
    }

    public function actionLogout(): void
    {
        session_destroy();
        $this->redirect('auth/login');
    }
}
