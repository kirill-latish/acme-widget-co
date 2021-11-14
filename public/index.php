<?php

require_once dirname(__FILE__)."/../app/conf/main.php";

spl_autoload_register(function ($className) {

    $appDirectories = array_diff(scandir(ROOT_PATH), array('.', '..'));

    foreach($appDirectories as $appDirectory){

        $fileName = stream_resolve_include_path(ROOT_PATH.'/'.$appDirectory.'/' . $className . '.php');

        if ($fileName !== false) {
            include $fileName;
        }
    }
});

$redWidget = new Product(['code' => 'R01','price' => 32.95]);
$redWidget->save();
$greenWidget = new Product(['code' => 'G01','price' => 24.95]);
$greenWidget->save();
$blueWidget = new Product(['code' => 'B01','price' => 7.95]);
$blueWidget->save();

$deliveryRules = [
    ['conditions'=>'itemsPrice<50','price'=>4.95],
    ['conditions'=>'itemsPrice>=50 and itemsPrice<90','price'=>2.95],
    ['conditions'=>'itemsPrice>=90','price'=>0],
];

$offers = ['eachSecondRedHalfPrice'];

$b1 = new Basket([
    'userId' => 1,
    'catalog'=> ['B01', 'G01'],
    'deliveryChargeRules'=> $deliveryRules ,
    'offers' => $offers
]);
var_dump($b1->total());

$b2 = new Basket([
    'userId' => 1,
    'catalog'=> ['R01', 'R01'],
    'deliveryChargeRules'=> $deliveryRules ,
    'offers' => $offers
]);

var_dump($b2->total());
$b3 = new Basket([
    'userId' => 1,
    'catalog'=> ['R01', 'G01'],
    'deliveryChargeRules'=> $deliveryRules ,
    'offers' => $offers
]);
var_dump($b3->total());

$b4 = new Basket([
    'userId' => 1,
    'catalog'=> ['B01','B01','R01', 'R01','R01'],
    'deliveryChargeRules'=> $deliveryRules ,
    'offers' => $offers
]);
var_dump($b4->total());



/*
R01, R01
R01, G01
B01, B01, R01, R01, R01
*/