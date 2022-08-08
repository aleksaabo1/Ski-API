<?php

require_once 'Authorisation.php';

class OrderApiCest
{
    public function _before(ApiTester $I)
    {
    }

    // Testing that the orders collection is properly retrieved
    public function testGetCollection(ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        Authorisation::setAuthorisationToken($I);
        $I->sendGet('/order?state=new');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'orderNumber' => 'integer',
            'quantity' => 'integer',
            'totalPrice' => 'integer',
            'state' => 'string',
            'date' => 'string'

        ]);
        $I->assertEquals(17, count(json_decode($I->grabResponse())));
        $I->seeResponseContainsJson(array('orderNumber' => 605083, 'quantity' => 1, 'totalPrice' => 10000, 'state' => 'new', 'date' => '2021-04-25') );
    }

    // Testing that the filtering works properly
    public function testFilteredGetCollection(ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        Authorisation::setAuthorisationToken($I);
        $I->sendGet('/order/605083/state=new');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'orderNumber' => 'integer',
            'quantity' => 'integer',
            'totalPrice' => 'integer',
            'state' => 'string',
            'date' => 'string'
        ]);
        $I->assertEquals(5, count(json_decode($I->grabResponse())));
        $I->seeResponseContainsJson(array('orderNumber' => 605083, 'quantity' => 1, 'totalPrice' => 10000, 'state' => 'new', 'date' => '2021-04-25'));
    }

  /*  // Testing that a dealer resource is properly retrieved
    public function testGetResource(ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        Authorisation::setAuthorisationToken($I);
        $I->sendGet('/dealers/13');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'orderNumber' => 'integer',
            'quantity' => 'integer',
            'totalPrice' => 'integer',
            'state' => 'string',
            'date' => 'string'
        ]);
        $I->seeResponseContainsJson(array('id' => 13, 'city' => 'Otta', 'county' => 'Innlandet'), );
    }

    public function testNoAuthorisationToken(ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendGet('/dealers');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::FORBIDDEN);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson(array('error_code' => 403,
            'title' => 'Forbidden',
            'detail' => 'The client is not authorized to perform the requested operation',
            'instance' => RESTConstants::API_URI . '/'));
    }
*/
}
