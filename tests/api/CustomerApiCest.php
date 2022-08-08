<?php

require_once 'Authorisation.php';

class CustomerApiCest
{
    public function _before(ApiTester $I)
    {
    }

    // Testing that the custoomer collection is properly retrieved
    public function testGetCollection(ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        Authorisation::setAuthorisationToken($I);
        $I->sendGet('/productionplan');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'period' => 'integer',
            'employeeNumber' => 'string',
            'model' => 'string',
            'type' => 'string',
            'numberOfSki' => 'string'
        ]);
        $I->assertEquals(17, count(json_decode($I->grabResponse())));
        $I->seeResponseContainsJson(array('period' => '2020-02-01', 'employeeNumber' => 1, 'model' => 'redline', 'type' => 'classic', 'numberOfSki'=> 1300),);
    }

    // Testing that the filtering works properly
    public function testFilteredGetCollection(ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        Authorisation::setAuthorisationToken($I);
        $I->sendGet('/customer/order');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'customerID' => 'integer',
            'name' => 'string',
            'startDate' => 'string',
            'endDate' => 'string'
        ]);
        $I->assertEquals(5, count(json_decode($I->grabResponse())));
        $I->seeResponseContainsJson(array('customerID' => 2, 'name' => 'Kari Olsen','starDate' => '2010-01-01', 'endDate' => '2030-01-01'), );
    }

    // Testing that a customer resource is properly retrieved
    public function testGetResource(ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        Authorisation::setAuthorisationToken($I);
        $I->sendGet('/customer/order?since=');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'customerID' => 'integer',
            'name' => 'string',
            'startDate' => 'string',
            'endDate' => 'string'
        ]);
        $I->seeResponseContainsJson(array('customerID' => 4, 'name' => 'Oda Nilsen','starDate' => '2020-12-01', 'endDate' => '2030-12-01'), );
    }

    // Testing that a customer resource is properly retrieved
    public function testGetResources(ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        Authorisation::setAuthorisationToken($I);
        $I->sendGet('/customer/order/');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'customerID' => 'integer',
            'name' => 'string',
            'startDate' => 'string',
            'endDate' => 'string'
        ]);
        $I->seeResponseContainsJson(array('customerID' => 4, 'name' => 'Oda Nilsen','starDate' => '2020-12-01', 'endDate' => '2030-12-01'), );
    }

   /* public function testNoAuthorisationToken(ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendGet('/customer');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::FORBIDDEN);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson(array('error_code' => 403,
            'title' => 'Forbidden',
            'detail' => 'The client is not authorized to perform the requested operation',
            'instance' => RESTConstants::API_URI . '/'));
    }*/

    // Testing successful creation of customer resource
    public function testCreateNormal(ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        Authorisation::setAuthorisationToken($I);
        $I->sendPost('/customer', ['customerID' => '2', 'name' => 'Kari Olsen']);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::CREATED); // 201
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'customerID' => 'integer',
            'name' => 'string',
            'startDate' => 'string',
            'endDate' => 'string'
        ]);
        $I->seeResponseContainsJson(array('customerID' => 2, 'name' => 'Kari Olsen','starDate' => '2010-01-01', 'endDate' => '2030-01-01'));
        $I->seeInDatabase('customer', ['customerID' => 2, 'name' => 'Kari Olsen','starDate' => '2010-01-01', 'endDate' => '2030-01-01']);
    }

    // Testing proper error response when the client passes an unknown county name
    public function testCreateInvalidCounty(ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        Authorisation::setAuthorisationToken($I);
        $I->sendPost('/customer', ['customerID' => '2', 'name' => 'Kari Olsen']);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::BAD_REQUEST);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson(array('error_code' => 400,
            'title' => 'Bad Request',
            'detail' => 'No matching county resource found',
            'instance' => RESTConstants::API_URI . '/customer'));
    }



}
