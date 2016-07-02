<?php
/**
 * Created by PhpStorm.
 * User: synthetic
 * Date: 01.07.16
 * Time: 23:07
 */

namespace AppBundle\ServersHandle\ObjectMapper;


class StrategyRegistry implements StrategyRegistryInterface
{
    /**
     * @var array
     */
    private $strategies = [];

    /**
     * Add strategy
     *
     * @param string            $key
     * @param StrategyInterface $strategy
     *
     * @return StrategyRegistry
     */
    public function add($key, StrategyInterface $strategy)
    {
        $this->strategies[$key] = $strategy;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function get($key)
    {
        if (!isset($this->strategies[$key])) {
            throw new StrategyNotFoundException(sprintf(
                'Not found strategy with key "%s".',
                $key
            ));
        }

        return $this->strategies[$key];
    }

    /**
     * Create default manager
     *
     * @return StrategyRegistry
     */
    public static function createDefault()
    {
        /** @var StrategyRegistry $manager */
        $manager = new static();

        $manager
            ->add(ObjectMetadata::STRATEGY_REFLECTION, new ReflectionStrategy());

        return $manager;
    }
}