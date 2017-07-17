<?php
namespace AmitKhare;
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */
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
    private $tempPattern=null;
    private $baseURL=null;
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
        $this->baseURL = $_SERVER['HTTP_HOST'].$baseURI;
    }
    public function add($httpMethod,$pattern,$callback,$methodVars=[]){
        //$pattern = "/product/{category:any}/{id:num}/{page:d}/";
        $patternArr = $this->prepairPattern($pattern);
        $patternArr['callback'] = $callback;
        $patternArr['httpMethod'] = strtoupper($httpMethod);
        $patternArr['methodVars'] = $methodVars;
        $this->routes[] = $patternArr;
        
        $this->tempPattern = $this->prepairPattern($pattern)['pattern'];
        return $this;
    }

    public function get($pattern,$callback,$methodVars=[]){
        $this->add("GET",$pattern,$callback,$methodVars);
    }

    public function post($pattern,$callback,$methodVars=[]){
        $this->add("POST",$pattern,$callback,$methodVars);
    }

    public function put($pattern,$callback,$methodVars=[]){
        $this->add("PUT",$pattern,$callback,$methodVars);
    }

    public function delete($pattern,$callback,$methodVars=[]){
        $this->add("DELETE",$pattern,$callback,$methodVars);
    }

    public function setName($name){
        
        foreach ($this->routes as $k => $route) {
            
            if($route['pattern'] == $this->tempPattern){
                $this->routes[$k]['name'] = $name;
                $this->tempPattern = null;
            }
            
        }
    }
    
    public function pathFor($name,$data=[]){
        $pattern = "";
        foreach ($this->routes as $route) {
            if(isset($route['name']) && $route['name'] == $name)
                $pattern = $route['pattern'];
        }
        
        return $this->generateURLforPattern($pattern,$data);
        
    }
    
    private function generateURLforPattern($patternRAW,$data=[]){
        
        $pattern = explode("/",$patternRAW);
        
        $pattern = array_slice($pattern, 1, -1);
        
        
        
        $j = 0;
        foreach ($pattern as $k => $v) {
            if(substr($v, 0,1) == "(" && substr( $v,-1) == ")") {
                $j++;
            }
        }
        
        // TODO:: check if right data segments is provided or not. 
        // alert user
        
        if($j > count($data)){
            die("Please provide proper segment(s). Only ". count($data) . " provided but ". $j . " segments needed. pathFor [".$patternRAW."]");
        }
        
        
        $i=0;
        foreach ($pattern as $k => $v) {
            if(substr($v, 0,1) == "(" && substr( $v,-1) == ")") {
                // is expression\
                $pattern[$k] = $data[$i];
                $i++;
            }
        }
        
        
        foreach ($pattern as $segment) {
            $URL = $this->baseURL .="/".$segment;
        }
        
        return $URL;
        
    }
    
    
     public function dispatch() {
        foreach ($this->routes as $route) {
            
            if (preg_match($route['pattern'], $this->uri, $params) === 1) {
                
                array_shift($params);
                
                if(strpos($route['httpMethod'], $_SERVER['REQUEST_METHOD']) === false){
                    return $this->methodNotAllowed();
                }
                if(is_array($route['callback'])){
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
        $this->classVars->router = $this; // also provide router class
        $class = $callback[0];
        $method = $callback[1];
        $cls = new $class($this->classVars);
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
