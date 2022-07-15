<?php

namespace ArmelWanes\Crudify;

use Symfony\Bundle\MakerBundle\Exception\RuntimeCommandException;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\Str;

class GeneratorExtension extends Generator
{
    private $fileManagerExtension;

    private $pendingOperationsExtension;

    /**
     * GeneratorExtension constructor.
     *
     * @param FileManagerExtension $fileManagerExtension
     * @param string               $rootNamespace
     */
    public function __construct(FileManagerExtension $fileManagerExtension, string $rootNamespace)
    {
        $this->fileManagerExtension = $fileManagerExtension;
        parent::__construct($fileManagerExtension, $rootNamespace);
    }

    /**
     * Generate a new file for a class from a template.
     *
     * @param string $className    The fully-qualified class name
     * @param string $templateName Template name in Resources/skeleton to use
     * @param array  $variables    Array of variables to pass to the template
     *
     * @return string The path where the file will be created
     *
     * @throws \Exception
     */
    public function generateClass(string $className, string $templateName, array $variables = []): string
    {
        $targetPath = $this->fileManagerExtension->getRelativePathForFutureClass($className);
        
        if (null === $targetPath) {
            throw new \LogicException(
                sprintf(
                    'Could not determine where to locate the new class "%s", 
                    maybe try with a full namespace like "\\My\\Full\\Namespace\\%s"',
                    $className,
                    Str::getShortClassName($className)
                )
            );
        }

        $variables = array_merge(
            $variables,
            [
                'class_name' => Str::getShortClassName($className),
                'namespace'  => Str::getNamespace($className),
            ]
        );

        $this->addOperationExtension($targetPath, $templateName, $variables);

        return $targetPath;
    }

    /**
     * Actually writes and file changes that are pending.
     */
    public function writeChanges()
    {
        foreach ($this->pendingOperationsExtension as $targetPath => $templateData) {
            if (isset($templateData['contents'])) {
                $this->fileManagerExtension->dumpFile($targetPath, $templateData['contents']);

                continue;
            }

            $this->fileManagerExtension->dumpFile(
                $targetPath,
                $this->getFileContentsForPendingOperationExtension($targetPath, $templateData)
            );
        }

        $this->pendingOperationsExtension = [];
    }

    /**
     * @param string $targetPath
     *
     * @return string
     */
    public function getFileContentsForPendingOperationExtension(string $targetPath): string
    {
        if (!isset($this->pendingOperationsExtension[$targetPath])) {
            throw new RuntimeCommandException(
                sprintf('File "%s" is not in the Generator\'s pending operations', $targetPath)
            );
        }

        $templatePath = $this->pendingOperationsExtension[$targetPath]['template'];
        $parameters = $this->pendingOperationsExtension[$targetPath]['variables'];

        $templateParameters = array_merge(
            $parameters,
            [
                'relative_path' => $this->fileManagerExtension->relativizePath($targetPath),
            ]
        );

        return $this->fileManagerExtension->parseTemplate($templatePath, $templateParameters);
    }

    /**
     * @return FileManagerExtension
     */
    public function getFileManager(): FileManagerExtension
    {
        return $this->fileManagerExtension;
    }

    /**
     * @param string $targetPath
     * @param string $templateName
     * @param array  $variables
     *
     * @throws \Exception
     */
    private function addOperationExtension(string $targetPath, string $templateName, array $variables)
    {
        if ($this->fileManagerExtension->fileExists($targetPath)) {
            throw new RuntimeCommandException(
                sprintf(
                    'The file "%s" can\'t be generated because it already exists.',
                    $this->fileManagerExtension->relativizePath($targetPath)
                )
            );
        }

        $variables['relative_path'] = $this->fileManagerExtension->relativizePath($targetPath);

        $templatePath = $templateName;
        if (!file_exists($templatePath)) {
            $templatePath = __DIR__ . '/Resources/skeleton/' . $templateName;
            if (!file_exists($templatePath)) {
                throw new \Exception(sprintf('Cannot find template "%s"', $templateName));
            }
        }

        $this->pendingOperationsExtension[$targetPath] = [
            'template'  => $templatePath,
            'variables' => $variables,
        ];
    }
}
