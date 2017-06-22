<?php

namespace nullref\useful;

use Yii;

/**
 * Trait PasswordTrait
 *
 * @property $password
 * @property $passwordHash
 */
trait PasswordTrait
{
    /**
     * @var string
     */
    public $passwordConfirm;
    /**
     * @var string $_password
     */
    protected $_password;

    public function getPassword()
    {
        return $this->_password;
    }

    public function setPassword($value)
    {
        if (empty($value)) return;
        $this->passwordHash = Yii::$app->security->generatePasswordHash($value);
        $this->_password = $value;
    }

    /**
     * Validates password
     *
     * @param  string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->passwordHash);
    }
} 