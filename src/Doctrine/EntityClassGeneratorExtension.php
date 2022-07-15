<?php

namespace ArmelWanes\Crudify\Doctrine;

use ArmelWanes\Crudify\GeneratorExtension;
use Symfony\Bundle\MakerBundle\Util\ClassNameDetails;

class EntityClassGeneratorExtension
{
    /**
     * @var GeneratorExtension
     */
    private $generator;

    /**
     * EntityClassGeneratorExtension constructor.
     *
     * @param GeneratorExtension $generatorExtension
     */
    public function __construct(GeneratorExtension $generatorExtension)
    {
        $this->generator = $generatorExtension;
    }

    /**
     * @param ClassNameDetails $entityClassDetails
     * @param bool             $apiResource
     * @param bool             $withPasswordUpgrade
     *
     * @return string
     */
    public function generateEntityClass(
        ClassNameDetails $entityClassDetails,
        bool $apiResource,
        bool $withPasswordUpgrade = false
    ): string {

        $this->generateManagedEntityClassDependences($entityClassDetails);
        $repoClassDetails = $this->generator->createClassNameDetails(
            $entityClassDetails->getRelativeName(),
            'Repository\\',
            'Repository'
        );

        $entityPath = $this->generator->generateClass(
            $entityClassDetails->getFullName(),
            'doctrine/Entity.tpl.php',
            [
                'repository_full_class_name' => $repoClassDetails->getFullName(),
                'api_resource'               => $apiResource,
            ]
        );

        $entityAlias = strtolower($entityClassDetails->getShortName()[0]);
        $this->generator->generateClass(
            $repoClassDetails->getFullName(),
            'doctrine/Repository.tpl.php',
            [
                'entity_full_class_name' => $entityClassDetails->getFullName(),
                'entity_class_name'      => $entityClassDetails->getShortName(),
                'entity_alias'           => $entityAlias,
                'with_password_upgrade'  => $withPasswordUpgrade,
            ]
        );
        $managerClassDetails = $this->generator->createClassNameDetails(
            $entityClassDetails->getRelativeName(),
            'Manager\\',
            'Manager'
        );
        $this->generator->generateClass(
            $managerClassDetails->getFullName(),
            'doctrine/Manager.tpl.php',
            [
                'entity_full_class_name' => $entityClassDetails->getFullName(),
                'entity_class_name'      => $entityClassDetails->getShortName(),
            ]
        );

        return $entityPath;
    }

    /**
     * @param ClassNameDetails $classNameDetails
     *
     * @return bool
     */
    private function fileExists(ClassNameDetails $classNameDetails): bool
    {
        $fileManager = $this->generator->getFileManager();
        $targetPath = $fileManager->getRelativePathForFutureClass($classNameDetails->getFullName());

        return $fileManager->fileExists($targetPath);
    }

    /**
     * @param ClassNameDetails $entityClassDetails
     */
    private function generateManagedEntityClassDependences(ClassNameDetails $entityClassDetails)
    {
        $entityInterfaceDetails = $this->generator->createClassNameDetails(
            'ManagedEntity',
            'Entity\\',
            'Interface'
        );
        if (!$this->fileExists($entityInterfaceDetails)) {
            $this->generator->generateClass(
                $entityInterfaceDetails->getFullName(),
                'doctrine/ManagedEntityInterface.tpl.php',
                []
            );
        }
        $abstractManagedEntityDetails = $this->generator->createClassNameDetails(
            'AbstractManaged',
            'Entity\\',
            'Entity'
        );
        if (!$this->fileExists($abstractManagedEntityDetails)) {
            $this->generator->generateClass(
                $abstractManagedEntityDetails->getFullName(),
                'doctrine/AbstractManagedEntity.tpl.php',
                []
            );
        }

        $repositoryInterfaceDetails = $this->generator->createClassNameDetails(
            'Repository',
            'Repository\\',
            'Interface'
        );
        if (!$this->fileExists($repositoryInterfaceDetails)) {
            $this->generator->generateClass(
                $repositoryInterfaceDetails->getFullName(),
                'doctrine/RepositoryInterface.tpl.php',
                []
            );
        }
        $abstractRepositoryDetails = $this->generator->createClassNameDetails(
            'AbstractManagedEntity',
            'Repository\\',
            'Repository'
        );
        if (!$this->fileExists($abstractRepositoryDetails)) {
            $this->generator->generateClass(
                $abstractRepositoryDetails->getFullName(),
                'doctrine/AbstractManagedEntityRepository.tpl.php',
                [
                    'entity_full_class_name' => $entityClassDetails->getFullName(),
                    'entity_class_name'      => $entityClassDetails->getShortName(),
                ]
            );
        }

        $managerInterfaceDetails = $this->generator->createClassNameDetails(
            'Manager',
            'Manager\\',
            'Interface'
        );
        if (!$this->fileExists($managerInterfaceDetails)) {
            $this->generator->generateClass(
                $managerInterfaceDetails->getFullName(),
                'doctrine/ManagerInterface.tpl.php',
                []
            );
        }

        $abstractManagerDetails = $this->generator->createClassNameDetails(
            'Abstract',
            'Manager\\',
            'Manager'
        );
        if (!$this->fileExists($abstractManagerDetails)) {
            $this->generator->generateClass(
                $abstractManagerDetails->getFullName(),
                'doctrine/AbstractManager.tpl.php',
                []
            );
        }
    }
}
