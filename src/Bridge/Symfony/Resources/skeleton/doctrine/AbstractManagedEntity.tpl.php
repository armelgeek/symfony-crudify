<?= "<?php\n" ?>

namespace <?= $namespace ?>;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class AbstractManagedEntity
 *
 * @ORM\MappedSuperclass
 */
abstract class AbstractManagedEntity implements ManagedEntityInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $enabled = true;

    /**
     * @return null|int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return bool|null
     */
    public function isEnabled(): ?bool
    {
        return $this->enabled;
    }

    /**
     * @param bool|null $enabled
     */
    public function setEnabled(?bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    /**
     * @return null|string
     */
    public function __toString(): ?string
    {
        return $this->id;
    }
}
