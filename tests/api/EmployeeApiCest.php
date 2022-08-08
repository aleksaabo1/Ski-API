<?php

require_once 'Authorisation.php';

class EmployeeApiCest
{
    public function _before(ApiTester $I)
    {
    }

    // Testing that the employees collection is properly retrieved
    public function testGetCollection(ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        Authorisation::setAuthorisationToken($I);
        $I->sendGet('/employee');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'employeeNumber' => 'integer',
            'name' => 'string',
            'department' => 'string'
        ]);
        $I->assertEquals(17, count(json_decode($I->grabResponse())));
        $I->seeResponseContainsJson(array('employeeNumber' => 1, 'name' => 'Oline Nordkvinne', 'department' => 'Storekeeper'), );
    }

    // Testing that the filtering works properly
    public function testFilteredGetCollection(ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        Authorisation::setAuthorisationToken($I);
        $I->sendGet('/employee?name=Atle Pedersen');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'employeeNumber' => 'integer',
            'name' => 'string',
            'department' => 'string'
        ]);
        $I->assertEquals(5, count(json_decode($I->grabResponse())));
        $I->seeResponseContainsJson(array('employeeNumber' => 4, 'name' => 'Atle Pedersen', 'department' => 'Production Planner'), );
    }

    // Testing that a dealer resource is properly retrieved
    public function testGetResource(ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        Authorisation::setAuthorisationToken($I);
        $I->sendGet('/employee/5');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'employeeNumber' => 'integer',
            'name' => 'string',
            'department' => 'string'
        ]);
        $I->seeResponseContainsJson(array('employeeNumber' => 5, 'name' => 'Maja Isdahl', 'department' => 'Production Planner'), );
    }

    /*public function testNoAuthorisationToken(ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendGet('/dealers');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::FORBIDDEN);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson(array('error_code' => 403,
            'title' => 'Forbidden',
            'detail' => 'The client is not authorized to perform the requested operation',
            'instance' => RESTConstants::API_URI . '/'));
    }*/

    // Testing successful creation of dealer resource
    public function testCreateNormal(ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        Authorisation::setAuthorisationToken($I);
        $I->sendPost('/employee', ['name' => 'Atle Pedersen', 'department' => 'Production Planner']);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::CREATED); // 201
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'employeeNumber' => 'integer',
            'name' => 'string',
            'department' => 'string'
        ]);
        $I->seeResponseContainsJson(array('employeeNumber' => 4, 'name' => 'Atle Pedersen', 'department' => 'Production Planner'));
        $I->seeInDatabase('employee', ['employeeNumber' => 4, 'name' => 'Atle Pedersen', 'department' => 'Production Planner']);
    }

    // Testing proper error response when the client passes an unknown county name
    public function testCreateInvalidCounty(ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        Authorisation::setAuthorisationToken($I);
        $I->sendPost('/employee', ['city' => 'Dokka', 'county' => 'Utlandet']);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::BAD_REQUEST);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson(array('error_code' => 400,
            'title' => 'Bad Request',
            'detail' => 'No matching county resource found',
            'instance' => RESTConstants::API_URI . '/employee'));
    }



}
