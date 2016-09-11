<?php
namespace AmitKhare\InkRouter;
/**
 *
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @link https://github.com/amitkhare/InkRouter
 * @author Amit Kumar Khare <amit@inkimagine.com>
 *
 * InkRouter is an easy to use minimal PHP routing system.
 *
 **/
class InkRouter
{
  private $routes = [];
  private $uri = [];
  private $methodVars = [];
  private $httpMethod = [];
	private $vars;
	private $baseURI="/";
	private $matchTypes = array(
		'{w}'  => '(\w+)',
		'{d}'  => '(\d+)'
	);
  public function __construct($vars=[],$baseURI="/")
  {
		$this->baseURI = $baseURI;
		$this->vars = (object) $vars;
		$this->uri = preg_replace('/^' . preg_quote($baseURI, '/') . '/', '', $_SERVER['REQUEST_URI']);
  }
  public function add($httpMethod,$pattern, $callback,$methodVars=[]) {
			if(substr($pattern, -1) !="/"){
				$pattern .= "/";
			}
			$pattern = "#^".$pattern."?$#";
			$pattern = str_replace(array_keys($this->matchTypes), $this->matchTypes, $pattern);
			if(!empty($methodVars)){
				$this->methodVars = $methodVars;
			}
      $this->routes[$pattern] = $callback;
      $this->httpMethod[$pattern] = strtoupper($httpMethod);
  }
  public function dispatch() {
			foreach ($this->routes as $pattern => $callback) {
				    if (preg_match($pattern, $this->uri, $params) === 1) {
							if(strpos($this->httpMethod[$pattern], $_SERVER['REQUEST_METHOD']) === false){
								return call_user_func(function() {
									echo "Method not allowed";
								}, array_values($params));
							}
							array_shift($params);
							if(is_string($callback)){
								if(count($cb = explode("#",$callback))>1){
										$callback = $cb[0];
										$paramvars = explode("|",$cb[1]);
								} else {
									$paramvars = [];
								}
								if(count($paramvars)!=count($params)){
									die('missing param on route '.$pattern);
								}
								foreach ($params as $key => $value) {
									$this->methodVars[$paramvars[$key]] = $value;
								}
								return $this->callMethod($callback,$this->methodVars);
							} else {
								return call_user_func_array($callback, array_values($params));
							}
          }
      }
			return call_user_func(function() {
				echo "page not found";
			}, array_values($params));
  }
	private function callMethod($callback,$methodVars=[]){
		$callback = explode(":",$callback);
		$class = $callback[0];
		$method = $callback[1];
		$cls = new $class($this->vars);
		return $cls->$method((object)$methodVars);
	}
}
