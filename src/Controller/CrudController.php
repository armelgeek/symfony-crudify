<?php
/*
 * This file is part of the  Crudify package.
 * (c) Armel wanes <armelgeek5@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ArmelWanes\Crudify\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
abstract class CrudController extends AutomaticCrud
{
    abstract public function getEntityInstance();
    abstract public function displaySearch();
    abstract public function isOrphanEntity();
    abstract public function hasCustomOperation();
    abstract public function getCustomOperation();
    /**
     * @Route("/nouveau", name="new", methods={"POST", "GET"})
     * @return Response
     */
    public function new(Request $request):Response
    {
        
        return $this->crudNew($this->getEntityInstance(),$request);
    }
    /**
     * @Route("/", name="index", methods={"POST", "GET"})
     */
    public  function index(Request $request){
  
        return $this->crudIndex($this->getEntityInstance(),$this->displaySearch(),$this->hasCustomOperation(),$this->getCustomOperation(),$request);
    }

    /**
     * @Route("/{id}/afficher", name="show", methods={"GET"})
     * @return Response
     */
    public function show($id):Response
    {
        $tag = $this->getDoctrine()
            ->getRepository(get_class($this->getEntityInstance()))
            ->find($id);
        return $this->crudShow($tag);
    }

    /**
     * @Route("/{id}", name="edit", methods={"GET","POST"})
     * @return Response
     */
    public function edit($id,Request $request):Response
    {
     
        $tag = $this->getDoctrine()
            ->getRepository(get_class($this->getEntityInstance()))
            ->find($id);
            return $this->crudEdit($tag,$request);

    }

    /**
     * @Route("/{id}", methods={"DELETE"})
     * @return Response
     */
    public function delete($id,Request $request): Response
    {
     
        $tag = $this->getDoctrine()
            ->getRepository(get_class($this->getEntityInstance()))
            ->find($id);
        return $this->crudDelete($tag,$request);
    }
    /**
     * @Route("/{id}/cloner", name="clone", methods={"GET","POST"})

     * @return Response
     */
    public function clone($id,Request $request): Response
    {
 
        $tag = $this->getDoctrine()
            ->getRepository(get_class($this->getEntityInstance()))
            ->find($id);
        return $this->crudClone($tag,$request);
    }

}