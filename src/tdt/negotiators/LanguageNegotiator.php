<?php
/**
 * The LanguageNegotiator parses the accept-language header and looks for the best language to return
 * You can use it like a stack:
 * while($ln->hasNext() && !theRightLanguage($lang)){
 *    $lang = $ln->pop();
 * }
 * The first element in the stack will be the most prioritized
 * The languageNegotiator will always return at least one result (the configured default language).
 *
 * @copyright (C) 2011,2013 by iRail vzw/asbl, OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Pieter Colpaert   <pieter@iRail.be>
 */

namespace tdt\negotiators;

class LanguageNegotiator{

    private $stack, $default;
    
    public function __construct($default = ""){
        if($default !== ""){
            $this->default = $default;
        }
        $this->doLanguageNegotiation();
    }

    public function hasNext(){
        return sizeof($this->stack) > 0;
    }
    
    public function pop(){
        return array_shift($this->stack);
    }
    
    private function doLanguageNegotiation(){
        /*
         * Language negotiation means checking the Accept header of the request to decide for the language to use
         */

        // But let's first check if we can add something to add the ease of use in a browser: a GET parameter lang=??
        if(isset($_GET["lang"])){
            $this->stack = array(strtolower(substr($_GET["lang"], 0,2)));
        }else{
            // The real content negotiation starts here
            if(!isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])){
                // if even no config was set, let's choose English, our documentation's language.
                if(isset($this->default)){
                    $this->stack = array($this->default,"en");
                }else{
                    $this->stack = array("en");
                }
            }else{
                $accept = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
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
                    //if we have a *, change it into the default language
                    if($type == "*"){
                        if(isset($this->default)){ 
                            $type = $this->default;
                        }else{
                            $type = "en";
                        }
                    }
                    //now add the language to the array
                    $type = strtolower(substr($type,0,2));
                    $stack[$type] = $q;
                }
                //all that is left for us to do is sorting the array according to their q
                arsort($stack);
                $this->stack = array_keys($stack);
            }
            
        }
        
    }
}