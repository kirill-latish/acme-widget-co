<?php

abstract class AbstractRepository
{
    static protected $collectionName;
    static protected $pk;

    abstract static function find($id);
    abstract public function save();
    abstract public function delete();

    static public function buildKey($id)
    {
        return static::$collectionName.$id;
    }

}