<?php
/*
 * This file is part of the  Crudify package.
 * (c) Armel wanes <armelgeek5@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ArmelWanes\Crudify\Form\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
        
class OrdreType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $e = range(1, 50);
          $resolver
                ->setDefaults(array('choices' => array_combine($e, $e)));
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
}
