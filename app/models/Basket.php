<?php


class Basket extends Model
{

    static protected $collectionName = 'basket';
    static protected $pk = 'userId';
    protected $rules = [
        'userId' => 'required',
        'items' => 'required',
        'deliveryChargeRules' => 'required',
        'offers' => '',
    ];

    protected $totalPrice = 0;
    protected $itemsPrice = 0;
    protected $deliveryPrice = 0;

    public function __construct($params)
    {
        $this->setUserId($params['userId']??null);
        $this->setCatalog($params['catalog']??[]);
        $this->setDeliveryChargeRules($params['deliveryChargeRules']??[]);
        $this->setOffers($params['offers']??[]);
    }

    public function setUserId($id)
    {
        $this->attributes['userId'] = $id;
        return $this;
    }

    public function getUserId()
    {
        return $this->attributes['userId']??null;
    }

    public function setCatalog(array $codes)
    {
        foreach($codes as $code){
            $this->add($code);
        }

        return $this;
    }

    public function setDeliveryChargeRules($rules)
    {
        $this->attributes['deliveryChargeRules'] = $rules;
        return $this;
    }

    public function setOffers($offers)
    {
        $this->attributes['offers'] = $offers;
        return $this;
    }

    public function add($productCode, $quantity = 1)
    {
       $product = Product::find($productCode);

       for($i = 1; $i<=$quantity; $i++){
           $this->attributes['items'][] = $product;
       }
    }

    public function total()
    {
        return  $this->calculateItemsPrice()+$this->calculateDeliveryPrice();
    }

    public function calculateDeliveryPrice()
    {
        $deliveryChargeRules = $this->attributes['deliveryChargeRules']??[];

        if(!count($deliveryChargeRules)) return;

        $variables = [
            'itemsPrice' => $this->calculateItemsPrice()
        ];

        $search = array_values(array_keys($variables));
        $replace = array_values($variables);


        foreach ($deliveryChargeRules as $deliveryChargeRule){

            $conditions = $deliveryChargeRule['conditions'];
            $price = $deliveryChargeRule['price'];

            $conditions = str_replace($search,$replace,$conditions);
            if(eval('return '.$conditions.';')){
                return $price;
            }
        }

        return 0;
    }

    public function calculateItemsPrice()
    {
        $totalPrice = 0;

        foreach($this->attributes['items'] as $product){
            $totalPrice += $product->getPrice();
        }

        $discount = 0;

        foreach($this->attributes['offers']??[] as  $offer){
            if($offer && !method_exists(get_called_class(),$offer)) continue;

            $discount += call_user_func([$this,$offer]);
        }

        return $totalPrice+$discount;
    }

    public function eachSecondRedHalfPrice()
    {
        $discount = 0;
        $redItems = 0;

        foreach($this->attributes['items'] as $product){

            if($product->getCode()=='R01'){
                $redItems++;

                if($redItems>1 && $redItems%2==0){
                    $discount-=round($product->getPrice()/2,2);
                }
            }

        }

        return $discount;
    }
}