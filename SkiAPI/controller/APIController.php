<?php

require_once ('RequestHandler.php');
require_once ('SkiAPI/controller/Endpoints/PublicEndpoint.php');
require_once ('SkiAPI/controller/Endpoints/CustomerEndpoint.php');
require_once ('SkiAPI/controller/Endpoints/TransporterEndpoint.php');
require_once ('SkiAPI/controller/Endpoints/CustomerRepEndpoint.php');
require_once ('SkiAPI/controller/Endpoints/StorekeeperEndpoint.php');
require_once ('SkiAPI/controller/Endpoints/ProductionPlannerEndpoint.php');
require_once ('SkiAPI/db/AuthorisationModel.php');

class APIController extends RequestHandler
{

    /**
     * The constructor defines the valid requests to be the dealers, used cars and report controller endpoints.
     * @see RequestHandler
     */
    public function __construct()
    {
        parent::__construct();
        $this->validRequests[] = RESTConstants::ENDPOINT_CUSTOMER;
        $this->validRequests[] = RESTConstants::ENDPOINT_PUBLIC;
        $this->validRequests[] = RESTConstants::ENDPOINT_TRANSPORTER;
        $this->validRequests[] = RESTConstants::ENDPOINT_CUSTOMERUSER;
    }

    /**
     * Verifies that the request contains a valid authorisation token. The authorisation scheme is quite simple -
     * assuming that there is only one authorisation token for the complete API
     * @param string $token the authorisation token to be verified
     * @param string $endpointPath the request endpoint
     * @throws APIException with the code set to HTTP_FORBIDDEN if the token is not valid
     */
    public function authorise(string $token, string $endpointPath) {
        if (!(new AuthorisationModel())->isValid($token)) {
            throw new APIException(RESTConstants::HTTP_FORBIDDEN, $endpointPath);
        }
    }

    public function handleRequest(array $uri, string $endpointPath, string $requestMethod, array $queries, array $payload): array
    {
        $auth = new AuthorisationModel();

        // Valid requests checked here - valid methods for each request checked in the special endpoint controllers
        $endpointUri = $uri[0];
        if (!$this->isValidRequest($endpointUri)) {
            $res['status'] = RESTConstants::HTTP_NOT_FOUND;
            $res['result'] = "Page Not found";
            return $res;
        }
        $endpointPath .= '/' . $uri[0];
        switch ($endpointUri)  {
            case RESTConstants::ENDPOINT_CUSTOMERUSER:
                if ($auth->hasAccess($_COOKIE['auth_token'], $endpointUri)) {
                    $endpoint  = new CustomerEndpoint();
                }
                break;
            case RESTConstants::ENDPOINT_TRANSPORTER:
                if ($auth->hasAccess($_COOKIE['auth_token'], $endpointUri)) {
                    $endpoint = new TransporterEndpoint();
                }
                break;
            case RESTConstants::ENDPOINT_PUBLIC:
                $endpoint  = new PublicEndpoint();
                break;
            case RESTConstants::ENDPOINT_CUSTOMER:
                if ($auth->hasAccess($_COOKIE['auth_token'], $endpointUri)) {
                    $employeeType = $auth ->employeeType($_COOKIE['auth_token']);
                    if ($employeeType == "Storekeeper"){
                        $endpoint = new StorekeeperEndpoint();
                    }elseif ($employeeType == "ProductionPlanner"){
                        $endpoint = new ProductionPlannerEndpoint();
                    }
                    elseif ($employeeType == "CustomerRepresentative"){
                        $endpoint = new CustomerRepEndpoint();
                    }
                }
        }
        if (isset($endpoint)){
            return $endpoint->handleRequest(array_slice($uri, 1), $endpointPath, $requestMethod, $queries, $payload);
        }else{
            $res = array();
            $res['result'] = "Not authorized";
            $res['status'] = RESTConstants::HTTP_NOT_AUTHORIZED;
            return $res;
        }
    }
}