<?php

namespace AppBundle\ServersHandle\Servers;

use Symfony\Component\HttpFoundation\Request;

/**
 * All API servers should be implemented of this interface
 */
interface ServerInterface
{
    /**
     * Is supported
     *
     * @param Request $request
     *
     * @return bool
     */
    public function isSupported(Request $request);

    /**
     * Handle symfony request
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request);
}