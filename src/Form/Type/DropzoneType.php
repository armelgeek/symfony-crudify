<?php
/*
 * This file is part of the  Crudify package.
 * (c) Armel wanes <armelgeek5@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ArmelWanes\Crudify\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DropzoneType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'attr' => [
                'placeholder' => 'Drag and drop or browse',
                'class' => 'dropzone',
                'id' => 'my-awesome-dropzone',
                'accept' => 'image/jpeg,image/png'
            ]
        ]);
    }

    public function getParent()
    {
        return FileType::class;
    }

    public function getBlockPrefix()
    {
        return 'dropzone';
    }
}
