<?php

namespace ArmelWanes\Crudify\Doctrine;

use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Bundle\MakerBundle\Doctrine\DoctrineHelper;

class DoctrineHelperExtension
{
    /**
     * @var DoctrineHelper
     */
    private $doctrineHelper;

    /**
     * DoctrineHelperExtension constructor.
     *
     * @param string          $entityNamespace
     * @param ManagerRegistry $registry
     */
    public function __construct(string $entityNamespace, ManagerRegistry $registry)
    {
        $this->doctrineHelper = new DoctrineHelper($entityNamespace, $registry);
    }

    /**
     * @return DoctrineHelper
     */
    public function getDoctrineHelper(): DoctrineHelper
    {
        return $this->doctrineHelper;
    }
}
