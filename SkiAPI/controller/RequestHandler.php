<?php
/**
 * Class RequestHandler is the main root class for endpoint handlers
 */
abstract class RequestHandler
{

    /**
     * @var array an array holding the RESTConstant values indicating what method may be called on this resource/controller.
     */
    protected $validMethods;

    /**
     * @var array an associative array mapping requests to an HTTP status indicating whether such a request is valid
     *      or not implemented,
     */
    protected $validRequests;

    /**
     * The constructor defines the valid requests to be the dealers, used cars and report controller endpoints.
     * @see RequestHandler
     */
    public function __construct()
    {
        $this->validMethods = array(RESTConstants::METHOD_GET => RESTConstants::ENDPOINT_SKI );
        $this->validRequests = array(
            RESTConstants::ENDPOINT_SKI);
    }

    /**
     * Checks whether the method call is valid for the given resource request/controller.
     * @param string $request the requested resource
     * @param string $method the method to check
     * @return int the HTTP status code indicating whether the method is OK, NOT IMPLEMENTED or NOT ALLOWED on the
     *         requested resource/controller
     */
    public function isValidMethod(string $request, string $method): int
    {

        print_r($this->validMethods);
        print_r($this->validRequests);
        print $this->validMethods[$request];

        if (key_exists($request, $this->validMethods) && key_exists($method, $this->validMethods[$request])) {
            return $this->validMethods[$request][$method];
        }
        return RESTConstants::HTTP_METHOD_NOT_ALLOWED;
    }

    /**
     * Checks wether the requested resource/controller is defined/valid
     * @param string $request
     * @return bool
     */
    public function isValidRequest(string $request): bool
    {

        return in_array($request, $this->validRequests);
    }

    /**
     * The main function handling the client request to the api.
     * @param array $uri the part of the requested URI that is still to be processed after being exploded to an array.
     * @param string $endpointPath the path to the current resource.
     * @param string $requestMethod the method being requested.
     * @param array $queries the query string from the client
     * @param array $payload the JSON payload received from the client - after being decoded to an array structure
     * @return array an associative array of the format array('status' => nn, 'result' => array()) where the status is
     *         one of the HTTP status codes indicating the success of the operation and result being the resource
     *         representation to be returned to the client.
     * @throws APIException in the case of resources not found, bad requests, methods not implemented, etc.
     * @throws BadRequestException in the case the request from the client is badly formatted or violates application
     *         or database constraints.
     */
    public abstract function handleRequest(array $uri, string $endpointPath, string $requestMethod,
                                           array $queries, array $payload): array;
}