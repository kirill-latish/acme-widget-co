#Acme Widget Co

This repository contains realization of next entities:

**Product** (code, price)

**Basket** (userId, items, delivery charge rules, offers)

To launch this code you need to execute next steps:
1. Clone this repository
```
git clone git@github.com:kirill-latish/acme-widget-co.git
```
2. Go to acme-widget-co and build docker containers
```
cd acme-widget-co && docker-compose build
```

3. Run containers
```
docker-compose up -d
```

5. Connect to php container and install composer dependencies
```
docker exec -it acme-widget-co_php_1 bash 
composer install
```
6. Now we can create needed products:
```php
$redWidget = new Product(['code' => 'R01','price' => 32.95]);
$redWidget->save();
$greenWidget = new Product(['code' => 'G01','price' => 24.95]);
$greenWidget->save();
$blueWidget = new Product(['code' => 'B01','price' => 7.95]);
$blueWidget->save();
```

If you have specific requirements to the produt properties you can specify them with help of rules:
```php

class Product {
 ...
 protected $rules = [
        'code' => 'required',
        'price' => 'required|numeric|positive'
    ];
...
}
```
If you don't see needed rule, you can add it by adding new method to 
app/traits/Validator.php 
Here is small example of the method that check if number is positive:
```php
trait Validator{
...

 public function positive($field,$value = null,$param = null)
    {
        if($this->attributes[$field]<=0){

            throw new Exception('Validation failed. Field '.$field.' must be positive');
        }
    }
...
}

```



*Products will be stored in Redis ( this decision was made to favor 
the speed ). To guarantee persistence I turned on sync on disk 
(both kinds of synchronizations are turned on: RDB and AOF).*

*FYI: Code was organized in a way that allow easily change data repository.*

7. And now we can create basket itself:
```php
$deliveryRules = [
    ['conditions'=>'itemsPrice<50','price'=>4.95],
    ['conditions'=>'itemsPrice>=50 and itemsPrice<90','price'=>2.95],
    ['conditions'=>'itemsPrice>=90','price'=>0],
];

$offers = ['eachSecondRedHalfPrice'];

$basket = new Basket([
    'userId' => 1,
    'catalog'=> ['B01', 'G01'],
    'deliveryChargeRules'=> $deliveryRules ,
    'offers' => $offers
]);

```
*As you might see basket can be initialized with set of product codes, delivery charge rules and special offers.
Delivery charge rules containts two parts - conditions that will be evaluated and price of the delivery that will be used if evaluation will return true*

*Special offers can be set by providing name of the function that calculates price adjustment according to specific conditions*

If you want to add another product to the basket you can do that by calling method add:

```php
$basket->add('R01');
```
You also can and specify quantity of products with help of second parameter:

```php
$basket->add('R01',3);
```

Basket class also has next methods:
```php
calculateItemsPrice() 
calculateDeliveryPrice() 
total() // which is calculateItemsPrice() + calculateDeliveryPrice()
```

Basket was tested on proposed set of products and results are in perfect match:

|Products|Total|
|--------|-----|
|B01, G01|                    $37.85|
|R01, R01|                    $54.37|
|R01, G01|                    $60.85|
|B01, B01, R01, R01, R01|     $98.27|


#CI/CD:

Unit test weren't implemented yet.

For this project I implemented continious deployment through
**buddy.works**. How it works:

1. I make some changes and push them to github
2. Pipeline in buddy.works runs on every push to main branch
3. It connects to tech server, builds all images, described in docker-compose
4. Then it tags this build
5. And then push to docker hub
6. After that it connects to the production server and pulls new container images from the private repository
7. And finally it executes docker-compose restart

