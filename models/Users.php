<?php

namespace naffiq\bridge\models;

use naffiq\bridge\models\query\UsersQuery;
use mongosoft\file\UploadImageBehavior;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "users".
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $avatar
 * @property string $access_token
 * @property integer $is_active
 * @property string $created_at
 * @property string $updated_at
 */
class Users extends ActiveRecord implements IdentityInterface
{
    /**
     * @var string
     */
    public $newPassword;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'password_hash'], 'required'],
            [['is_active'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['username', 'newPassword'], 'string', 'max' => 50],
            [['password_hash'], 'string', 'max' => 255],
            [['username'], 'unique'],
            [['avatar'], 'image', 'extensions' => 'jpg, jpeg, gif, png', 'on' => ['create', 'update']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'password_hash' => 'Password Hash',
            'avatar' => 'Avatar',
            'is_active' => 'Is Active',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'value' => new Expression('NOW()')
            ],
            [
                'class' => UploadImageBehavior::className(),
                'attribute' => 'avatar',
                'scenarios' => ['create', 'update'],
                'path' => '@webroot/media/users/{id}',
                'url' => '@web/media/users/{id}',
                'thumbs' => [
                    'preview' => ['width' => 50, 'height' => 50],
                ]
            ],
        ];
    }

    /**
     * @inheritdoc
     * @return \naffiq\bridge\models\query\UsersQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UsersQuery(get_called_class());
    }

    /**
     * Finds an identity by the given ID.
     * @param string|int $id the ID to be looked for
     * @return IdentityInterface the identity object that matches the given ID.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * Finds an identity by the given token.
     * @param mixed $token the token to be looked for
     * @param mixed $type the type of the token. The value of this parameter depends on the implementation.
     * For example, [[\yii\filters\auth\HttpBearerAuth]] will set this parameter to be `yii\filters\auth\HttpBearerAuth`.
     * @return IdentityInterface the identity object that matches the given token.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::find()->where(['access_token' => $token])->one();
    }

    /**
     * Returns an ID that can uniquely identify a user identity.
     * @return string|int an ID that uniquely identifies a user identity.
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns a key that can be used to check the validity of a given identity ID.
     *
     * The key should be unique for each individual user, and should be persistent
     * so that it can be used to check the validity of the user identity.
     *
     * The space of such keys should be big enough to defeat potential identity attacks.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @return string a key that is used to check the validity of a given identity ID.
     * @see validateAuthKey()
     */
    public function getAuthKey()
    {
        return $this->access_token;
    }

    /**
     * Validates the given auth key.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @param string $authKey the given auth key
     * @return bool whether the given auth key is valid.
     * @see getAuthKey()
     */
    public function validateAuthKey($authKey)
    {
        return $authKey == $this->access_token;
    }

    /**
     * @param $username
     * @return Users
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username]);
    }

    /**
     * Checks specified password
     *
     * @param $password
     * @return bool
     */
    public function validatePassword($password)
    {
        return \Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * @param $password
     */
    public function setPassword($password)
    {
        $this->password_hash = \Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Создание и сохранение пользователя
     *
     * @param $username
     * @param $password
     * @return Users
     */
    public static function create($username, $password)
    {
        $user = new static([
            'username' => $username,
            'avatar' => '',
            'is_active' => 1,
            'access_token' => \Yii::$app->security->generateRandomString(32)

        ]);
        $user->setPassword($password);
        $user->save();

        return $user;
    }

    /**
     * @inheritdoc
     */
    public function beforeValidate()
    {
        if ($this->newPassword) {
            $this->setPassword($this->newPassword);
        }
        return parent::beforeValidate();
    }
}
