<?php
/*
 * This file is part of the  Crudify package.
 * (c) Armel wanes <armelgeek5@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ArmelWanes\Crudify\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;

class DateTimeType extends \Symfony\Component\Form\Extension\Core\Type\DateTimeType
{

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'html5' => false,
            'widget' => 'single_text',
            'attr' => [
                'id' => 'date-picker'
            ]
        ]);
    }

}
