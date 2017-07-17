<?php

namespace App\Controllers;

class PageController {
	
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
		print_r($this->router->pathFor('article',['fooo',123]));
		//echo "this is home.";
	}
	public function product_process()
	{
		echo "this will show only if accessed via POST method.";
	}
}
