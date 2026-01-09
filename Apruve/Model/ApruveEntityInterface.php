<?php

namespace Oro\Bundle\ApruveBundle\Apruve\Model;

/**
 * Base interface for Apruve entity models.
 *
 * Defines the contract for Apruve entities to provide their data representation
 * and unique identifiers for API communication.
 */
interface ApruveEntityInterface
{
    /**
     * @return array
     */
    public function getData();

    /**
     * @return string|null
     */
    public function getId();
}
