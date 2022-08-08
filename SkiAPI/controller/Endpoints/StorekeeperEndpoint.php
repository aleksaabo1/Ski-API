<?php


class StorekeeperEndpoint extends ResourceController
{

    protected function doRetrieveCollection(array $queries): array
    {
        $queries['state'] = "available";
        return (new OrderModel())->getOrdersByState($queries);
    }

    protected function doCreateResource(array $payload, int $id = null): array
    {
        return (new skiModel())->createResource($payload);
    }

    protected function doRetrieveResource(int $id): ?array
    {
    }

    protected function doUpdateResource(int $id, array $query)
    {
        if (isset($query['product'])){
            return (new OrderModel())->assigningSkis($id,$query['product']);
        }elseif (isset($query['setstate'])){
            return (new OrderModel())->updateOrderState($id,$query);
        }

    }

    protected function doDeleteResource(int $id)
    {
    }
}