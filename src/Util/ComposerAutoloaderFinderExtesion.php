<?php

namespace ArmelWanes\Crudify\Util;

use Symfony\Bundle\MakerBundle\Util\ComposerAutoloaderFinder;

class ComposerAutoloaderFinderExtesion extends ComposerAutoloaderFinder
{
    /**
     * ComposerAutoloaderFinderExtesion constructor.
     *
     * @param string $rootNamespace
     */
    public function __construct(string $rootNamespace = "")
    {
        parent::__construct($rootNamespace);
    }
}
