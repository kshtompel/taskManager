<?php

namespace AppBundle\Controller;

use AppBundle\Registry\ServerRegistry;
use Symfony\Component\HttpFoundation\Request;

class AppController
{
    /**
     * @var ServerRegistry
     */
    private $serverRegistry;

    /**
     * Construct
     *
     * @param ServerRegistry $serverRegistry
     */
    public function __construct(ServerRegistry $serverRegistry)
    {
        $this->serverRegistry = $serverRegistry;
    }

    /**
     * Handle
     *
     * @param Request $request
     * @param string  $server
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, $server)
    {
        return $this->serverRegistry->getServer($server)
            ->handle($request);
    }
}