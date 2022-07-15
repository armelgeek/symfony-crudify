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
 * @author Crudify <armelgeek5@gmail.com>
 *
 * @Annotation
 * @Target({"CLASS"})
 * @Attributes(
 *     @Attribute("searchField", type="array"),
 *     @Attribute("routePrefix", type="string"),
 *     @Attribute("title", type="array"),
 *     @Attribute("customize", type="array"),
 *     @Attribute("page", type="integer"),
 *     @Attribute("orderBy", type="array"),
 *     @Attribute("customOperation", type="array"),
 *     @Attribute("children", type="array"),
 *     @Attribute("hasCustomOperation", type="bool"),
 *)
 */
final class Crudify
{

    /**
     * @var array
     */
    private $searchField;
    /**
     * @var
     */
    private $routePrefix;
    /**
     * @var array
     */
    private $title;
    /**
     * @var array
     */
    private $customize=[];
    /**
     * @var integer
     */
    private $page;
    /**
     * @var array
     */
    private $orderBy;
    /**
     * @var array
     */
    private $children;
    /**
     * @var array
     */
    private $customOperation;
    /**
     * @var boolean
     */
    private $hasCustomOperation;

    /**
     * Crudify constructor.
     * @param array $options
     */
    public function __construct(array $options)
    {
        if(empty($options['searchField'])){
            throw new \InvalidArgumentException("L'annotation Crudify doit avoir un attribut searchField");
        }


        if(empty($options['routePrefix'])){
            throw new \InvalidArgumentException("L'annotation Crudify doit avoir un attribut routePrefix");
        }
        if(empty($options['children'])){
            throw new \InvalidArgumentException("L'annotation Crudify doit avoir un attribut children");
        }

        if(empty($options['orderBy'])){
            throw new \InvalidArgumentException("L'annotation Crudify doit avoir un attribut orderBy");
        }
        if(empty($options['orderBy']['attribute'])){
            $this->orderBy['attribute'] ='id';
        }else{
            $this->orderBy['attribute']=$options['orderBy']['attribute'];
        }
        if(empty($options['orderBy']['direction'])){
            $this->orderBy['direction'] ='desc';
        }else{
            $this->orderBy['direction']=$options['orderBy']['direction'];
        }

        if(empty($options['page'])){
            $this->pageCount = 10;
        }else{
            $this->pageCount=$options['pageCount'];
        }
        for($i=0;$i<count($options['children']);$i++){
            if(empty($options['children'][$i]) || $options['children'][$i] == null){
                $this->children[$i] = "not";
            }else{
                $this->children[$i]=$options['children'][$i];
            }
        }
     /* */
        $this->searchField=$options['searchField'];
        $this->routePrefix=$options['routePrefix'];
        $this->orderBy=$options['orderBy'];
        if(!empty($options['title']['add'])){
            $this->title['add']=$options['title']['add'];
        }else{
            $this->title['add']='Ajouter du contenu';
        }
        if(!empty($options['title']['edit'])){
            $this->title['edit']=$options['title']['edit'];
        }else{
            $this->title['edit']='Editer du contenu';
        }
        if(!empty($options['title']['index'])){
            $this->title['index']=$options['title']['index'];
        }else{
            $this->title['index']='Liste des contenus';
        }
        if(!empty($options['title']['show'])){
            $this->title['show']=$options['title']['show'];
        }else{
            $this->title['show']='Afficher';
        }
        if(empty($options['customize']['form'])){
            $this->customize['form']  = false;
        }else{
            $this->customize['form'] = $options['customize']['form'];
        }
        if(empty($options['customize']['view'])){
            $this->customize['view'] = false;
        }else{
            $this->customize['view']=$options['customize']['view'];
        }
        if(empty($options['customize']['index'])){
            $this->customize['index']= false;
        }else{
            $this->customize['index']=$options['customize']['index'];
        }

        if(empty($options['customize']['folder'])){
                throw new \InvalidArgumentException("L'annotation Crudify doit avoir un attribut folder");
            if(!empty($options['customize']['index']) || !empty($options['customize']['view']) || !empty($options['customize']['form'])){
                throw new \InvalidArgumentException("L'annotation Crudify doit avoir un attribut folder");
            }
        }else {
            $this->customize['folder'] = $options['customize']['folder'];
        }
         if(empty($options['hasCustomOperation'])){
            $this->hasCustomOperation=false;
        }else{
            $this->hasCustomOperation=$options['hasCustomOperation'];
        }
        if(empty($options['customOperation'])){
            $this->customOperation=null;
        }else{
            $this->customOperation=$options['customOperation'];
        }
        if(empty($options['customOperation']['title'])){
            $this->customOperation['title']= "C.OP";
        }else{
            $this->customOperation['title']=$options['customOperation']['title'];
        }
        if(empty($options['customOperation']['tbw'])){
            $this->customOperation['tbw']= "auto";
        }else{
            $this->customOperation['tbw']=$options['customOperation']['tbw'];
        }
     
        
        if(empty($options['customOperation']['all']['routeUrl'])){
            $this->customOperation['all']['routeUrl']= $this->getRoutePrefix()."_index";
        }else{
            $this->customOperation['all']['routeUrl']=$options['customOperation']['all']['routeUrl'];
        }
        if(empty($options['customOperation']['all']['label'])){
            $this->customOperation['all']['label']= 'Label';
        }else{
            $this->customOperation['all']['label']=$options['customOperation']['all']['label'];
        }
    }

    /**
     * @return array
     */
    public function getSearchField() : array
    {
        return $this->searchField;
    }

    /**
     * @param mixed $searchField
     * @return Crudify
     */
    public function setSearchField(array $searchField)
    {
        $this->searchField = $searchField;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRoutePrefix()
    {
        return $this->routePrefix;
    }

    /**
     * @param mixed $routePrefix
     * @return Crudify
     */
    public function setRoutePrefix($routePrefix)
    {
        $this->routePrefix = $routePrefix;
        return $this;
    }

    /**
     * @return array
     */
    public function getTitle(): array
    {
        return $this->title;
    }

    /**
     * @param array $title
     * @return Crudify
     */
    public function setTitle(array $title): Crudify
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return array
     */
    public function getCustomize(): array
    {
        return $this->customize;
    }

    /**
     * @param array $customize
     * @return Crudify
     */
    public function setCustomize(array $customize): Crudify
    {
        $this->customize = $customize;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @param mixed $page
     * @return Crudify
     */
    public function setPage($page)
    {
        $this->page = $page;
        return $this;
    }

    /**
     * @return array
     */
    public function getOrderBy(): array
    {
        return $this->orderBy;
    }

    /**
     * @param mixed $orderBy
     * @return Crudify
     */
    public function setOrderBy(array $orderBy)
    {
        $this->orderBy = $orderBy;
        return $this;
    }

    /**
     * @return array
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    /**
     * @param array $children
     * @return Crudify
     */
    public function setChildren(array $children): Crudify
    {
        $this->children = $children;
        return $this;
    }
    /**
     * @return mixed
     */
    public function getCustomOperation(): ?array
    {
        return $this->customOperation;
    }

    /**
     * @param mixed $customOperation
     * @return CrudField
     */
    public function setCustomOperation(array $customOperation)
    {
        $this->customOperation = $customOperation;
        return $this;
    }
    /**
     * @return mixed
     */
    public function getHasCustomOperation()
    {
        return $this->hasCustomOperation;
    }

    /**
     * @param mixed $hasCustomOperation
     * @return Crudify
     */
    public function setHasCustomOperation($hasCustomOperation)
    {
        $this->hasCustomOperation = $hasCustomOperation;
        return $this;
    }

    
}