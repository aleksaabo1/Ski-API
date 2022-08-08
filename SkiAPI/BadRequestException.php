<?php

/**
 * Class BadRequestException exception class to be thrown when the client request is badly formatted or violates
application or database constraints
 */
class BadRequestException extends Exception
{
    protected $detailCode;
    protected $instance;

    public function __construct(int $code, int $detailCode = -1, string $instance= "", string $message = "", Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->detailCode = $detailCode;
        $this->instance = $instance;
    }

    public function getDetailCode()
    {
        return $this->detailCode;
    }

    public function getInstance()
    {
        return $this->instance;
    }
}
