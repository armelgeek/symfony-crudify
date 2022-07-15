<?= "<?php\n" ?>

namespace <?= $namespace ?>;

use App\Entity\ManagedEntityInterface;
use App\Repository\RepositoryInterface;
use \InvalidArgumentException;

interface ManagerInterface
{
    /**
     * @param ManagedEntityInterface $entity
     *
     * @throws InvalidArgumentException
     */
    public function create(ManagedEntityInterface $entity): void;

    /**
     * @param ManagedEntityInterface $entity
     *
     * @throws InvalidArgumentException
     */
    public function edit(ManagedEntityInterface $entity): void;

    /**
     * @param ManagedEntityInterface $entity
     *
     * @throws InvalidArgumentException
     */
    public function delete(ManagedEntityInterface $entity): void;

    /**
     * @param ManagedEntityInterface $entity
     *
     * @return bool
     * @throws InvalidArgumentException
     */
    public function changeStatus(ManagedEntityInterface $entity): bool;

    /**
     * Retrouve le repository du manager.
     *
     * @return RepositoryInterface
     */
    public function getRepository(): RepositoryInterface;

    /**
     * Retrouve la class de l'entié géré par le manager
     *
     * @return string
     */
    public function getManagedEntityClassName(): string;
}
