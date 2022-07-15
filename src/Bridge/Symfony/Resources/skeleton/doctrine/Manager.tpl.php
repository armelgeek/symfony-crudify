<?= "<?php\n" ?>

namespace <?= $namespace ?>;

use <?= $entity_full_class_name; ?>;
use App\Manager\AbstractManager;

class <?= $class_name ?> extends AbstractManager
{
    /**
     * {@inheritdoc}
     */
    public function getManagedEntityClassName(): string
    {
        return <?= $entity_class_name ?>::class;
    }
}
