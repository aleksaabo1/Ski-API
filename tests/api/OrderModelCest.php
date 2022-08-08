<?php


namespace api;


use ApiTester;

class OrderModelCest
{

    public function testGetCollection(ApiTester $I){
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendGet('/ski?model=redline');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsJson();
        $I->assertEquals(6, count(json_decode($I->grabResponse())));
        $I->seeResponseContainsJson(array('Model' => 'redline', 'Type' => 'skate', 'Temperature' => 'warm', 'Grip System' => 'wax'), );
    }



}