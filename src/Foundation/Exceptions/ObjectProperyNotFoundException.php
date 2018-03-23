<?php

namespace Crazymeeks\WP\Foundation\Exceptions;

class ObjectProperyNotFoundException extends \Exception
{

    /**
     * Status code
     * @var int
     */
    protected $statusCode = 500;

    /**
     * @param string  $message
     * @param int $statusCode
     */
    public function __construct($message = ' The property has not been set. ', $statusCode = null)
    {
        parent::__construct($message);
        if (!is_null($statusCode)) {
            $this->setStatusCode($statusCode);
        }
    }

    /**
     * Set the response status code
     *
     * @param int $statusCode
     * @return void
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
    }

    /**
     * Get the status code
     *
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

}