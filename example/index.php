<?php

// autoload via composer
require __DIR__.'/../vendor/autoload.php';

// use namespace
use AmitKhare\EasyRouter\EasyRouter;

// Take an instance of Router Class.
// set optional Base URI as second perameter
$router = new EasyRouter(['var1'=>123],"/example");

// URL::GET www.example.com/
$router->add("GET",'/', "Page:home");

// URL::GET www.example.com/product/222
$router->add("GET",'/product/{id:d}', "Page:product");

// URL::POST www.example.com/product
$router->add("POST",'/product', "Page:product_process");

// URL::GET www.example.com/about/some-thing/
$router->add("GET",'/about/{var1:any}', "Page:about",['myname'=>'amitkhare']);

// Dispatch Routes.
$router->dispatch();




// ###############################################
// ############## EXAMPLE CLASS ##################
// ###############################################

class Page {

	public function __construct($vars=[]) {
		foreach ($vars as $key => $value) {
			$this->$key= $value;
		}
	}
	public function get($vars)
	{
		echo $this->db."<br/>";
		echo $this->var1."<br/>";
		echo $this->var1."<br/>";

		print_r($vars);
	}

	public function about($vars)
	{
		echo $vars->var1."<br/>";
		echo $vars->myname."<br/>";
	}

	public function product($vars)
	{
		echo " ID: ".$vars->id;
	}

	public function home()
	{
		echo "this is home.";
	}

	public function product_process()
	{
		echo "this will show only if accessed via POST method.";
	}
}
