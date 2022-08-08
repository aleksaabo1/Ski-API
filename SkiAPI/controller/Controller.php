<?php


class Controller
{


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


    /**
     * The Main function starting the API for the client
     * @param array $uri URI that the user request
     * @param string $requestMethod Which method the user uses
     * @param array $queries contains the query
     * @param array $payload contains the body the user is sending
     * @return array return the information the user is requesting
     * @throws APIException
     * @throws BadRequestException
     */
    public function handleRequest(array $uri, string $requestMethod, array $queries, array $payload): array
    {
        $auth = new AuthorisationModel();

        $exception = new Exceptions();


        if (!$exception -> isValidMethod($requestMethod)){
            throw new APIException(RESTConstants::HTTP_METHOD_NOT_ALLOWED, $requestMethod);
        }



        $endpointUri = $uri[0];


        switch ($endpointUri) {
            case RESTConstants::ENDPOINT_CUSTOMERUSER . "/" . RESTConstants::ENDPOINT_ORDER:
                if ($auth->hasAccess($_COOKIE['auth_token'], $endpointUri)) {
                    return $this->handleCustomerRequest($uri, $requestMethod, $queries, $payload);
                }
                break;
            case RESTConstants::ENDPOINT_CUSTOMER . "/" . RESTConstants::ENDPOINT_ORDER:
                if ($auth->hasAccess($_COOKIE['auth_token'], $endpointUri)) {
                    $employeeType = $auth ->employeeType($_COOKIE['auth_token']);
                    if ($employeeType == "Storekeeper"){
                        return $this->storeKeeperHandler($uri, $requestMethod, $queries, $payload);
                    }elseif ($employeeType == "ProductionPlanner"){
                        return $this->productionPlannerHandler($uri, $requestMethod, $queries, $payload);
                    }
                    elseif ($employeeType == "CustomerRepresentative"){
                        return $this->customerRepresentative($uri, $requestMethod, $queries, $payload);
                    }
                }
                break;
            case RESTConstants::ENDPOINT_TRANSPORTER . "/" . RESTConstants::ENDPOINT_ORDER:
                if ($auth->hasAccess($_COOKIE['auth_token'], $endpointUri)) {
                    return $this->handleTransporterRequest($uri, $requestMethod, $queries, $payload);
                }
                break;
            case RESTConstants::ENDPOINT_SKI:
                    return $this->handlePublicEndpoint($uri, $requestMethod, $queries, $payload);

        }
        return array();
    }

    public function handleCustomerRequest(array $uri, string $requestMethod, array $queries, array $payload): array
    {
        $auth = new AuthorisationModel();
        $endpointUri = "";
        for ($i = 0; $i < count($uri); $i++){
            $endpointUri .= $uri[$i] . "/";
            $endpointUri = $auth -> extractString($endpointUri);

        }
        $endpointUri = rtrim($endpointUri, "/");
        if ($queries != null){

            $endpointUri .= "?" . key($queries) . "=";
        }
            $order = new OrderModel();
            switch ($requestMethod) {
                case RESTConstants::METHOD_GET:
                    if ($endpointUri == RESTConstants::ENDPOINT_CUSTOMERUSER . "/" . RESTConstants::ENDPOINT_ORDER . "?since=" ) {
                        return $order->getOrderSinceDate($queries);
                    } elseif (count($uri) == 2 && $endpointUri == RESTConstants::ENDPOINT_CUSTOMERUSER . "/" . RESTConstants::ENDPOINT_ORDER){
                        return $order->getCollection();
                    } elseif ($endpointUri . "/" == RESTConstants::ENDPOINT_CUSTOMERUSER . "/" . RESTConstants::ENDPOINT_ORDER . "/" ) {
                        return $order->getResource($uri[2]);
                        //Todo check if order number is valid
                    }
                    break;
                case RESTConstants::METHOD_POST:
                    if ($endpointUri == RESTConstants::ENDPOINT_CUSTOMERUSER . "/" . RESTConstants::ENDPOINT_ORDER) {
                        return $order->createResource($payload);
                    }
                    break;

                case RESTConstants::METHOD_DELETE:
                    if ($endpointUri . "/" == RESTConstants::ENDPOINT_CUSTOMERUSER . "/" . RESTConstants::ENDPOINT_ORDER . "/") {
                        return $order->cancelResource($uri[2]);
                    }
                    break;

                case RESTConstants::METHOD_PUT:
                     if ($endpointUri . "/" == RESTConstants::ENDPOINT_CUSTOMERUSER . "/" . RESTConstants::ENDPOINT_ORDER . "/" . RESTConstants::ENDPOINT_SPLIT . "/") {
                         //Todo fix transaction problem
                         $order->splitOrder($uri[3]);
                    }
           }
            return array();

    }



