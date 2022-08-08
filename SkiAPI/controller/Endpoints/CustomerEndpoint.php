<?php

require_once 'SkiAPI/db/OrderModel.php';

class CustomerEndpoint extends ResourceController
{

    protected function doRetrieveCollection(array $queries): array
    {

        return (new OrderModel()) -> getCollectionCustomer($queries);

    }

    protected function doCreateResource(array $payload, int $id = null): array
    {
        return (new OrderModel()) -> createResource($payload);
    }

    protected function doRetrieveResource(int $id): ?array
    {
        return (new OrderModel()) -> getResource($id);
    }

    protected function doUpdateResource(int $id, array $query): array
    {
        return (new OrderModel()) -> splitOrder($id);
    }

    protected function doDeleteResource(int $id): array
    {
        return (new OrderModel()) -> cancelResource($id);
    }
}