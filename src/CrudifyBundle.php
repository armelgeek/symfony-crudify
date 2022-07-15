<?php
namespace ArmelWanes\Crudify;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
class CrudifyBundle extends Bundle
{ /**
    * {@inheritdoc}
    */
   public function build(ContainerBuilder $container)
   {
       parent::build($container);

    $extension = $container->getExtension('crudify');
  //  $extension->addResolverFactory(new WebPathResolverFactory());
    }
}