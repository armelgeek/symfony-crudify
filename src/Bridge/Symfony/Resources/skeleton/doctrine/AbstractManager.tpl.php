<?= "<?php\n" ?>

namespace <?= $namespace ?>;

use App\Entity\ManagedEntityInterface;
use App\Repository\RepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use \InvalidArgumentException;

abstract class AbstractManager implements ManagerInterface
{
    /** @var  EntityManagerInterface */
    protected $entityManager;

    /** @var RepositoryInterface */
    protected $repository;

    /**
     * AbstractBaseManager constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $this->entityManager->getRepository($this->getManagedEntityClassName());
    }

    /**
     * {@inheritdoc}
     */
    public function getRepository(): RepositoryInterface
    {
        return $this->repository;
    }

    /**
     * {@inheritdoc}
     */
    public function create(ManagedEntityInterface $entity): void
    {
        $entityClassName = $this->getManagedEntityClassName();
        if (!$entity instanceof $entityClassName) {
            throw new InvalidArgumentException($entityClassName);
        }
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function edit(ManagedEntityInterface $entity): void
    {
        $entityClassName = $this->getManagedEntityClassName();
        if (!$entity instanceof $entityClassName) {
            throw new InvalidArgumentException($entityClassName);
        }
        $this->entityManager->merge($entity);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function changeStatus(ManagedEntityInterface $entity): bool
    {
        $entityClassName = $this->getManagedEntityClassName();
        if ($entity instanceof $entityClassName) {
            throw new InvalidArgumentException($entityClassName);
        }
        $entity->setEnabled(!$entity->isEnabled());
        $this->edit($entity);

        return $entity->isEnabled();
    }

    /**
     * {@inheritdoc}
     */
    public function delete(ManagedEntityInterface $entity): void
    {
        $entityClassName = $this->getManagedEntityClassName();
        if ($entity instanceof $entityClassName) {
            throw new InvalidArgumentException($entityClassName);
        }
        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }
}
