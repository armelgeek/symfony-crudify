<?= "<?php\n" ?>

namespace <?= $namespace; ?>;

interface ManagedEntityInterface
{
    /**
     * @return int|null
     */
    public function getId(): ?int;

    /**
     * @return bool|null
     */
    public function isEnabled(): ?bool;

    /**
     * @param bool|null $enabled
     */
    public function setEnabled(?bool $enabled): void;
}
