<?php

declare(strict_types=1);

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * User model.
 *
 * @property int $id
 * @property string $username
 * @property string $email
 * @property string $password_hash
 * @property string $auth_key
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Task[] $tasks
 */
class User extends ActiveRecord implements IdentityInterface
{
    public const SCENARIO_REGISTER = 'register';
    public const SCENARIO_PROFILE = 'profile';

    /** @var string|null plain password, exists only while the form is being validated */
    public ?string $password = null;
    public ?string $password_repeat = null;

    public static function tableName(): string
    {
        return '{{%user}}';
    }

    public function behaviors(): array
    {
        return [
            TimestampBehavior::class,
        ];
    }

    public function scenarios(): array
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_REGISTER] = ['username', 'email', 'password', 'password_repeat'];
        $scenarios[self::SCENARIO_PROFILE] = ['username', 'email'];
        return $scenarios;
    }

    public function rules(): array
    {
        return [
            [['username', 'email'], 'trim'],
            [['username', 'email'], 'required'],
            ['username', 'string', 'min' => 3, 'max' => 64],
            ['username', 'match', 'pattern' => '/^[a-z0-9_]+$/i',
                'message' => 'Username can contain only latin letters, digits and underscore.'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            [['username', 'email'], 'unique'],

            [['password', 'password_repeat'], 'required', 'on' => self::SCENARIO_REGISTER],
            ['password', 'string', 'min' => 6, 'max' => 72, 'on' => self::SCENARIO_REGISTER],
            ['password_repeat', 'compare', 'compareAttribute' => 'password', 'on' => self::SCENARIO_REGISTER,
                'message' => 'Passwords do not match.'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'username' => 'Username',
            'email' => 'Email',
            'password' => 'Password',
            'password_repeat' => 'Repeat password',
        ];
    }

    public function beforeSave($insert): bool
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        if ($this->password !== null && $this->password !== '') {
            $this->setPassword($this->password);
        }
        if ($insert) {
            $this->generateAuthKey();
        }
        return true;
    }

    // ---- relations ----

    public function getTasks()
    {
        return $this->hasMany(Task::class, ['user_id' => 'id']);
    }

    // ---- password helpers ----

    public function setPassword(string $password): void
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    public function validatePassword(string $password): bool
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    public function generateAuthKey(): void
    {
        $this->auth_key = Yii::$app->security->generateRandomString(32);
    }

    // ---- IdentityInterface ----

    public static function findIdentity($id): ?self
    {
        return static::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null): ?self
    {
        // token auth is not used in this app
        return null;
    }

    public static function findByUsername(string $username): ?self
    {
        return static::findOne(['username' => $username]);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getAuthKey(): string
    {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey): bool
    {
        return Yii::$app->security->compareString($this->auth_key, (string)$authKey);
    }
}
