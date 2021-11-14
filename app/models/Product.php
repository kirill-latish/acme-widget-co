<?php

class Product extends Model
{
    static protected $collectionName = 'product';
    static protected $pk = 'code';
    protected $rules = [
        'code' => 'required',
        'price' => 'required|numeric|positive'
    ];

    public function __construct($params)
    {
        $this->setPrice($params['price']??null);
        $this->setCode($params['code']??null);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->attributes['code'];
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        return $this->attributes['price'];
    }

    /**
     * @param mixed $code
     */
    public function setCode($code): Product
    {
        $this->attributes['code'] = $code;
        return $this;
    }

    /**
     * @param mixed $price
     */
    public function setPrice($price): Product
    {
        $this->attributes['price'] = $price;
        return $this;
    }



}