<?php

require_once ('SkiAPI/db/ProductionPlan.php');

class ProductionPlannerEndpoint extends ResourceController
{

    protected function doRetrieveCollection(array $queries): array
    {
       return (new ProductionPlan()) ->getCollection($queries);
    }

    protected function doCreateResource(array $payload, int $id = null): array
    {
        return (new ProductionPlan()) ->createResource($payload);
    }

    protected function doRetrieveResource(int $id): ?array
    {
    }

    protected function doUpdateResource(int $id, array $query)
    {
    }

    protected function doDeleteResource(int $id)
    {
    }
}