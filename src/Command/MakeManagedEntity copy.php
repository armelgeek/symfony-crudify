<?php

namespace  ArmelWanes\Crudify\Command;
use Symfony\Bundle\MakerBundle\Str;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Doctrine\EntityRelation;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Bundle\MakerBundle\Maker\MakeEntity;
use Symfony\Bundle\MakerBundle\MakerInterface;
use Symfony\Bundle\MakerBundle\Util\ClassNameDetails;
use Symfony\Bundle\MakerBundle\Util\ClassSourceManipulator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Bundle\MakerBundle\Validator;
use Symfony\Bundle\MakerBundle\Doctrine\DoctrineHelper;
use ArmelWanes\Crudify\Doctrine\DoctrineHelperExtension;
use Symfony\Bundle\MakerBundle\Doctrine\EntityClassGenerator;
use ArmelWanes\Crudify\Doctrine\EntityClassGeneratorExtension;
use ArmelWanes\Crudify\FileManagerExtension;
use ArmelWanes\Crudify\GeneratorExtension;
use Symfony\Bundle\MakerBundle\Exception\RuntimeCommandException;
class MakeManagedEntity extends AbstractMaker implements MakerInterface
{
    /** @var \Symfony\Bundle\MakerBundle\Doctrine\DoctrineHelper */
    private $doctrineHelper;

    /** @var FileManagerExtension */
    private $fileManager;

    /** @var GeneratorExtension */
    private $generator;

    /** @var MakeEntity */
    private $makeEntity;


    /** @var EntityClassGenerator */
    private $entityClassGenerator;
    /**
     * MakeEntityManagerCommand constructor.
     *
     * @param DoctrineHelperExtension $doctrineHelperExtension
     * @param FileManagerExtension    $fileManager
     * @param GeneratorExtension      $generator
     */
    public function __construct(
        DoctrineHelperExtension $doctrineHelperExtension,
        FileManagerExtension $fileManager,
        GeneratorExtension $generator,
        string $projectDirectory
    ) {
        $this->doctrineHelper = $doctrineHelperExtension->getDoctrineHelper();
        $this->fileManager = $fileManager;
        $this->generator = $generator;
        $this->makeEntity = new MakeEntity(
            $fileManager,
            $this->doctrineHelper,
            $projectDirectory,
            $this->generator
        );
    }

    /**
     * Return the command name for your maker (e.g. make:report).
     *
     * @return string
     */
    public static function getCommandName(): string
    {
        return 'make:crudify-entity';
    }

    /**
     * Configure the command: set description, input arguments, options, etc.
     *
     * By default, all arguments will be asked interactively. If you want
     * to avoid that, use the $inputConfig->setArgumentAsNonInteractive() method.
     *
     * @param Command            $command
     * @param InputConfiguration $inputConfig
     */


    public function configureCommand(Command $command, InputConfiguration $inputConf)
    {
        $command
            ->setDescription('Creates or updates a Doctrine entity class, and optionally an API Platform resource')
            ->addArgument('name', InputArgument::REQUIRED, sprintf('Class name of the entity to create or update (e.g. <fg=yellow>%s</>)', Str::asClassName(Str::getRandomTerm())))
            ->addOption('api-resource', 'a', InputOption::VALUE_NONE, 'Mark this class as an API Platform resource (expose a CRUD API for it)')
            ->addOption('regenerate', null, InputOption::VALUE_NONE, 'Instead of adding new fields, simply generate the methods (e.g. getter/setter) for existing fields')
            ->addOption('overwrite', null, InputOption::VALUE_NONE, 'Overwrite any existing getter/setter methods')
            ->setHelp(file_get_contents(__DIR__.'/../Resources/help/MakeManagedEntity.txt'))
        ;

        $inputConf->setArgumentAsNonInteractive('name');
    }

    /**
     * {@inheritdoc}
     */
    public function interact(InputInterface $input, ConsoleStyle $io, Command $command)
    {
        if ($input->getArgument('name')) {
            return;
        }
       
        if ($input->getOption('regenerate')) {
            $io->block([
                'This command will generate any missing methods (e.g. getters & setters) for a class or all classes in a namespace.',
                'To overwrite any existing methods, re-run this command with the --overwrite flag',
            ], null, 'fg=yellow');
         //   $classOrNamespace = $io->ask('Enter a class or namespace to regenerate', $this->getEntityNamespace(), [Validator::class, 'notBlank']);

          //  $input->setArgument('name', $classOrNamespace);

            return;
        }
        // name of entity
        $nameArgument = $command->getDefinition()->getArgument('name');
        $question = $this->createEntityClassQuestion($nameArgument->getDescription());
        $value = $io->askQuestion($question);
        $input->setArgument('name', $value);

        // crudify-setup
        $command->addArgument('enable-crudify', InputArgument::REQUIRED);
       // $command->addArgument('entity-namespace', InputArgument::REQUIRED);
        $input->setArgument(
                'enable-crudify',
                $io->confirm(
                    'Do you want to generate a CRUD with crudify ?',
                    true
                )
       );
      //  $this->makeEntity->interact($input, $io, $command);
    }

    /**
     * Configure any library dependencies that your maker requires.
     *
     * @param DependencyBuilder $dependencies
     */
    public function configureDependencies(DependencyBuilder $dependencies)
    {
        $this->makeEntity->configureDependencies($dependencies);
    }

