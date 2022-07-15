<?php

namespace ArmelWanes\Crudify\Command;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MakeEntityCommand extends AbstractMakeCommand
{
    protected static $defaultName = 'do:entity';

    protected function configure(): void
    {
        $this
            ->setDescription('Crée une entité dans le domaine choisi et le test associé')
            ->addArgument('entityName', InputArgument::OPTIONAL, "Nom de l'entité")
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($input->getArgument('entityName')) {
            return false;
        }

        $io = new SymfonyStyle($input, $output);
        /** @var string $entity */
        $entity = $input->getArgument('entityName');

        /** @var Application $application */
        $application = $this->getApplication();
        $command = $application->find('make:entity');
        $arguments = [
            'command' => 'make:entity',
            'name' => "\\App\\Entity\\$entity",
        ];
        $greetInput = new ArrayInput($arguments);

        return $command->run($greetInput, $output);
    }
}
