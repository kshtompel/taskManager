<?php

namespace AppBundle\ServersHandle\Servers;

use Symfony\Component\HttpFoundation\Request;

/**
 * Interface ServerDataInterface
 *
 * @package AppBundle\ServerData\Servers
 * @deprecated
 */
interface ServerDataInterface
{
    /**
     * Is allowed
     *
     * @param Request $request
     *
     * @return bool
     */
    public function isAllowed(Request $request);

    /**
     * Handle request
     *
     * @param  Request $request
     * @return array
     * @throws \Exception
     */
    public function getData(Request $request);

    /**
     * Return content type for processed data.
     *
     * @return string
     */
    public function getContentType();
}