    public function handleTransporterRequest(array $uri, string $requestMethod, array $queries, array $payload): array
    {
        $auth = new AuthorisationModel();

        $endpointUri = "";
        for ($i = 0; $i < count($uri); $i++){
            $endpointUri .= $uri[$i] . "/";
            $endpointUri = $auth -> extractString($endpointUri);

        }
        $endpointUri = rtrim($endpointUri, "/");
        if ($queries != null){
            $endpointUri .= "?" . key($queries) . "=";
        }

      $order = new Transporter();
        switch ($requestMethod){
          case RESTConstants::METHOD_GET:
              if ($endpointUri == RESTConstants::ENDPOINT_TRANSPORTER . "/" . RESTConstants::ENDPOINT_ORDER){
                  return $order->getReadyOrders();
              }
              break;
            case RESTConstants::METHOD_PUT:
               if ($endpointUri == RESTConstants::ENDPOINT_TRANSPORTER . "/" . RESTConstants::ENDPOINT_ORDER . "?setstate=") {
                   return $order->pickedUp($uri[2]);
               }
        }
        return array();
    }


    public function customerRepresentative(array $uri, string $requestMethod, array $queries, array $payload): array
    {
        $order = new OrderModel();
        $customerRep = new CustomerRep();
        $auth = new AuthorisationModel();

        $endpointUri = "";
        for ($i = 0; $i < count($uri); $i++){
            $endpointUri .= $uri[$i] . "/";
            $endpointUri = $auth -> extractString($endpointUri);

        }
        $endpointUri = rtrim($endpointUri, "/");
        if ($queries != null){
            $endpointUri .= "?" . key($queries) . "=";
        }


        switch ($requestMethod){
            case RESTConstants::METHOD_GET:
                if ($endpointUri == RESTConstants::ENDPOINT_CUSTOMER . "/" . RESTConstants::ENDPOINT_ORDER . "?state="){
                   return $order ->getOrdersByState($queries);
                }
                break;
            case RESTConstants::METHOD_PUT:
                if ($queries["setstate"] != null){
                    $customerRep -> fromNewToOpen($uri[2], $queries);
                }
        }
            return array();
    }


    public function storeKeeperHandler(array $uri, string $requestMethod, array $queries, array $payload): array
    {
        $order = new OrderModel();
        $customerRep = new CustomerRep();
        $auth = new AuthorisationModel();

        $endpointUri = "";
        for ($i = 0; $i < count($uri); $i++){
            $endpointUri .= $uri[$i] . "/";
            $endpointUri = $auth -> extractString($endpointUri);

        }
        $endpointUri = rtrim($endpointUri, "/");
        if ($queries != null){
            $endpointUri .= "?" . key($queries) . "=";
        }

        print $endpointUri;

        switch ($requestMethod){
            case RESTConstants::METHOD_GET:
                if ($endpointUri == RESTConstants::ENDPOINT_CUSTOMER . "/" . RESTConstants::ENDPOINT_ORDER . "?state="){
                    return $order ->getOrdersByState($queries);
                }
                break;
            case RESTConstants::METHOD_PUT:
                if ($queries["setstate"] != null){
                    $order -> updateOrderState($uri[2], $queries);
                }
        }
        return array();
    }


    /**
     * @throws BadRequestException
     */
    public function productionPlannerHandler(array $uri, string $requestMethod, array $queries, array $payload): array
    {
        $plan = new ProductionPlan();
        $auth = new AuthorisationModel();

        $endpointUri = "";
        for ($i = 0; $i < count($uri); $i++){
            $endpointUri .= $uri[$i] . "/";
            $endpointUri = $auth -> extractString($endpointUri);

        }
        $endpointUri = rtrim($endpointUri, "/");
        if ($queries != null){
            $endpointUri .= "?" . key($queries) . "=";
        }

        print $endpointUri;

        switch ($requestMethod){
            case RESTConstants::METHOD_POST:
                if ($endpointUri == RESTConstants::ENDPOINT_CUSTOMER . "/" . RESTConstants::ENDPOINT_PRODUCTIONPLANNER){
                    return $plan ->createResource($payload);
                    //Todo Have a look at the plan
                }
                break;
        }
        return array();
    }
















    public function handlePublicEndpoint(array $uri, string $requestMethod, array $queries, array $payload): array
    {
        $skiTypeModel = new SkiTypeModel();
        $auth = new AuthorisationModel();


        $endpointUri = "";
        for ($i = 0; $i < count($uri); $i++){
            $endpointUri .= $uri[$i] . "/";
            $endpointUri = $auth -> extractString($endpointUri);

        }
        $endpointUri = rtrim($endpointUri, "/");
        if ($queries != null){
            $endpointUri .= "?" . key($queries) . "=";
        }

        print $endpointUri;

        switch ($requestMethod) {
            case RESTConstants::METHOD_GET:
                 if ($endpointUri == RESTConstants::ENDPOINT_SKI . "?" . key($queries) . "=") {
                     if ($queries['model'] ?? null){
                         return $skiTypeModel->getCollectionModel($queries);
                     }elseif ($queries['grip'] ?? null){
                         return $skiTypeModel->filterGripSystem($queries);
                     }
                }elseif ($endpointUri == RESTConstants::ENDPOINT_SKI){
                     return $skiTypeModel->getCollection();
                 }
        }

        return array();

    }




}