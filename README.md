# amitkhare/inkrouter

##InkRouter is an easy to use minimal PHP routing system

# INSTALL
## VIA COMPOSER
```sh
composer require amitkhare/inkrouter
```
## VIA GIT
```sh
git clone https://github.com/amitkhare/inkrouter.git
```



# EXAMPLE USAGE

## MINIMAL EXAMPLE
<?php

// autoload via composer
require __DIR__.'/../vendor/autoload.php';

// OR WITHOUT COMPOSER
// require __DIR__.'/PATH-TO/InkRouter.php';

// use namespace
use AmitKhare\InkRouter\InkRouter;

// Take an instance of Router Class.
// set Base URI as second perameter : /example
$router = new InkRouter(['var1'=123],"/example");

// URL::GET www.example.com/
$router->add("GET",'/', "Page:home");

// Dispatch Routes.
$router->dispatch();
```
## DETAILED EXAMPLE
```sh
<?php

// autoload via composer
require __DIR__.'/../vendor/autoload.php';

// use namespace
use AmitKhare\InkRouter\InkRouter;

// Take an instance of Router Class.
// set Base URI as second perameter : /example
$router = new InkRouter(['db'='DATABASE','var1'=123],"/example");

// URL::GET www.example.com/
$router->add("GET",'/', "Page:home");

// URL::GET www.example.com/product/222
$router->add("GET",'/product/{d}', "Page:product#id");

// URL::POST www.example.com/product
$router->add("POST",'/product', "Page:product_process");

// URL::GET www.example.com/about/something/
$router->add("GET",'/about/{w}', "Page:about#var1",['myname'='amitkhare']);

// URL::GET www.example.com/page/t-shirts/323
$router->add("GET",'/page/(\w+)/(\d+)/', "Page:get#category|id");

// Dispatch Routes.
$router->dispatch();
```

## PAGE CLASS

```sh
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
```
