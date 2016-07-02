<?php

namespace AdminBundle;

use AdminBundle\DependencyInjection\AdminExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AdminBundle extends Bundle
{
    /**
     * {@inheritDoc}
     */
    public function getContainerExtension()
    {
        if (!$this->extension) {
            $this->extension = new AdminExtension();
        }

        return $this->extension;
    }
}
