<?php
/**
 * Created by PhpStorm.
 * User: synthetic
 * Date: 01.07.16
 * Time: 22:55
 */

namespace AppBundle\ServersHandle\ObjectMapper\Annotation;


/**
 * Indicate property as collection for mapping
 *
 * @Annotation
 * @Target("ANNOTATION")
 *
 */
class Collection
{
    /** @var string */
    public $class = 'ArrayObject';
    /** @var bool */
    public $saveKeys = true;
}
