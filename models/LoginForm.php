<?php

declare(strict_types=1);

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 */
class LoginForm extends Model
{
    public ?string $username = null;
    public ?string $password = null;
    public bool $rememberMe = true;

    private User|false|null $_user = false;

    public function rules(): array
    {
        return [
            [['username', 'password'], 'required'],
            ['rememberMe', 'boolean'],
            ['password', 'validatePassword'],
        ];
    }

    /**
     * Validates the password.
     * Same error text for "no such user" and "wrong password", so nobody can find out which usernames exist.
     */
    public function validatePassword(string $attribute): void
    {
        if ($this->hasErrors()) {
            return;
        }

        $user = $this->getUser();
        if (!$user || !$user->validatePassword((string)$this->password)) {
            $this->addError($attribute, 'Incorrect username or password.');
        }
    }

    public function login(): bool
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
        }
        return false;
    }

    public function getUser(): ?User
    {
        if ($this->_user === false) {
            $this->_user = User::findByUsername((string)$this->username);
        }
        return $this->_user;
    }
}
