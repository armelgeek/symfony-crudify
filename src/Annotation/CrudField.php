<?php

/*
 * This file is part of the Crudify Annotation.
 *
 * (c) Armel wanes <armelgeek5@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace ArmelWanes\Crudify\Annotation;


/**
 * Crudify annotation.
 *
 * @author CrudField <armelgeek5@gmail.com>
 *
 * @Annotation
 * @Target({"METHOD", "PROPERTY"})
 * @Attributes(
 *     @Attribute("label", type="string"),
 *     @Attribute("type", type="string"),
 *     @Attribute("fieldList", type="array"),
 *     @Attribute("form", type="string"),
 *     @Attribute("sortable", type="bool"),
 *     @Attribute("showed", type="bool"),
 *
 *)
 */
final class CrudField
{
    private $label;
    private $type;
    private $fieldList=[];
    private $listable=false;
    private $form;
    private $sortable;
    private $showed;
    private $entity;
    /**
     * CrudField constructor
     */
    public function __construct(array $options)

    {
        if(empty($options['label'])){
            $this->label = null;
        } else {
            $this->label = $options['label'];
        }
        if (empty($options['type'])) {
            $this->type = null;
        } else {
            $this->type = $options['type'];
        }
        if(empty($options['fieldList'])){
            $this->fieldList=null;
        }else{
            $this->fieldList=$options['fieldList'];
        }

        if(empty($options['fieldList']['tbw'])){
            $this->fieldList['tbw'] ="auto";
        }else{
            $this->fieldList['tbw']=$options['fieldList']['tbw'];
        }
       
        if(empty($options['fieldList']['type'])){
             $this->fieldList['type'] ="string";

        }else{
            $this->fieldList['type']=$options['fieldList']['type'];

        }
         if($this->fieldList!=null && !empty($options['fieldList']['name'])){
            $this->fieldList['name']=$options['fieldList']['name'];
        }else{
            $this->fieldList['name']=null;
        }
    
        if(empty($options['form'])){
            $this->form=null;
        }else{
            $this->form=$options['form'];
        }
        if(empty($options['sortable'])){
            $this->sortable=false;
        }else{
            $this->sortable=$options['sortable'];
        }
        if(empty($options['showed'])){
            $this->showed=false;
        }else{
            $this->showed=$options['showed'];
        }
        if(empty($options['entity'])){
            $this->entity=null;
        }else{
            $this->entity=$options['entity'];
        }
        if(empty($options['listable'])){
            $this->listable=false;
        }else{
            $this->listable=$options['listable'];
        }

    }

    /**
     * @return mixed
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param mixed $label
     * @return CrudField
     */
    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     * @return CrudField
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFieldList(): ?array
    {
        return $this->fieldList;
    }

    /**
     * @param mixed $fieldList
     * @return CrudField
     */
    public function setFieldList(array $fieldList)
    {
        $this->fieldList = $fieldList;
        return $this;
    }




    /**
     * @return mixed
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @param mixed $form
     * @return CrudField
     */
    public function setForm($form)
    {
        $this->form = $form;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSortable()
    {
        return $this->sortable;
    }

    /**
     * @param mixed $sortable
     * @return CrudField
     */
    public function setSortable($sortable)
    {
        $this->sortable = $sortable;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getShowed()
    {
        return $this->showed;
    }

    /**
     * @param mixed $showed
     * @return CrudField
     */
    public function setShowed($showed)
    {
        $this->showed = $showed;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param mixed $entity
     */
    public function setEntity($entity): void
    {
        $this->entity = $entity;
    }
     /**
     * @return mixed
     */
    public function getListable()
    {
        return $this->listable;
    }

    /**
     * @param mixed $listable
     */
    public function setListable($listable): void
    {
        $this->listable = $listable;
    }

}