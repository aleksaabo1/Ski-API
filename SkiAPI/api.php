<?php
require_once 'controller\APIController.php';
require_once 'db/OrderModel.php';
require_once 'db/SkiTypeModel.php';
require_once 'db/ShipmentModel.php';
require_once 'db/AuthorisationModel.php';
require_once 'Controller/Endpoints/TransporterEndpoint.php';
require_once 'Controller/Endpoints/CustomerRepEndpoint.php';
require_once 'errors.php';
require_once ('SkiAPI/controller/RequestHandler.php');
header('Content-Type: application/json');





// Parse request parameters
$queries = array();
if (!empty($_SERVER['QUERY_STRING'])) {
    parse_str($_SERVER['QUERY_STRING'], $queries);
    $querySince = explode( ',', $queries['since'] ?? null);
    $querySince = explode( ',', $queries['state'] ?? null);
    $queryModel = explode( ',', $queries['model'] ?? null);
    $queryGrip = explode( ',', $queries['grip'] ?? null);
    $querySet = explode( ',', $queries['setstate'] ?? null);
    $querySet = explode( ',', $queries['product'] ?? null);

    unset($queries['request']);
}





$path = $_SERVER['PHP_SELF'];
$path = ltrim($path, "/");
$uri = explode( '/', $path);


$requestMethod = $_SERVER['REQUEST_METHOD'];


$content = file_get_contents('php://input');
if (strlen($content) > 0) {
    $payload = json_decode($content, true);
} else {
    $payload = array();
}


$token = $_COOKIE['auth_token'] ?? '';

try {
    $res = array();

    $controller = new APIController();

    $controller->authorise($token, RESTConstants::API_URI . '/');




    $res = $controller->handleRequest($uri, RESTConstants::API_URI, $requestMethod, $queries, $payload);


    if (count($res) != 0) {
        http_response_code($res['status']);
        print(json_encode($res));
    }

// Handle application exceptions
} catch (APIException | BadRequestException $e){
}

