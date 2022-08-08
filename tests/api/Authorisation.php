<?php

class Authorisation
{
    public static function setAuthorisationToken(ApiTester $I) {
        $cookie = new Symfony\Component\BrowserKit\Cookie('auth_token', 'rep');
        $I->getClient()->getCookieJar()->set($cookie);

    }

}
