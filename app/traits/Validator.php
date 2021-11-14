<?php

trait Validator {

    public function validate()
    {
        foreach($this->rules as $fieldToValidate => $rules){

            $this->validateAttribute($fieldToValidate,$rules);

        }
    }

    public function validateAttribute($fieldToValidate,$rules)
    {
        $value = $this->attributes[$fieldToValidate]??null;

        $rules  = explode('|',$rules);

        foreach ($rules as $rule){

            list($method,$parameter) = explode(":",$rule);

            if(!method_exists(get_called_class(),$method)) continue;

            call_user_func_array([$this,$method],[$fieldToValidate,$value,$parameter]);
        }
    }

    public function required($field,$value = null,$param = null)
    {
        if(!isset($this->attributes[$field]) || is_null($this->attributes[$field]??null)){

            throw new Exception('Validation failed. Field '.$field.' is required');
        }
    }

    public function numeric($field,$value = null,$param = null)
    {
        if(!is_numeric($this->attributes[$field])){

            throw new Exception('Validation failed. Field '.$field.' must be numeric');
        }
    }

    public function positive($field,$value = null,$param = null)
    {
        if($this->attributes[$field]<=0){

            throw new Exception('Validation failed. Field '.$field.' must be positive');
        }
    }

}
