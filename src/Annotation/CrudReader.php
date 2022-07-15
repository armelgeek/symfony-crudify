<?php

namespace ArmelWanes\Crudify\Annotation;

use Doctrine\Common\Annotations\Reader;
use Psr\Container\ContainerInterface;
use ReflectionClass;

class CrudReader
{
    private $annotationReader;
    private $container;
    public function __construct(Reader $annotationReader,ContainerInterface $container)
    {
        $this->annotationReader = $annotationReader;
        $this->container = $container;
    }
    public function isCruddable($entity):bool {
        $reflexion = new ReflectionClass(get_class($entity));
        return $this->annotationReader->getClassAnnotation($reflexion,Crudify::class)!==null;
    }
    public function getConfig($entity)
    {
        $reflexion = new ReflectionClass(get_class($entity));
        return $this->annotationReader->getClassAnnotation($reflexion,Crudify::class);
    }
    public function getCrudFields($entity):array
    {
        $reflexion = new ReflectionClass(get_class($entity));
        if (!$this->isCruddable($entity)){
            return [];
        }
        $properties=[];
        foreach ($reflexion->getProperties() as $property){
            $annotation = $this->annotationReader->getPropertyAnnotation($property,CrudField::class);

            if($annotation!==null){
                $properties[$property->getName()]=$annotation;
            }
        }

        return $properties;
    }
  
    public function isCrudField($entity,$proper):bool {

        $reflexion = new ReflectionClass(get_class($entity));
        if (!$this->isCruddable($entity)){
            return false;
        }
        foreach ($reflexion->getProperties() as $property) {
            if($property->getName()==$proper){
            $annotation = $this->annotationReader->getPropertyAnnotation($property, CrudField::class);
            if ($annotation !== null) {
                if ($annotation->getType() != null) {
                    return true;
                    break;
                }
            }
            }
        }
        return false;
    }
    public function getOneField($entity,$proper) {

        $reflexion = new ReflectionClass(get_class($entity));
        if (!$this->isCruddable($entity)){
            return false;
        }
        foreach ($reflexion->getProperties() as $property) {
            if($property->getName()==$proper){
                $annotation = $this->annotationReader->getPropertyAnnotation($property, CrudField::class);
                return $annotation->getType();
            }
        }
        return null;
    }
    
    public function getOneLabel($entity,$proper) {

        $reflexion = new ReflectionClass(get_class($entity));
        if (!$this->isCruddable($entity)){
            return false;
        }
        foreach ($reflexion->getProperties() as $property) {
            if($property->getName()==$proper){
                $annotation = $this->annotationReader->getPropertyAnnotation($property, CrudField::class);
                return $annotation->getLabel();
            }
        }
        return null;
    }
    public function getOneForm($entity,$proper) {

        $reflexion = new ReflectionClass(get_class($entity));
        if (!$this->isCruddable($entity)){
            return false;
        }
        foreach ($reflexion->getProperties() as $property) {
            if($property->getName()==$proper){
                $onetomany = $this->annotationReader->getPropertyAnnotation($property, CrudField::class);
                if($onetomany!==null){
                    return $onetomany->getForm();
                }
            }
        }
        return null;
    }
    public function getOneEntite($entity,$proper) {

        $reflexion = new ReflectionClass(get_class($entity));
        if (!$this->isCruddable($entity)){
            return false;
        }
        foreach ($reflexion->getProperties() as $property) {
            if($property->getName()==$proper){
                $annotation = $this->annotationReader->getPropertyAnnotation($property, CrudField::class);
                return $annotation->getEntity();
            }
        }
        return null;
    }
    public function getTableFields($entity):array
    {
        $reflexion = new ReflectionClass(get_class($entity));
        if (!$this->isCruddable($entity)){
            return [];
        }
        $properties=[];
        foreach ($reflexion->getProperties() as $property){
            $annotation = $this->annotationReader->getPropertyAnnotation($property,CrudField::class);
            if($annotation!==null){
               if($annotation->getListable()==true){
                $properties[]=$property->getName();
               }
            }
        }
        return $properties;
    }

  
    public function getTableContentFields($entity):array
    {

        $reflexion = new ReflectionClass(get_class($entity));
        if (!$this->isCruddable($entity)){
            return [];
        }
        $properties=[];
        foreach ($reflexion->getProperties() as $property){
            $annotation = $this->annotationReader->getPropertyAnnotation($property,CrudField::class);
            if($annotation!==null){
               if($annotation->getListable()==true){
                   $properties[$property->getName()]=$annotation->getFieldList();       
                }
            }
        }
        return $properties;
    }

    public function getTableLabelFields($entity):array
    {
        $reflexion = new ReflectionClass(get_class($entity));
        if (!$this->isCruddable($entity)){
            return [];
        }
        $properties=[];
        foreach ($reflexion->getProperties() as $property){
            $annotation = $this->annotationReader->getPropertyAnnotation($property,CrudField::class);
            if($annotation!==null){
                //   dd($annotation);
                if($annotation->getListable()==true){
                   $properties[$property->getName()]=$annotation->getLabel();
                }
            }
        }
        return $properties;
    }
    public function getCrudFieldSortBy($entity):array
    {
        $reflexion = new ReflectionClass(get_class($entity));
        if (!$this->isCruddable($entity)){
            return [];
        }
        $properties=[];
        foreach ($reflexion->getProperties() as $property){
            $annotation = $this->annotationReader->getPropertyAnnotation($property,CrudField::class);
            if($annotation!==null){
                if($annotation->getSortable()==true){
                    $properties[]='row.'.$property->getName();
                }
            }
        }
        return $properties;
    }
    public function getCrudFieldShow($entity):array
    {
        $reflexion = new ReflectionClass(get_class($entity));
        if (!$this->isCruddable($entity)){
            return [];
        }
        $properties=[];
        foreach ($reflexion->getProperties() as $property){
            $annotation = $this->annotationReader->getPropertyAnnotation($property,CrudField::class);
            if($annotation!==null){
                if($annotation->getShowed()==true){
                    $properties[]=$property->getName();
                }
            }
        }
        return $properties;
    }

}