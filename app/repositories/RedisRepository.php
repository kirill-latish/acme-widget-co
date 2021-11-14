<?php

require_once dirname(__FILE__).'/../../vendor/predis/predis/autoload.php';

class RedisRepository extends AbstractRepository
{
    static protected $client;
    protected $attributes;

    static public function client()
    {
        if(!isset( static::$client) ) {

            $client = new \Predis\Client([
                'host' => 'redis',
                'port' => 6379,
            ]);

            static::$client = $client;
        }

        return static::$client;
    }

    static public function find($id)
    {
        $key = static::buildKey($id);

        $res = static::client()->get($key);

        if($res){
            $res = unserialize($res);
        }

        return $res;
    }

    public function save()
    {
        $key = static::buildKey($this->getPK());
        return static::client()->set($key,serialize($this->attributes));
    }

    public function delete()
    {
        $key = static::buildKey($this->getPK());
        return static::client()->del($key);
    }
}