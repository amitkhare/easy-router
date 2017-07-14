<?php

// use namespace
use AmitKhare\EasyRouter;

use App\Controllers\PageController;

// Take an instance of Router Class.
// (Optional) set Base URI as second perameter
$baseURI = "/nakul";
$router = new EasyRouter(['var1'=>123],$baseURI);

// URL::GET www.example.com/
$router->add("GET",'/', [PageController::class,"home"]);

// URL::GET www.example.com/article/tshirts/323
// anonymous callback function
$router->add("GET",'/article/{category:w}/{id:num}/', function($category, $id){
    echo "Category: ".$category."<br/>";
    echo "ID: ".$id;
});

// URL::GET www.example.com/product/222
$router->add("GET",'/product/{id:d}', [PageController::class,"product"]);

// URL::POST www.example.com/product
$router->add("POST",'/product', [PageController::class,"product_process"]);

// URL::GET www.example.com/about/something/
$router->add("GET",'/about/{var1:w}', [PageController::class,"about"],['myname'=>'amitkhare']);

// Dispatch Routes.
$router->dispatch();