    /**
     * @param InputInterface $input
     * @param ConsoleStyle   $io
     * @param Generator      $generator
     *
     * @throws \Exception
     */
    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator)
    {   $overwrite = $input->getOption('overwrite');
        $entityClassGenerator = new EntityClassGenerator($generator, $this->doctrineHelper);
        $enableCrudify = $input->getArgument('enable-crudify');
        if($enableCrudify){
         
        }
        if ($input->getOption('regenerate')) {
            $this->regenerateEntities($input->getArgument('name'), $overwrite, $generator);
            $this->writeSuccessMessage($io);

            return;
        }

        $entityClassDetails = $generator->createClassNameDetails(
            $input->getArgument('name'),
            'Entity\\'
        );
        $classExists = class_exists($entityClassDetails->getFullName());
        if (!$classExists) {
            $entityPath = $entityClassGenerator->generateEntityClass(
                $entityClassDetails,
                $input->getOption('api-resource')
            );

            $generator->writeChanges();
        }
        if (!$this->doesEntityUseAnnotationMapping($entityClassDetails->getFullName())) {
            throw new RuntimeCommandException(sprintf('Only annotation mapping is supported by make:entity, but the <info>%s</info> class uses a different format. If you would like this command to generate the properties & getter/setter methods, add your mapping configuration, and then re-run this command with the <info>--regenerate</info> flag.', $entityClassDetails->getFullName()));
        }

        if ($classExists) {
            $entityPath = $this->getPathOfClass($entityClassDetails->getFullName());
            $io->text([
                'Your entity already exists! So let\'s add some new fields!',
            ]);
        } else {
            $io->text([
                $entityPath,
                'Entity generated! Now let\'s add some fields!',
                'You can always add more fields later manually or by re-running this command.',
            ]);
        }
        $currentFields = $this->getPropertyNames($entityClassDetails->getFullName());
        $manipulator = $this->createClassManipulator($entityPath, $io, $overwrite);

        $isFirstField = true;

    }

    /**
     * @param ConsoleStyle $io
     * @param array        $fields
     * @param string       $entityClass
     * @param bool         $isFirstField
     *
     * @return array|EntityRelation
     */
    private function askForNextField(ConsoleStyle $io, array $fields, string $entityClass, bool $isFirstField)
    {
        $reflectionObject = new \ReflectionObject($this->makeEntity);
        $method = $reflectionObject->getMethod('askForNextField');
        $method->setAccessible(true);

        return $method->invokeArgs($this->makeEntity, [$io, $fields, $entityClass, $isFirstField]);
    }

    /**
     * @param string       $path
     * @param ConsoleStyle $io
     * @param bool         $overwrite
     *
     * @return ClassSourceManipulator
     */
    private function createClassManipulator(string $path, ConsoleStyle $io, bool $overwrite): ClassSourceManipulator
    {
        $reflectionObject = new \ReflectionObject($this->makeEntity);
        $method = $reflectionObject->getMethod('createClassManipulator');
        $method->setAccessible(true);

        return $method->invokeArgs($this->makeEntity, [$path, $io, $overwrite]);
    }

    /**
     * @param string $class
     *
     * @return string
     */
    private function getPathOfClass(string $class): string
    {
        $reflectionObject = new \ReflectionObject($this->makeEntity);
        $method = $reflectionObject->getMethod('getPathOfClass');
        $method->setAccessible(true);

        return $method->invokeArgs($this->makeEntity, [$class]);
    }

    /**
     * @param string    $classOrNamespace
     * @param bool      $overwrite
     * @param Generator $generator
     */
    private function regenerateEntities(string $classOrNamespace, bool $overwrite, Generator $generator)
    {
        $reflectionObject = new \ReflectionObject($this->makeEntity);
        $method = $reflectionObject->getMethod('regenerateEntities');
        $method->setAccessible(true);

        $method->invokeArgs($this->makeEntity, [$classOrNamespace, $overwrite, $generator]);
    }
    private function getPropertyNames(string $class): array
    {
        if (!class_exists($class)) {
            return [];
        }

        $reflClass = new \ReflectionClass($class);

        return array_map(function (\ReflectionProperty $prop) {
            return $prop->getName();
        }, $reflClass->getProperties());
    }
    /**
     * @param string $className
     *
     * @return bool
     */
    private function doesEntityUseAnnotationMapping(string $className): bool
    {
        if (!class_exists($className)) {
            $otherClassMetadatas = $this->doctrineHelper->getMetadata(Str::getNamespace($className).'\\', true);

            // if we have no metadata, we should assume this is the first class being mapped
            if (empty($otherClassMetadatas)) {
                return false;
            }

            $className = reset($otherClassMetadatas)->getName();
        }

        $driver = $this->doctrineHelper->getMappingDriverForClass($className);

        return $driver instanceof AnnotationDriver;
    }


    private function createEntityClassQuestion(string $questionText): Question
    {
        $question = new Question($questionText);
        $question->setValidator([Validator::class, 'notBlank']);
       // $question->setAutocompleterValues($this->doctrineHelper->getEntitiesForAutocomplete());

        return $question;
    }
    private function createCrudifyClassQuestion(string $questionText):Question
    {
        $question = new Question($questionText);
        $question->setValidator([Validator::class, 'notBlank']);
       // $question->setAutocompleterValues($this->doctrineHelper->getEntitiesForAutocomplete());

        return $question;
    }
}
