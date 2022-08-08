<?php
namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class Api extends \Codeception\Module
{

    // https://stackoverflow.com/questions/36334244/rest-module-get-set-cookies/36335651
    public function getClient()
    {
        return $this->getModule('REST')->client;
    }

}
