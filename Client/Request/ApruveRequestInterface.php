<?php

namespace Oro\Bundle\ApruveBundle\Client\Request;

/**
 * Defines the contract for Apruve API request objects.
 */
interface ApruveRequestInterface
{
    /**
     * @return string
     */
    public function getUri();

    /**
     * @return array
     */
    public function getData();

    /**
     * @return string
     */
    public function getMethod();

    /**
     * @return array
     */
    public function toArray();
}
