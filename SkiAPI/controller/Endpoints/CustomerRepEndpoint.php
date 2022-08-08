<?php


class CustomerRepEndpoint extends ResourceController
{

    protected function doRetrieveCollection(array $queries): array
    {
        return (new OrderModel()) -> getCollection($queries);
    }

    protected function doCreateResource(array $payload, int $id = null): array
    {
        $record = new shipmentRecording();
        $comment = "Created shipment request for order";
        $record ->addTransition($id, $payload['transporterID'],$comment );
        return (new shipmentModel) -> createResource($payload, $id);
    }


    protected function doUpdateResource(int $id, array $query): ?array
    {
        (new handleOrder()) ->addTransition($id, "Updated order to state: " . $query['setstate']);
        return (new OrderModel()) -> updateOrderState($id, $query);
    }

    protected function doDeleteResource(int $id)
    {
    }

    protected function doRetrieveResource(int $id): ?array
    {
    }
}