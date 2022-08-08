<?php
require_once 'SkiAPI/RESTConstants.php';
require_once 'SkiAPI/controller/APIController.php';
require_once 'SkiAPI/db/SkiTypeModel.php';
require_once 'SkiAPI/db/OrderModel.php';
require_once 'SkiAPI/db/handleOrder.php';
require_once 'SkiAPI/db/shipmentRecording.php';
require_once 'SkiAPI/db/ShipmentModel.php';



class controllerTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    
    protected function _before()
    {
    }

    protected function _after()
    {
    }

    // tests

    /**
     * @throws APIException
     * @throws BadRequestException
     */
    public function testCollectionGet()
    {

        $_COOKIE['auth_token']="qaz1";
        $uri = ["customer", "order"];
        $endpointPath = "/customer/order";
        $requestMethod = RESTConstants::METHOD_GET;
        $queries = array();
        $payload = array();

        $endpoint = new APIController();
        $res = $endpoint->handleRequest( $uri, $endpointPath,  $requestMethod, $queries, $payload)['result'];
        $this->tester->assertCount(3, $res);
        $resource = $res[0];
        $this->tester->assertEquals('206546', $resource['orderNumber']);
        $this->tester->assertEquals('1', $resource['quantity']);
        $this->tester->assertEquals('2500', $resource['totalPrice']);
        $this->tester->assertEquals('new', $resource['state']);
        $this->tester->assertEquals('2021-05-27 13:31:35', $resource['date']);
    }


    public function testCollectionPost()
    {
        $_COOKIE['auth_token']="qaz2";
        $uri = ["customer", "order"];
        $endpointPath = "/customer/order";
        $requestMethod = RESTConstants::METHOD_POST;
        $queries = array();
        $payload = array(
            0 => Array
            (
                "model" => "raceSpeed",
                "type" => "classic",
                "temperature" => "regular",
                "gripSystem" => "wax",
                "length" => "182",
                 "weight" => "70-80"
         ));

        $endpoint = new APIController();
        $res = $endpoint->handleRequest( $uri, $endpointPath,  $requestMethod, $queries, $payload)['result'];
        $res = $res[0];
        $this->tester->assertEquals(1, $res['quantity']);
        $this->tester->assertEquals(4600, $res['totalPrice']);
        $this->tester->assertEquals('new', $res['state']);

    }



    public function testCollectionDelete()
    {
        $_COOKIE['auth_token']="qaz2";
        $uri = ["customer", "order"];
        $endpointPath = "/customer/order";
        $requestMethod = RESTConstants::METHOD_DELETE;
        $queries = array();
        $payload = array();

        $endpoint = new APIController();
        try {
            $endpoint->handleRequest( $uri, $endpointPath, $requestMethod, $queries, $payload);

            $this->fail("APException was expected");

        } catch (APIException $e) {
            $this->tester->assertEquals(RESTConstants::HTTP_METHOD_NOT_ALLOWED, $e->getCode());
            $this->tester->assertEquals('/customer/order', $e->getInstance());
        }
    }



    public function testCollectionPut()
    {
        $_COOKIE['auth_token']="qaz2";
        $uri = ["customer", "order"];
        $endpointPath = "/customer/order";
        $requestMethod = RESTConstants::METHOD_PUT;
        $queries = array();
        $payload = array();

        $endpoint = new APIController();
        try {
            $endpoint->handleRequest( $uri, $endpointPath, $requestMethod, $queries, $payload);
            $this->fail("APException was expected");
        } catch (APIException $e) {
            $this->tester->assertEquals(RESTConstants::HTTP_METHOD_NOT_ALLOWED, $e->getCode());
            $this->tester->assertEquals('/customer/order', $e->getInstance());
        }
    }



    public function testCollectionPutCustomerrep()
    {
        $_COOKIE['auth_token']="asd3";
        $uri = ["employee", "order", "206546" ];
        $endpointPath = "/employee/order/206546?setstate=open";
        $requestMethod = RESTConstants::METHOD_PUT;
        $queries = array('setstate' => "open") ;
        $payload = array();

        $endpoint = new APIController();
        $res = $endpoint->handleRequest( $uri, $endpointPath,  $requestMethod, $queries, $payload)['result'];
        $resource = $res[0];

        $this->tester->assertArrayHasKey('orderNumber', $resource);
            $this->tester->assertEquals(206546, $resource['orderNumber']);
            $this->tester->assertEquals('open', $resource['state']);

    }



    public function testShipmentPost()
    {
        $_COOKIE['auth_token']="asd3";
        $uri = ["employee", "shipment", "316206"];
        $endpointPath = "/employee/shipment/316206";
        $requestMethod = RESTConstants::METHOD_POST;
        $queries = array();
        $payload = array(
                "transporterID" => 1
            );
        $endpoint = new APIController();
        $res = $endpoint->handleRequest($uri, $endpointPath,  $requestMethod, $queries, $payload)['result'];
        $resource = $res[0];
        $this->tester->assertEquals('ready', $resource['Status']);


    }


    /**
        * @throws APIException
        * @throws BadRequestException
        */
       public function testOrderSplit()
       {
           $_COOKIE['auth_token']="qaz1";
           $uri = ["customer", "order", "779075"];
           $endpointPath = "/customer/order/779075";
           $requestMethod = RESTConstants::METHOD_PUT;
           $queries = array();
           $payload = array();

           $endpoint = new APIController();
           $res = $endpoint->handleRequest( $uri, $endpointPath,  $requestMethod, $queries, $payload)['result'];
           $this->tester->assertCount(2, $res);
           $resource = $res[1];
           $this->tester->assertEquals(1, $resource['quantity']);
           $this->tester->assertEquals(2600, $resource['totalPrice']);
           $this->tester->assertEquals('available', $resource['state']);
       }


       public function testDeleteOrder(){
           $_COOKIE['auth_token']="qaz1";
           $uri = ["customer", "order", "779075"];
           $endpointPath = "/customer/order/779075";
           $requestMethod = RESTConstants::METHOD_DELETE;
           $queries = array();
           $payload = array();

           $endpoint = new APIController();
           $res = $endpoint->handleRequest( $uri, $endpointPath,  $requestMethod, $queries, $payload)['result'];
           $this->tester->assertCount(1, $res);
           $resource = $res[0];
           $this->tester->assertEquals(2, $resource['quantity']);
           $this->tester->assertEquals(10300, $resource['totalPrice']);
           $this->tester->assertEquals('cancelled', $resource['state']);
       }




}
