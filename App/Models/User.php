<?php

namespace App\Models;

/**
 * User model
 */
class User extends \Framework\Model
{
    /**
     * User email
     *
     * @var string
     */
    public $email;

    /**
     * User first name
     *
     * @var string
     */
    public $first_name;

    /**
     * User last name
     *
     * @var string
     */
    public $last_name;

    /**
     * User password
     *
     * @var string
     */
    public $password;

    /**
     * Returns db table name
     *
     * @return string model table name
     */
    public function tableName()
    {
        return 'users';
    }

    /**
     * Returns safe attrubites
     *
     * @return array 
     */
    public function safeAttributes()
    {
        return ['email', 'first_name', 'last_name', 'password'];
    }

    /**
     * Returns validation rules
     *
     * @return array 
     */
    public function rules()
    {
        return [
            ['email, first_name, last_name, password', 'required'],
            ['password', 'minlength', 6],
        ];
    }
}