<?php

require "vendor/autoload.php";

class NegotiationTest extends \PHPUnit_Framework_TestCase{
    public function testLanguage(){
        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = "en, fr-be;q=0.8, nl-be;q=0.7";
        $ln = new \tdt\negotiators\LanguageNegotiator();
        $this->assertEquals("en", $ln->pop());
        $this->assertEquals("fr", $ln->pop());
        $this->assertEquals("nl", $ln->pop());

        $_GET["lang"] = "nl";
        $ln = new \tdt\negotiators\LanguageNegotiator();
        $this->assertEquals("nl", $ln->pop());
    }

    public function testContent(){
        $_SERVER['HTTP_ACCEPT'] = "text/html, application/json;q=0.8, text/csv;q=0.7";
        $ln = new \tdt\negotiators\ContentNegotiator();
        $this->assertEquals("html", $ln->pop());
        $this->assertEquals("json", $ln->pop());
        $this->assertEquals("csv", $ln->pop());
    }
}
