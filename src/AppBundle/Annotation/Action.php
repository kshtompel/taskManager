<?php

namespace AppBundle\Annotation;

/**
 * Indicate of API action
 *
 * @Annotation
 * @Target("METHOD")
 */

class Action
{
    /**
     * Action name. As example: "system.ping"
     *
     * @var string @Required
     */
    public $name;

    /**
     * Validation groups. As example: {"Update", "EmailUnique"}
     *
     * @var array
     */
    public $validationGroups = ['Default'];

    /**
     * Construct
     *
     * @param array $values
     */
    public function __construct(array $values)
    {
        if (isset($values['value']) && count($values) == 1) {
            $this->name = $values['value'];
        } else {
            foreach ($values as $key => $value) {
                $this->{$key} = $value;
            }
        }
    }
}
