<?php
function dd($data){
    print_r($data);
    die;
};
error_reporting(E_ALL);
ini_set('display_errors', 1 );
// use namespace
use AmitKhare\EasyRouter;

use App\Controllers\PageController;

require __DIR__ . '/../vendor/autoload.php';

require __DIR__ . '/PageController.php';

// Take an instance of Router Class.
// (Optional) set Base URI as second perameter
$baseURI = "/er/example";
$router = new EasyRouter(['var1'=>123],$baseURI);

// URL::GET www.example.com/
$router->add("GET",'/homepage', [PageController::class,"home"])->setName('home');

// URL::GET www.example.com/article/tshirts/323
// anonymous callback function
$router->add("GET",'/article/{category:w}/{id:num}/', function($category, $id) use ($router){
    
    echo $router->pathFor('product',[123])."<br/>";
    
    echo "Category: ".$category."<br/>";
    echo "ID: ".$id;
})->setName('article');

// URL::GET www.example.com/product/222
$router->add("GET",'/product/{id:d}', [PageController::class,"product"])->setName('product');

// URL::POST www.example.com/product
$router->add("POST",'/product', [PageController::class,"product_process"])->setName('product.process');

// URL::GET www.example.com/about/something/
$router->add("GET",'/about/{var1:w}', [PageController::class,"about"],['myname'=>'amitkhare'])->setName('about');

// Dispatch Routes.
$router->dispatch();