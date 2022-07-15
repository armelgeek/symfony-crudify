<?php

/*
 * This file is part of the  Crudify package.
 * (c) Armel wanes <armelgeek5@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ArmelWanes\Crudify\EventListener;

use ArmelWanes\Crudify\Asset\EntrypointLookupCollection;

class ExceptionListener
{
    private $entrypointLookupCollection;

    private $buildNames;

    public function __construct(EntrypointLookupCollection $entrypointLookupCollection, array $buildNames)
    {
        $this->entrypointLookupCollection = $entrypointLookupCollection;
        $this->buildNames = $buildNames;
    }

    public function onKernelException()
    {
        foreach ($this->buildNames as $buildName) {
            $this->entrypointLookupCollection->getEntrypointLookup($buildName)->reset();
        }
    }
}
