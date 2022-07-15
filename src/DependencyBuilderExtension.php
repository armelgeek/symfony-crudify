<?php

namespace ArmelWanes\Crudify;

use Symfony\Bundle\MakerBundle\DependencyBuilder;

final class DependencyBuilderExtension
{
    /**
     * @var DependencyBuilder
     */
    private $dependencyBuilder;

    /**
     * DependencyBuilderExtension constructor.
     *
     * @param DependencyBuilder $dependencyBuilder
     */
    public function __construct(DependencyBuilder $dependencyBuilder)
    {
        $this->dependencyBuilder = $dependencyBuilder;
    }

    /**
     * @return DependencyBuilder
     */
    public function getDependencyBuilder(): DependencyBuilder
    {
        return $this->dependencyBuilder;
    }
}
