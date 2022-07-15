<?php
/*
 * This file is part of the  Crudify package.
 * (c) Armel wanes <armelgeek5@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ArmelWanes\Crudify\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AutoSizeType extends TextareaType
{

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'html5' => true,
            'row_attr' => [
                'class' => 'autosize'
            ]
        ]);
    }
}
