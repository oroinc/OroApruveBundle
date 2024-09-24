<?php

namespace Oro\Bundle\ApruveBundle\Apruve\Model;

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
