# amitkhare/php-router

##PHPRouter is an easy to use minimal PHP routing system

## INSTALL
### VIA COMPOSER
```sh
composer require amitkhare/php-router dev-master
```
### VIA GIT
```sh
git clone https://github.com/amitkhare/php-router.git
```



## EXAMPLE USAGE

### MINIMAL EXAMPLE

```sh
<?php

// autoload via composer
require __DIR__.'/../vendor/autoload.php';

// OR WITHOUT COMPOSER
// require __DIR__.'/PATH-TO/PHPRouter.php';

// Take an instance of Router Class.
$router = new AmitKhare\PHPRouter\PHPRouter();

// URL::GET www.example.com/article/tshirts/323
// anonymous callback function
$router->add("GET",'/article/{category:w}/{id:num}/', function($category, $id){
    echo "Category: ".$category."<br/>";
    echo "ID: ".$id;
});

// OR Callback of a class->method()
// URL::GET www.example.com/
$router->add("GET",'/', "Page:home");

// Dispatch Routes.
$router->dispatch();

```

### DETAILED EXAMPLE

```sh
<?php

// autoload via composer
require __DIR__.'/../vendor/autoload.php';

// OR WITHOUT COMPOSER
// require __DIR__.'/PATH-TO/PHPRouter.php';

// use namespace
use AmitKhare\PHPRouter\PHPRouter;

// Take an instance of Router Class.
// (Optional) set Base URI as second perameter : /example
$router = new PHPRouter(['var1'=123],"/example");

// URL::GET www.example.com/
$router->add("GET",'/', "Page:home");

// URL::GET www.example.com/article/tshirts/323
// anonymous callback function
$router->add("GET",'/article/{category:w}/{id:num}/', function($category, $id){
    echo "Category: ".$category."<br/>";
    echo "ID: ".$id;
});

// URL::GET www.example.com/product/222
$router->add("GET",'/product/{id:d}', "Page:product");

// URL::POST www.example.com/product
$router->add("POST",'/product', "Page:product_process");

// URL::GET www.example.com/about/something/
$router->add("GET",'/about/{var1:w}', "Page:about",['myname'='amitkhare']);

// Dispatch Routes.
$router->dispatch();
```

### PAGE CLASS

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

### .HTACCESS FILE

```sh
RewriteEngine On

RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
```
