<?php

class User extends Model
{
    public static function findByUsername(string $username)
    {
        return self::query('SELECT * FROM users WHERE username = ?', [$username])->fetch();
    }

    public static function create(string $username, string $password): bool
    {
        try {
            self::query('INSERT INTO users (username, password) VALUES (?, ?)', [
                $username,
                password_hash($password, PASSWORD_DEFAULT),
            ]);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public static function verify(string $username, string $password)
    {
        $user = self::findByUsername($username);
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }
}
