<?php

namespace Test;

use PHPUnit\Framework\TestCase;

class TestModel extends \Framework\Model
{
    public $email;
    public $first_name;
    public $last_name;
    public $password;
    public $errors = [];

    public function tableName()
    {
        return 'users';
    }

    public function safeAttributes()
    {
        return ['email', 'first_name', 'last_name', 'password'];
    }

    public function rules()
    {
        return [
            ['email, first_name, last_name, password', 'required'],
            ['password', 'minlength', 6],
        ];
    }

    public function setAttributes($data)
    {
        return parent::setAttributes($data);
    }

    public function addError($field, $result)
    {
        return parent::addError($field, $result);
    }

    public function validateRequired($field, $param = null)
    {
        return parent::validateRequired($field, $param);
    }

    public function validateMinlength($field, $param = null)
    {
        return parent::validateMinlength($field, $param);
    }

    public function validate($data = [])
    {
        return parent::validate($data);
    }

    public function truncate()
    {
        $db = static::getDB();
        $db->query('TRUNCATE users');
    }

    public function fixtures()
    {
        $db = static::getDB();
        $db->query("INSERT INTO users (email, first_name, last_name, password) VALUES ('bilbo@shire.com', 'Bilbo', 'Baggins', '12345678')");
    }
}

/**
 */
final class ModelTest extends TestCase
{
    public function setUp()
    {
        $model=new TestModel;
        $model->truncate();
        $model->fixtures();
    }

    public function tearDown()
    {
        $model=new TestModel;
        $model->truncate();
    }

    public function testTableName()
    {
        $model = new TestModel;
        $this->assertEquals($model->tableName(), 'users');
    }

    public function testRequired()
    {
        $model = new TestModel;
        $result = $model->validateRequired('email');
        $this->assertEquals($result, 'email is required');
    }

    public function testMinlength()
    {
        $model = new TestModel;
        $result = $model->validateMinlength('password', 6);
        $this->assertEquals($result, 'password should have at least 6 chars length');
    }

    public function testAddError()
    {
        $model = new TestModel;
        $this->assertEquals($model->errors, []);
        $model->addError('email', 'Email is required');
        $this->assertEquals($model->errors['email'][0], 'Email is required');
    }

    public function testSetAttribute()
    {
        $model = new TestModel;
        $attributes=[
            'email' => 'biblo@shire.com',
            'first_name' => 'Biblo',
            'last_name' => 'Baggins',
            'password' => 'ring1234',
        ];
        $model->setAttributes($attributes);
        $this->assertEquals($model->email, $attributes['email']);
        $this->assertEquals($model->first_name, $attributes['first_name']);
        $this->assertEquals($model->last_name, $attributes['last_name']);
    }

    public function testGetErrors()
    {
        $model = new TestModel;
        $this->assertTrue(empty($model->getErrors()));
        $attributes=[
            'first_name' => 'Biblo',
            'last_name' => 'Baggins',
            'password' => 'ring12345',
        ];
        $model->setAttributes($attributes);
        $model->validate();
        $this->assertFalse(empty($model->getErrors()));
    }

    public function testValidate()
    {
        $model = new TestModel;
        $this->assertFalse($model->validate());
        $attributes=[
            'email' => 'biblo@shire.com',
            'first_name' => 'Biblo',
            'last_name' => 'Baggins',
            'password' => 'ring12345',
        ];
        $model->setAttributes($attributes);
        $this->assertTrue($model->validate());
    }

    public function testFindByPk()
    {
        $model = new TestModel;
        $user = $model->findByPk(1);
        $this->assertEquals($user->email, 'bilbo@shire.com');
    }

    public function testFindAll()
    {
        $model = new TestModel;
        $users = $model->findAll();
        $this->assertEquals($users[0]->email, 'bilbo@shire.com');
    }

    public function testDelete()
    {
        $model = new TestModel;
        $user = $model->findByPk(1);
        $user->delete();
        $userAfter = $model->findByPk(1);
        $this->assertFalse($userAfter);
    }

    public function testUpdate()
    {
        $model = new TestModel;
        $user = $model->findByPk(1);
        $user->update(['first_name' => 'Frodo']);
        $userUpdated = $model->findByPk(1);

        $this->assertEquals($userUpdated->first_name, 'Frodo');
    }

    public function testCreate()
    {
        $model = new TestModel;
        $this->assertFalse($model->create([]));
        $attributes=[
            'email' => 'gandalf@shire.com',
            'first_name' => 'Gandalf',
            'last_name' => 'MIthrandir',
            'password' => 'ring12345',
        ];
        
        $this->assertTrue($model->create($attributes));

        $user = $model->findByPk(2);
        $this->assertEquals($user->email, 'gandalf@shire.com');
    }

}
