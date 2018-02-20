<?php

namespace Oro\Bundle\ApruveBundle;

use Oro\Bundle\ApruveBundle\DependencyInjection\OroApruveExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class OroApruveBundle extends Bundle
{
    /**
     * {@inheritDoc}
     */
    public function getContainerExtension()
    {
        if (!$this->extension) {
            $this->extension = new OroApruveExtension();
        }

        return $this->extension;
    }
}
