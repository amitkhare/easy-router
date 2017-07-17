# amitkhare/easy-router

##EasyRouter is an easy to use minimal PHP routing system

## INSTALL
### VIA COMPOSER
```sh
composer require amitkhare/easy-router dev-master
```
### VIA GIT
```sh
git clone https://github.com/amitkhare/easy-router.git
```



## EXAMPLE USAGE

### .HTACCESS FILE

```sh
RewriteEngine On

RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
```

### MINIMAL EXAMPLE

```sh
<?php

// autoload via composer
require __DIR__.'/../vendor/autoload.php';

// OR WITHOUT COMPOSER
// require __DIR__.'/PATH-TO/EasyRouter.php';

// Take an instance of Router Class.
$router = new AmitKhare\EasyRouter();

// URL::GET www.example.com/product/10
// anonymous callback function
$router->add('GET','/product/{id:num}', function($id){
	echo  $id;
});

// OR Callback of a class->method()
// URL::GET www.example.com/
$router->add("GET",'/', [Page::class,"home"])->setName('home');

// Dispatch Routes.
$router->dispatch();

```

### DETAILED EXAMPLE

```sh
<?php

use App\Controllers\Page;

// autoload via composer
require __DIR__.'/../vendor/autoload.php';

// OR WITHOUT COMPOSER
// require __DIR__.'/PATH-TO/EasyRouter.php';

// use namespace
use AmitKhare\EasyRouter;

// Take an instance of Router Class.
// (Optional) set Base URI as second perameter
$baseURI = "/";
$router = new EasyRouter(['var1'=>123],$baseURI);

// URL::GET www.example.com/
$router->add("GET",'/', [Page::class,"home"])->setName('home');

// URL::GET www.example.com/article/tshirts/323
// anonymous callback function
$router->add("GET",'/article/{category:w}/{id:num}/', function($category, $id){
    echo "Category: ".$category."<br/>";
    echo "ID: ".$id;
});

// URL::GET www.example.com/product/222
$router->add("GET",'/product/{id:d}', [Page::class,"product"]);

// URL::POST www.example.com/product
$router->add("POST",'/product', [Page::class,"product_process"]);

// URL::GET www.example.com/about/something/
$router->add("GET",'/about/{var1:w}', [Page::class,"about"],['myname'=>'amitkhare']);

// Dispatch Routes.
$router->dispatch();
```

### PAGE CLASS

```sh

namespace App\Controllers;

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
