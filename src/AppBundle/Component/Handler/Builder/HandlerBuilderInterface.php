<?php
/**
 * Created by PhpStorm.
 * User: synthetic
 * Date: 01.07.16
 * Time: 23:28
 */

namespace AppBundle\Component\Handler\Builder;


use AppBundle\Component\Handler\HandlerInterface;

interface HandlerBuilderInterface
{
    /**
     * Build handler
     *
     * @return HandlerInterface
     */
    public function buildHandler();

//    /**
//     * Build doc extractor for this handler
//     *
//     * @return ExtractorInterface
//     */
//    public function buildDocExtractor();
}