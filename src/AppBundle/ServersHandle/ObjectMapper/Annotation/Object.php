<?php
/**
 * Created by PhpStorm.
 * User: synthetic
 * Date: 01.07.16
 * Time: 22:53
 */

namespace AppBundle\ServersHandle\ObjectMapper\Annotation;

use AppBundle\ServersHandle\ObjectMapper\ObjectMetadata;


/**
 * Indicate object for available mapping
 *
 * @Annotation
 * @Target("CLASS")
 */
class Object
{
    /** @var string */
    public $strategy = 'reflection';
    /** @var string */
    public $group = ObjectMetadata::DEFAULT_GROUP;
    /** @var bool */
    public $allProperties = false; // Available only for reflection strategy
}