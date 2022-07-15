<?php
/*
 * This file is part of the  Crudify package.
 * (c) Armel wanes <armelgeek5@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ArmelWanes\Crudify\Form;

use ArmelWanes\Crudify\Form\Type\AutoSizeType;
use ArmelWanes\Crudify\Form\Type\DateTimeType;
use ArmelWanes\Crudify\Form\Type\SwitchType;
use ArmelWanes\Crudify\Annotation\CrudReader;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;
use RuntimeException;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;

/**
 * Génère un formulaire de manière automatique en lisant les propriété d'un objet
 */
class AutomaticForm extends AbstractType
{
    private $annotationReader;
    const TYPES = [
        "string"                 => TextType::class,
        "color"                  => ColorType::class,
        "integer"                => NumberType::class,
        "float"                  => NumberType::class,
        "file"                   => VichImageType::class,
        "vichUploader"           => VichImageType::class,
        "entity"                 => EntityType::class,
        'checkbox'               => CheckBoxType::class,
        'textarea'               => AutoSizeType::class,
        'link'                   => UrlType::class,
        'collection'             =>CollectionType::class,
        'datetime'               =>DateTimeType::class,
        'only'                   => TextType::class
    ];

    public function __construct(CrudReader $annotationReader)
    {
        $this->annotationReader = $annotationReader;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $data = $options['data'];
        try {
            $refClass = new ReflectionClass($data);
            $classProperties = $refClass->getProperties(ReflectionProperty::IS_PRIVATE);
            foreach ($classProperties as $property) {

                $name = $property->getName();
                if ($name!="id") {
                    if($this->annotationReader->isCrudField($data, $name)){
                        if (array_key_exists($this->annotationReader->getOneField($data, $name), self::TYPES)) {
                            if ($this->annotationReader->getOneField($data, $name) == 'entity') {
                                $builder->add($name, EntityType::class, [
                                    'class' =>  $this->annotationReader->getOneEntite($data, $name)[0],
                                    'choice_label' => $this->annotationReader->getOneEntite($data, $name)[1],
                                    //         'attr' => ['class' => 'col-lg-6']
                                ]);
                            } else if ($this->annotationReader->getOneField($data, $name) == 'only') {
                                $builder->add($name, $this->annotationReader->getOneForm($data, $name));
                            } else if ($this->annotationReader->getOneField($data, $name) == 'collection') {

                                $builder->add($name,CollectionType::class, array(
                                    'entry_type'   => $this->annotationReader->getOneForm($data, $name),
                                    'allow_add'    => true,
                                    'allow_delete' => true,
                                    'by_reference' => false,
                                    //         'attr' => ['class' => 'col-lg-6']
                                ));
                            } else if ($this->annotationReader->getOneField($data, $name) == 'checkbox') {

                                $builder->add($name, CheckBoxType::class,[
                                    'required' =>false
                                ]);
                            } else{
                                $builder->add($name,self::TYPES[$this->annotationReader->getOneField($data, $name)],[
                                    'required' =>false,
                                    'label'  =>$this->annotationReader->getOneLabel($data, $name),
                                    //     'attr' => ['class' => 'col-lg-6']

                                ]);
                            }
                        } else {
                            throw new RuntimeException(sprintf(
                                'Impossible de trouver le champs associé au type %s dans %s::%s',
                                $this->annotationReader->getOneField($data, $name),
                                get_class($data),
                                $name
                            ));
                        }
                    }
                }
            }
        } catch (ReflectionException $e) {
        }
    }
}
