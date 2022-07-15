<?= "<?php\n" ?>

namespace <?= $namespace ?>;

<?php if ($api_resource): ?>use ApiPlatform\Core\Annotation\ApiResource;
<?php endif ?>
use App\Entity\AbstractManagedEntity;
use Doctrine\ORM\Mapping as ORM;

/**
<?php if ($api_resource): ?> * @ApiResource()
<?php endif ?>
 * @ORM\Entity(repositoryClass="<?= $repository_full_class_name ?>")
 */
class <?= $class_name." extends AbstractManagedEntity\n" ?>
{
}
