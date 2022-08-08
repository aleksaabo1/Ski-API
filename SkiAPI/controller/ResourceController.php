<?php
require_once 'RequestHandler.php';
require_once 'SkiAPI/controller/APIException.php';
require_once 'SkiAPI/db/CustomerModel.php';

abstract class ResourceController extends RequestHandler
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * The main function handling the client request to the resource endpoint. Collection requests are forwarded to the
     * handleCollectionRequest(), while resource requests are forwarded to the handleResourceRequest().
     * @throws APIException as described in the superclass
     * @throws BadRequestException as described in the superclass
     * @see RequestHandler::handleRequest()
     * @see handleCollectionRequest for the handling og collection requests
     * @see handleResourceRequest for the handling of resource requests
     */
    public function handleRequest(array $uri, string $endpointPath, string $requestMethod, array $queries, array $payload): array
    {
        // Collection request
        if (count($uri) == 1) {
            return $this->handleCollectionRequest($uri, $endpointPath, $requestMethod, $queries, $payload);
            // Resource request
        } else if (count($uri) == 2) {
            return $this->handleResourceRequest($uri, $endpointPath, $uri[1], $requestMethod, $payload, $queries);
        } else if (count($uri) > 1) {
            throw new APIException(RESTConstants::HTTP_NOT_FOUND, $endpointPath . '/' . implode('/', $uri));
        }
    }

    /**
     * The function handling the collection requests. The function is a dispatcher method that forwards the retrieval
     * and create requests to the respective request handlers.
     * @throws APIException as other request handling methods
     * @throws BadRequestException as other request handling methods
     * @see RequestHandler
     * @see handleRetrieveCollectionRequest for the handling of collection retrieval requests
     * @see handleCreateResourceRequest for the handling of resource creation requests
     */
    public function handleCollectionRequest(array $uri, string $endpointPath, string $requestMethod, array $queries, array $payload): array
    {
        $endpointPath = explode("/", $endpointPath)[3];

        $res = array();
        $path = $endpointPath . "/" . $uri[0];

        try {
            switch ($requestMethod) {
                case RESTConstants::METHOD_GET:
                    if ($path == "public/ski" || $path == "transporter/shipment" || $path == "customer/order" || $path == "employee/order" ||$path == "employee/plan" ) {
                        $res['result'] = $this->doRetrieveCollection($queries);
                        if (count($res['result']) > 0) {
                            $res['status'] = RESTConstants::HTTP_OK;
                        } else {
                            $res['status'] = RESTConstants::HTTP_NO_CONTENT;
                            http_response_code(RESTConstants::HTTP_NO_CONTENT);
                            throw new APIException(RESTConstants::HTTP_NO_CONTENT, $endpointPath);
                        }
                    }elseif ($path == "customer/plan"){
                        $res['result'] = (new ProductionPlan()) ->getCollection($queries);
                        if (count($res['result']) > 0) {
                            $res['status'] = RESTConstants::HTTP_OK;
                        } else {
                            http_response_code(RESTConstants::HTTP_NO_CONTENT);
                            throw new APIException(RESTConstants::HTTP_NOT_FOUND, $endpointPath);
                        }
                    }
                    break;
                case RESTConstants::METHOD_POST:
                    if ($path == "employee/customer") {
                        $res['result'] = (new CustomerModel())->createResource($payload, $queries);
                    }else{
                        $res['result'] = $this->doCreateResource($payload, $uri[1] ?? 0);
                        if (count($res['result']) > 0){
                            $res['status'] = RESTConstants::HTTP_CREATED;
                        }
                        else{
                            $res['result'] = "Missing required fields to continue";
                            $res['status'] = RESTConstants::HTTP_BAD_REQUEST;
                        }
                    }
                    if (!$res['status']){
                        $res['status'] = RESTConstants::HTTP_CREATED;
                    }
                    break;
            }
        } catch (BadRequestException $e) {
            throw new BadRequestException($e->getCode(), $e->getDetailCode(), $endpointPath, $e->getMessage(), $e);
        }
        return $res;
    }

    /**
     * The function handling the resource requests. The function is a dispatcher method that forwards the retrieval,
     * update, and delete requests to the respective request handlers. (Only resource retrieval is currently implemented.)
     * @throws APIException as other request handling methods
     * @throws BadRequestException as other request handling methods
     * @see RequestHandler::handleRetrieveResourceRequest
     * @see RequestHandler::handleUpdateResourceRequest
     * @see RequestHandler::handleRDeleteResourceRequest
     */
    public function handleResourceRequest(array $uri, string $endpointPath, int $id, string $requestMethod, array $payload, array $queries): array
    {

        $endpointPath = explode("/", $endpointPath)[3];

        $res = array();
        $path = $endpointPath . "/" . $uri[0];

        $res = array();
        try {
            switch ($requestMethod) {
                case RESTConstants::METHOD_GET:
                    if ($uri[0] == "order" || $uri[0] == "shipment" ){
                        $res['result'] = $this->doRetrieveResource($id);
                        if ($res['result']) {
                            $res['status'] =  RESTConstants::HTTP_OK;
                        } else {
                            throw new APIException(RESTConstants::HTTP_NOT_FOUND, $endpointPath);
                        }
                    }
                    break;
                case RESTConstants::METHOD_POST:

                    $res['result'] = $this->doCreateResource($payload, $id);
                    if (count($res['result']) > 0){
                        $res['status'] = RESTConstants::HTTP_CREATED;
                    }
                    else{
                        $res['status'] = RESTConstants::HTTP_BAD_REQUEST;
                    }
                    break;

                case RESTConstants::METHOD_PUT:
                    $res['result'] = $this->doUpdateResource($id, $queries);
                    if (count($res) < 0){
                        $res['status'] = RESTConstants::HTTP_BAD_REQUEST;
                    }else{
                        $res['status'] = RESTConstants::HTTP_OK;
                    }
                    break;
                case RESTConstants::METHOD_DELETE:
                    $res['result'] = $this->doDeleteResource($id);
                    if(count($res['result']) == 0){
                        $res['status'] = RESTConstants::HTTP_NOT_AUTHORIZED;
                    }else{
                        $res['status'] = RESTConstants::HTTP_OK;
                    }
                    break;
            }
        } catch (BadRequestException $e) {
            throw new BadRequestException($e->getCode(), $e->getDetailCode(), $endpointPath, $e->getMessage(), $e);
        }
        return $res;
    }

    /**
     * The function handling the collection retrieval requests.
     * @param array $queries filter parameters being passed from the client
     * @return array the collection of resources
     * @throws BadRequestException as other request handling methods
     * @see handleCollectionRequest
     */
    protected abstract function doRetrieveCollection(array $queries): array;


    /**
     * The function handling the resource creation requests.
     * @param array $payload the resource attributes sent from the client
     * @return array the resource as being stored in the database - including the id created by the database
     * @throws BadRequestException as other request handling methods
     * @see handleCollectionRequest
     */
    protected abstract function doCreateResource(array $payload, int $id = null): array;

    /**
     * The function handling the resource retrieval requests.
     * @param $id int the resource to be retrieved
     * @return array|null the requested resource
     * @throws BadRequestException as other request handling methods
     * @see handleResourceRequest
     */
    protected abstract function doRetrieveResource(int $id): ?array;

    /**
     * The function handling the resource update requests.
     * @param int $id of the resource we want to update
     * @param array $query of the resource we want to update.
     * @throws BadRequestException as other request handling methods
     * @see handleResourceRequest
     */
    protected abstract function doUpdateResource(int $id, array $query);

    /**
     * The function handling the resource deletion requests.
     * @param $id int the resource to be deleted
     * @throws BadRequestException as other request handling methods
     * @see handleResourceRequest
     */
    protected abstract function doDeleteResource(int $id);
}