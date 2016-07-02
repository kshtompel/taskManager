<?php
/**
 * Created by PhpStorm.
 * User: synthetic
 * Date: 01.07.16
 * Time: 23:30
 */

namespace AppBundle\Component\Error;

interface ErrorFactoryInterface
{
    /**
     * Get available errors. Must be return array, when:
     * key - error code
     * value - error message
     *
     * @return array
     */
    public function getErrors();

    /**
     * Get exceptions. Must be return array, when:
     * key - exception class
     * value - error code
     *
     * @return array
     */
    public function getExceptions();

    /**
     * Get reserved diapason
     *
     * @return array
     */
    public function getReservedDiapason();
}