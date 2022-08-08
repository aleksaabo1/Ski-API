<?php

require_once 'RESTConstants.php';
require_once 'ResourceController.php';
require_once 'errors.php';
require_once 'db/CustomerModel.php';

    /**
     * Class DealersEndpoint implementing the dealers endpoint controller.
     */
class CustomerEndpoints extends ResourceController
{
    /**
     * CustomerEndpoint constructor. It specifies which sub resource requests are allowed It also defines which functions
     * are implemented on the collection and the resource.
     * @see RequestHandler::$validRequests
     * @see RequestHandler::$validMethods
     */
    public function __construct()
    {
        parent::__construct();
        $this->validRequests[] = RESTConstants::ENDPOINT_ID;
        // Valid collection method calls vs implementation status
        $this->validMethods[''] = array();
        $this->validMethods[''][RESTConstants::METHOD_GET] = RESTConstants::HTTP_OK;
        $this->validMethods[''][RESTConstants::METHOD_POST] = RESTConstants::HTTP_OK;
        // Valid resource method calls vs implementation status
        $this->validMethods[RESTConstants::ENDPOINT_ID] = array();
        $this->validMethods[RESTConstants::ENDPOINT_ID][RESTConstants::METHOD_GET] = RESTConstants::HTTP_OK;
        $this->validMethods[RESTConstants::ENDPOINT_ID][RESTConstants::METHOD_PUT] = RESTConstants::HTTP_NOT_IMPLEMENTED;
        $this->validMethods[RESTConstants::ENDPOINT_ID][RESTConstants::METHOD_DELETE] = RESTConstants::HTTP_NOT_IMPLEMENTED;
    }

    /**
     * @throws BadRequestException as other request handling methods
     * @see ResourceController::doRetrieveCollection
     */
    protected function doRetrieveCollection(array $queries): array
    {
        $filter = null;
        if (isset($queries['counties'])) {
            $filter = array();
            $filter['counties'] = preg_split('/[,][\s]*/', $queries['counties']);
        }
        return (new CustomerModel())->getCollection($filter);
    }

    /**
     * @throws BadRequestException as other request handling methods
     * @see ResourceController::doRetrieveResource
     */
    protected function doRetrieveResource(int $id): ?array
    {
        return (new CustomerModel())->getResource($id);
    }

    /**
     * @throws BadRequestException as other request handling methods
     * @see ResourceController::doCreateResource
     */
    protected function doCreateResource(array $payload): array
    {
        return (new CustomerModel())->createResource($payload);
    }

    /**
     * @throws BadRequestException as other request handling methods
     * @see ResourceController::doUpdateResource
     */
    protected function doUpdateResource(array $payload)
    {
        (new CustomerModel())->updateResource($payload);
    }

    /**
     * @throws BadRequestException as other request handling methods
     * @see ResourceController::doDeleteResource
     */
    protected function doDeleteResource(int $id)
    {
        (new CustomerModel())->deleteResource($id);
    }
}


