<?php

namespace Framework;

use PDO;
use App\Config;

/**
 * Base model
 */
abstract class Model
{
    /**
     * Errors array
     *
     * @var array
     */
    protected $errors = [];

    /**
     * Get the PDO database connection
     *
     * @return mixed
     */
    protected static function getDB()
    {
        static $db = null;
        if ($db === null) {
            $dsn = 'mysql:host=' . Config::DB_HOST . ';dbname=' . Config::DB_NAME . ';charset=utf8';
            $db = new PDO($dsn, Config::DB_USER, Config::DB_PASSWORD);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        return $db;
    }

    /**
     * Should return table db name
     */
    abstract public function tableName();

    /**
     * Returns errors array
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Creates record if validation succed
     *
     * @param  array $data key => value attrubute data where key is column name 
     * @return boolean
     */
    public function create($data)
    {
        if ($this->validate($data)) {
            $db = static::getDB();
            $sql = $this->createInsertQuery();
            $parameters = $this->createInsertParams($data);
            $query = $db->prepare($sql);
            return $query->execute($parameters);
        }
        return false;
    }

    /**
     * Creates insert into query
     *
     * @return string
     */
    protected function createInsertQuery()
    {
        $attributes = implode(',', $this->safeAttributes());
        $attributesPrepeare = array_map(
            function ($item) {
                return ":$item"; 
            }, $this->safeAttributes()
        );
        $attributesPrepeareCollected = implode(',', $attributesPrepeare);
        return "INSERT INTO ".$this->tableName()." (".$attributes.") VALUES (".$attributesPrepeareCollected.")";
    }

    /**
     * Creates params array for create query
     *
     * @param  array $data key => value where key is column name
     * @return array
     */
    protected function createInsertParams($data)
    {
        $parameters = [];
        foreach ($this->safeAttributes() as $item) {
            $parameters[":$item"]=$data[$item];
        }
        return $parameters;
    }

    /**
     * Updates model
     *
     * @param  array $data key => value attrubute data where key is column name 
     * @return boolean
     */
    public function update($data)
    {
        $db = static::getDB();
        $this->setAttributes($data);
        if ($this->validate()) {
            $updateAttributes = [];
            foreach ($data as $key => $item) {
                if (in_array($key, $this->safeAttributes())) {
                    $updateAttributes[] = $key;
                }
            }
            $attributesPrepeare = array_map(
                function ($item) {
                    return "$item = :$item"; 
                }, $updateAttributes
            );
            $attributesPrepeare = implode(',', $attributesPrepeare);
            $sql = "UPDATE ".$this->tableName()." SET ".$attributesPrepeare." WHERE id = ".$this->id;
            $parameters = [];
            foreach ($updateAttributes as $item) {
                $parameters[":$item"]=$data[$item];
            }
            $query = $db->prepare($sql);
            return $query->execute($parameters);
        }
        return false;
    }

    /**
     * Deletes model
     *
     * @param  array $data key => value attrubute data where key is column name 
     * @return boolean
     */
    public function delete()
    {
        $db = static::getDB();
        $sql = "DELETE FROM ".$this->tableName()." WHERE id = :id LIMIT 1";
        $query = $db->prepare($sql);
        $parameters = [':id' => $this->id];
        return $query->execute($parameters);
    }

    /**
     * Finds and returns model
     *
     * @param  integer $id 
     * @return User|boolean
     */
    public function findByPk($id)
    {
        $db = static::getDB();
        $sql = "SELECT * FROM ".$this->tableName()." WHERE id = :id LIMIT 1";
        $query = $db->prepare($sql);
        $parameters = [':id' => $id];
        $query->execute($parameters);
        return $query->fetchObject(get_class($this));
    }

    /**
     * Retrives all records
     *
     * @return array array of users
     */
    public function findAll()
    {
        $db = static::getDB();
        $sql = "SELECT * FROM ".$this->tableName();
        $query = $db->prepare($sql);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_CLASS, get_class($this));
    }

    /**
     * Performs validation, errors are returned in array
     * If attributes are given they will be set first
     *
     * @param  array $data attributes to set
     * @return boolean
     */
    public function validate($data = [])
    {
        if (!empty($data)) {
            $this->setAttributes($data);
        }

        $this->errors=[];
        foreach ($this->rules() as $ruleSet) {
            $method = 'validate'.$ruleSet[1];
            $fields = explode(',', $ruleSet[0]);
            $param = isset($ruleSet[2]) ? $ruleSet[2] : null;
            foreach ($fields as $field) {
                $field = trim($field);
                $result = $this->$method($field, $param);
                if ($result !== true) {
                    $this->addError($field, $result);
                }
            }   
        }
        return empty($this->errors);
    }

    /**
     * Adds error to internal errors array
     *
     * @param string $field  column name
     * @param string $result result of validation (validation msg) 
     */
    protected function addError($field, $result)
    {
        if (!array_key_exists($field, $this->errors)) {
            $this->errors[$field] = [];
        }
        $this->errors[$field][] = $result;
    }

    /**
     * Set attributes on model
     *
     * @param array $data key => value attrubute data where key is column name 
     */
    protected function setAttributes($data)
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * Validation function
     *
     * @param  string $field column name
     * @param  mixed  $param optional parameter
     * @return string|boolean
     */
    protected function validateRequired($field, $param = null)
    {
        if (!isset($this->$field) || $this->$field == '') {
            return "$field is required";
        }
        return true;
    }

    /**
     * Validation function
     *
     * @param  string $field column name
     * @param  mixed  $param optional parameter
     * @return string|boolean
     */
    protected function validateMinlength($field, $param = null)
    {
        if (strlen($this->$field) <= $param) {
            return "$field should have at least $param chars length";
        }
        return true;
    }
}
