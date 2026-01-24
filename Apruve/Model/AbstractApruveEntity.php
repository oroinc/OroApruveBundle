<?php

namespace Oro\Bundle\ApruveBundle\Apruve\Model;

/**
 * Provides common functionality for Apruve entity models.
 *
 * This abstract class serves as a base for Apruve API entity models. It manages the underlying data array
 * and provides methods to access the complete data and retrieve entity identifiers.
 * Subclasses represent specific Apruve entities such as orders, line items, and other API objects.
 */
abstract class AbstractApruveEntity implements ApruveEntityInterface
{
    /**
     * @var array
     */
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    #[\Override]
    public function getData()
    {
        return (array)$this->data;
    }

    #[\Override]
    public function getId()
    {
        if (array_key_exists('id', $this->data)) {
            return (string) $this->data['id'];
        }

        return null;
    }
}
