<?="<?php\n" ?>

namespace <?= $namespace ?>;

use Doctrine\Common\Persistence\ObjectRepository;

interface RepositoryInterface extends ObjectRepository
{
    /**
     * @return string
     */
    public function getRepositoryEntityClassName(): string;
}
