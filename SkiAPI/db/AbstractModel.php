<?php

/**
 * Class AbstractModel root class for all model classes
 */
abstract class AbstractModel extends DB
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Returns the collection of resources from the database.
     * @param array $query an optional set of conditions that the retrieved
     *              resources need to meet - e.g., array('make' => 'Ford') would
     *              mean that only resources having make = Ford would be returned.
     * @return array an array of associative arrays of resource attributes. The
     *               array will be empty if there are no resources to be returned.
     * @throws BadRequestException in the case the request from the client is badly formatted or violates application
     *         or database constraints.
     */
    abstract function getCollection(array $query = null): array;

    /**
     * Returns the collection of resources from the database.
     * @param int $id the id of the resource to be retrieved.
     * @return array an associative array of resource attributes - or null if
     *               no resources have the given id.
     * @throws BadRequestException in the case the request from the client is badly formatted or violates application
     *         or database constraints.
     */
    abstract function getResource(int $id): ?array;

    /**
     * Creates a new resource in the database.
     * @param array $resource the resource to be created.
     * @return array an associative array of resource attributes representing
     *               the resource - the returned value will include the id
     *               assigned to the resource.
     * @throws BadRequestException in the case the request from the client is badly formatted or violates application
     *         or database constraints.
     */
    abstract function createResource(array $resource): array;

    /**
     * Modifies a resource in the database.
     * @param array $resource the resource to be modified.
     * @return array an associative array of resource attributes representing
     *               the resource after being modified.
     * @throws BadRequestException in the case the request from the client is badly formatted or violates application
     *         or database constraints.
     */
    abstract function updateResource(array $resource): array;

    /**
     * Deletes a resource from the database.
     * @param int $id the id of the resource to be deleted.
     * @throws BadRequestException in the case the request from the client is badly formatted or violates application
     *         or database constraints.
     */
    abstract function cancelResource(int $id);

}
