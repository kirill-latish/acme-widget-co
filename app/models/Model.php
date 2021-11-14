<?php

class Model extends RedisRepository
{
    use Validator;
    protected $rules = [];
    protected $attributes = [];

    static public function find($id)
    {
        $data = parent::find($id);

        $instance = null;

        $className = get_called_class();

        if($data){
            $instance = new $className($data);
        }

        return $instance;
    }

    public function getPK()
    {
        return $this->attributes[static::$pk]??null;
    }

    public function save()
    {
        $this->validate();
        parent::save();
    }



}