<?php

namespace AppBundle;

use AppBundle\DependencyInjection\AppExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AppBundle extends Bundle
{
    /**
     * {@inheritDoc}
     */
    public function getContainerExtension()
    {
        if (!$this->extension) {
            $this->extension = new AppExtension();
        }

        return $this->extension;
    }
}
