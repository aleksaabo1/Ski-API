<?php


class TransporterEndpoint extends ResourceController
{
    protected function doRetrieveCollection(array $queries): array
    {
        $queries['state'] = "ready";
        return (new OrderModel()) -> getOrdersByState($queries);
    }

    protected function doCreateResource(array $payload, int $id = null): array
    {
    }

    protected function doRetrieveResource(int $id): ?array
    {
    }

    protected function doUpdateResource(int $id, array $query):array
    {

        $token = (new AuthorisationModel()) -> extractID($_COOKIE['auth_token']);
        (new SkiModel) -> deleteResource($id);
        (new shipmentRecording()) -> addTransition($id, $token, "Shipped order");

        return (new shipmentModel) -> updateResource($id, $query);
    }

    protected function doDeleteResource(int $id)
    {
    }
}