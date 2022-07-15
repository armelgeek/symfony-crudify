<?= "<?php\n"; ?>

namespace <?= $namespace; ?>;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

abstract class AbstractManagedEntityRepository extends ServiceEntityRepository implements RepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, $this->getRepositoryEntityClassName());
    }
}
