<?= "<?php\n"; ?>

namespace <?= $namespace; ?>;

use <?= $entity_full_class_name; ?>;
use App\Repository\AbstractManagedEntityRepository;
<?= $with_password_upgrade ? "use Symfony\Component\Security\Core\Exception\UnsupportedUserException;\n" : '' ?>
<?= $with_password_upgrade ? "use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;\n" : '' ?>
<?= $with_password_upgrade ? "use Symfony\Component\Security\Core\User\UserInterface;\n" : '' ?>

/**
 * @method <?= $entity_class_name; ?>|null find($id, $lockMode = null, $lockVersion = null)
 * @method <?= $entity_class_name; ?>|null findOneBy(array $criteria, array $orderBy = null)
 * @method <?= $entity_class_name; ?>[]    findAll()
 * @method <?= $entity_class_name; ?>[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class <?= $class_name; ?> extends AbstractManagedEntityRepository<?= $with_password_upgrade ? " implements PasswordUpgraderInterface\n" : "\n" ?>
{
    /**
     * {@inheritdoc}
     */
    public function getRepositoryEntityClassName(): string
    {
        return <?= $entity_class_name; ?>::class;
    }
<?php if ($with_password_upgrade): ?>

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }
<?php endif ?>
}
