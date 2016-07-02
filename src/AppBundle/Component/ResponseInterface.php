<?php
/**
 * Created by PhpStorm.
 * User: synthetic
 * Date: 01.07.16
 * Time: 23:46
 */

namespace AppBundle\Component;


interface ResponseInterface
{
    /**
     * Get data
     *
     * @return mixed
     */
    public function getData();

    /**
     * Get http status code
     *
     * @return int
     */
    public function getHttpStatusCode();

    /**
     * Get headers
     *
     * @return \Symfony\Component\HttpFoundation\HeaderBag
     */
    public function getHeaders();
}