<?php
/**
 * The ContentNegotiator parses the accept header and looks for the best format requested.
 * You can use it like a stack:
 * while($cn->hasNext() && !theRightFormat($format)){
 *    $format = $cn->pop();
 * }
 * The first element in the stack will be the most prioritized
 *
 * @copyright (C) 2011,2013 by iRail vzw/asbl, OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Pieter Colpaert   <pieter@iRail.be>
 */

namespace tdt\negotiators;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class ContentNegotiator{
    private $default;
    private $log;
    private $stack;
    private $config;
   
   /*
    * Pass config to allow for logging
    * log_dir = dir path to you logging
    */
   
    public function __construct($default = "",array $config = array()){
        $this->config = $config;
        
        /*
         * configure the negotiator with logging parameters, if any provided in the config
         */
        $this->log = new Logger("content_negotiator");
        if(isset($config["log_dir"])){
            $log_dir = rtrim($config["log_dir"],"/");
            $this->log->pushHandler(new StreamHandler($log_dir . "/log_". date('Y-m-d') . ".txt", Logger::INFO));            
        }
        
        
        
        $this->default = $default;
        $this->doContentNegotiation();
    }

    public function hasNext(){
        return sizeof($this->stack) > 0;
    }
    
    public function pop(){
        return array_shift($this->stack);
    }
    
    private function doContentNegotiation(){
        /*
         * Content negotiation means checking the Accept header of the request. The header can look like this:
         * Accept: text/html,application/xhtml+xml,application/xml;q=0.9,* /*;q=0.8
         * This means the agent prefers html, but if it cannot provide that, it should return xml. If that is not possible, give anything.
         */
	if(!isset($_SERVER['HTTP_ACCEPT']) && $this->default !== ""){
            $this->log->addInfo("server and default not set");
            $this->stack = array(strtolower($this->default));
	}else if(!isset($_SERVER['HTTP_ACCEPT'])){
            $this->log->addInfo("accept not set, taking default");
            $this->stack = array("html");
        }else{
            $this->log->addInfo("accept is set, doing content negotiation");
            $accept = $_SERVER['HTTP_ACCEPT'];
            $types = explode(',', $accept);
            //this removes whitespace from each type
            $types = array_map('trim', $types);
            foreach($types as $type){
                $q = 1.0;
                $qa = explode(";q=",$type);
                if(isset($qa[1])){
                    $q = (float)$qa[1];
                }
                $type = $qa[0];
                //throw away the first part of the media type
                $typea = explode("/", $type);
                if(isset($typea[1])){
                    $type = $typea[1];
                }
                $type = strtolower($type);
            
                //default formatter for when it just doesn't care. Probably this is when a developer is just performing tests.
                if($type == "*" && $this->default !== ""){
                    $type = strtolower($this->default);
                }else if($type == "*"){
                    $type = "html";
                }
                //now add the format type to the array, if it hasn't been added yet
                if(!isset($stack[$type])){
                    $stack[$type] = $q;
                }
            }
            //all that is left for us to do is sorting the array according to their q
            arsort($stack);
            $this->stack = array_keys($stack);
        }
    }
}