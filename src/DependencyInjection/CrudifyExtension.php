<?php
namespace ArmelWanes\Crudify\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

use Symfony\Bundle\MakerBundle\DependencyInjection\CompilerPass\MakeCommandRegistrationPass;
/**
 * Registers service configuration.
 */
class CrudifyExtension extends Extension
{
    /**
     * @param array            $config    Configuration object
     * @param ContainerBuilder $container DI container that provides an API to easily describe services
     *
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yaml');
        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);
    
        $rootNamespace = trim($config['root_namespace'], '\\');
      
    }
}
