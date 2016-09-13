<?php
namespace AmitKhare\EasyRouter;
/**
 *
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @link https://github.com/amitkhare/easy-router
 * @author Amit Kumar Khare <amit@inkimagine.com>
 *
 * EasyRouter is an easy to use minimal PHP routing system.
 *
 **/
class EasyRouter {
    private $baseURI="/";
    private $classVars;
    private $methodVars;
    private $uri = [];
    private $routes = [];
    private $httpMethod = [];
    private $matchPattern = array(
        'w'  => '(\w+)',
        'd'  => '(\d+)',
        'any' => '([^/]+)',
        'num' => '([0-9]+)',
        'all' => '(.*)',
        'i'  => '([0-9]++)',
        'a'  => '([0-9A-Za-z]++)',
        'h'  => '([0-9A-Fa-f]++)',
        '*'  => '(.+?)',
        '**' => '(.++)'
    );
    public $error_callback;
    
    public function __construct($classVars=[],$baseURI="/") {
        $this->baseURI = $baseURI;
        $this->classVars = (object) $classVars;
        $this->uri = preg_replace('/^' . preg_quote($baseURI, '/') . '/', '', $_SERVER['REQUEST_URI']);
    }
    public function add($httpMethod,$pattern,$callback,$methodVars=[]){
    	//$pattern = "/product/{category:any}/{id:num}/{page:d}/";
        $patternArr = $this->prepairPattern($pattern);
        $patternArr['callback'] = $callback;
        $patternArr['httpMethod'] = strtoupper($httpMethod);
        $patternArr['methodVars'] = $methodVars;
        $this->routes[] = $patternArr;
    }
    
     public function dispatch() {
     	foreach ($this->routes as $route) {
     		
     		if (preg_match($route['pattern'], $this->uri, $params) === 1) {
     			//print_r($route);die;
     			array_shift($params);
     			
     			if(strpos($route['httpMethod'], $_SERVER['REQUEST_METHOD']) === false){
					return $this->methodNotAllowed();
				}
				if(is_string($route['callback'])){
					$i=0;
					foreach ($route['params'] as $key => $value) {
						$this->methodVars[$key]  =$params[$i];
						$i++;
				
					}
					foreach ($route['methodVars'] as $key => $value) {
						$this->methodVars[$key]  =$value;
					}
					return $this->callMethod($route['callback'],$this->methodVars);
				} else {
					return call_user_func_array($route['callback'], array_values($params));
				}
     		}
     	}
     	
	      if (!$this->error_callback) {
	        $this->error_callback = function() {
	          header($_SERVER['SERVER_PROTOCOL']." 404 Not Found");
	          include(__DIR__."/includes/404.php");
	        };
	      } else {
	        if (is_string($this->error_callback)) {
		          return $this->callMethod($this->error_callback,[]);
		        }
	      }
	      call_user_func($this->error_callback,[]);
     }
    
    
    private function prepairPattern($pattern){
        if(substr($pattern, -1) !="/"){
    		$pattern .= "/";
    	}
    	
    	$pattern = "#^".$pattern."?$#";
    	$paramst1 =[];
    	$paramst2 =[];
    	preg_match_all("({(\w+):(\w+)})",$pattern,$out,PREG_PATTERN_ORDER);
		foreach ($out[0] as $key=>$value) {
			$val = str_replace(array_keys($this->matchPattern), $this->matchPattern, $out[2][$key]);
			$paramst1[$out[1][$key]] = $val;
			$paramst2[$out[0][$key]] = ['var'=>$out[1][$key],'pattern'=>$val];;
		}
	   foreach ($paramst2 as $key=>$value) {
	   		$pattern = str_replace($key,$value['pattern'],$pattern);
	   }
	   
	   $result['pattern'] = $pattern;
	   $result['params'] = $paramst1;
	   return $result;
    }
    
    private function callMethod($callback,$methodVars=[]){
		$callback = explode(":",$callback);
		$class = $callback[0];
		$method = $callback[1];
		$cls = new $class($this->vars);
		return $cls->$method((object)$methodVars);
	}
	
	public function methodNotAllowed(){
		header($_SERVER['SERVER_PROTOCOL']." 405 Method Not Allowed");
		include(__DIR__."/includes/405.php");
	}
	
	public function error404($callback) {
	    $this->error_callback = $callback;
	}
}
