<?php

namespace Oro\Bundle\ApruveBundle\Client\Request;

use Oro\Bundle\ApruveBundle\Apruve\Model\ApruveEntityInterface;

/**
 * Represents an Apruve API request with method, URI, and body.
 */
class ApruveRequest implements ApruveRequestInterface
{
    /**
     * @var string
     */
    protected $uri;

    /**
     * @var ApruveEntityInterface
     */
    protected $requestData;

    /**
     * @var string
     */
    protected $method;

    /**
     * @param string                     $method
     * @param string                     $uri
     * @param ApruveEntityInterface|null $requestData
     */
    public function __construct($method, $uri, ?ApruveEntityInterface $requestData = null)
    {
        $this->uri = $uri;
        $this->requestData = $requestData;
        $this->method = $method;
    }

    #[\Override]
    public function toArray()
    {
        return [
            'method' => $this->getMethod(),
            'uri' => $this->getUri(),
            'data' => $this->getData(),
        ];
    }

    #[\Override]
    public function getMethod()
    {
        return $this->method;
    }

    #[\Override]
    public function getUri()
    {
        return $this->uri;
    }

    #[\Override]
    public function getData()
    {
        if ($this->requestData) {
            return $this->requestData->getData();
        }

        return [];
    }
}
