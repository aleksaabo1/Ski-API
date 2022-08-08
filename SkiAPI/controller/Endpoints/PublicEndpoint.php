<?php

require_once ('SkiAPI/controller/ResourceController.php');

class PublicEndpoint extends ResourceController
{


    protected function doRetrieveCollection(array $queries): array
    {
        return (new SkiTypeModel()) ->getCollection($queries);
    }

    protected function doCreateResource(array $payload, int $id = null): array
    {
    }

    protected function doRetrieveResource(int $id): ?array
    {
    }

    protected function doUpdateResource(int $id, array $query): array
    {
    }

    protected function doDeleteResource(int $id)
    {
    }
}