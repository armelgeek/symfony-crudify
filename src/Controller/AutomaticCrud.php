<?php
/*
 * This file is part of the  Crudify package.
 * (c) Armel wanes <armelgeek5@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ArmelWanes\Crudify\Controller;

use App\Form\AutomaticForm;
use App\Helper\Paginator\PaginatorInterface;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ObjectRepository;
use ArmelWanes\Crudify\Annotation\CrudReader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
 
/**
 * @template E
 */
abstract class AutomaticCrud extends AbstractController
{

    private $annotationReader;
    protected $em;
    protected $paginator;
    private $dispatcher;
    private $requestStack;

    protected array $events = [
        'update' => null,
        'delete' => null,
        'create' => null
    ];

    public function __construct(
        EntityManagerInterface $em,
        PaginatorInterface $paginator,
        EventDispatcherInterface $dispatcher,
        RequestStack $requestStack,
        CrudReader $annotationReader

    )
    {
        $this->em = $em;
        $this->paginator = $paginator;
        $this->dispatcher = $dispatcher;
        $this->requestStack = $requestStack;
        $this->annotationReader = $annotationReader;

    }

    public function crudNew(object $data,Request $request): Response
    {

        $classAnnotation=$this->annotationReader->getConfig($data);
        for($i=0; $i<count($classAnnotation->getChildren()); $i++){
            if($classAnnotation->getChildren()[$i]==null){
                $selection[$i]="not";
            }else{
                $selection[$i]=$classAnnotation->getChildren()[$i];
            }
        }
        $form = $this->createForm(AutomaticForm::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $data->createdAt=new DateTime();
            if (!isset($data->author)) $data->author=$this->getUser();
            $data->updatedAt=new DateTime();
            $this->em->persist($data);
            $this->em->flush();
            if ($this->events['create']) {
                $this->dispatcher->dispatch(new $this->events['create']($data));
            }
            $this->addFlash('success','Le contenu est crée avec succès');
            return $this->redirectToRoute($classAnnotation->getRoutePrefix() . '_index');
        }

        return $this->render("@Crudify/crud/new.html.twig", [
            'form' => $form->createView(),
            'routePrefix' => $classAnnotation->getRoutePrefix(),
            'title' => $classAnnotation->getTitle()['add'],
            'formCustom'=> $classAnnotation->getCustomize()['form'],
            'folder'=> $classAnnotation->getCustomize()['folder'],
            'selection' => $selection,
        ]);


    }

    public function crudShow(object $data): Response
    {
        $classAnnotation=$this->annotationReader->getConfig($data);
        for($i=0; $i<count($classAnnotation->getChildren()); $i++){
            if($classAnnotation->getChildren()[$i]==null){
                $selection[$i]="not";
            }else{
                $selection[$i]=$classAnnotation->getChildren()[$i];
            }
        }

        $row = $this->annotationReader->getCrudFieldShow($data);
        if ($classAnnotation->getCustomize()['view'] == true) {
            return $this->render('admin/custom/' . $classAnnotation->getCustomize()['folder']. '/show.html.twig', [
                'row' => $row,
                'routePrefix' => $classAnnotation->getRoutePrefix(),
                'data' => $data,
                'title' => $classAnnotation->getTitle()['show'],
                'selection' => $selection

            ]);
        } else {
            return $this->render('@Crudify/crud/show.html.twig', [
                'row' => $row,
                'routePrefix' => $classAnnotation->getRoutePrefix(),
                'data' => $data,
                'title' => $classAnnotation->getTitle()['show'],
                'selection' => $selection,

            ]);
        }

    }

    public function crudIndex($entityClass,$displaySearch,bool $hasCustomOperation,$customOperation,Request $request, QueryBuilder $query = null): Response
    {
        $classAnnotation=$this->annotationReader->getConfig($entityClass);
        $query = $query ?: $this->getRepository(get_class($entityClass))
            ->createQueryBuilder('row')
            ->orderBy("row.{$classAnnotation->getOrderBy()['attribute']}","{$classAnnotation->getOrderBy()['direction']}");
        if ($request->get('q')) {
            $query = $this->applySearch($request->get('q'),$entityClass, $query);
        }

        $thead = $this->annotationReader->getTableFields($entityClass);
        $theadContent = $this->annotationReader->getTableLabelFields($entityClass);

        $tbody = $this->annotationReader->getTableContentFields($entityClass);

        $sort = $this->annotationReader->getCrudFieldSortBy($entityClass);

        $show = $this->annotationReader->getCrudFieldShow($entityClass);

        if (!empty($show)) $showing = true; else $showing = false;
        if (!empty($sort)) {
            $tri = true;
            $this->paginator->allowSort($sort);
        } else {
            $tri = false;
        }
        if (!$request->get('q')) {
            $count = $this->getRepository(get_class($entityClass))->createQueryBuilder('row')
                ->select('COUNT(row.id) as count')->setMaxResults(1)
                ->getQuery()
                ->getSingleScalarResult();
        } else {
            $query = $this->getRepository(get_class($entityClass))->createQueryBuilder('row');
            $query->select('COUNT(row.id) as count');
            $query->where("LOWER(row.{$this->annotationReader->getConfig($entityClass)->getSearchField()[0]}) LIKE :search0");
            $query->setParameter('search0', "%" . strtolower($request->get('q')) . "%");
            if (count($this->annotationReader->getConfig($entityClass)->getSearchField()) > 0) {
                for ($i = 1; $i < count($this->annotationReader->getConfig($entityClass)->getSearchField()); $i++) {
                    $query->andWhere("LOWER(row.{$this->annotationReader->getConfig($entityClass)->getSearchField()[$i]}) LIKE :search{$i}");
                    $query->setParameter('search{$i}', "%" . strtolower($request->get('q')) . "%");
                }
            }
            $query->setMaxResults(1);
            $count = $query->getQuery()->getSingleScalarResult();


        }
    
        $rows = $this->paginator->paginate($query->getQuery(),$classAnnotation->getPage());
        if ($classAnnotation->getCustomize()['index'] == true) {
            return $this->render("admin/custom/" . $classAnnotation->getCustomize()['folder'] . "/index.html.twig", [
                'rows' => $rows,
                'tri' => $tri,
                'sort' => $sort,
                'showing' => $showing,
                'thead' => $thead,
                'count' => $count,
                'customOperation' => $customOperation,
                'hasCustomOperation' => $hasCustomOperation,
                'displaySearch' => $displaySearch,
                'theadCount' => count($thead) + 1,
                'tbody' => $tbody,
                'routePrefix' => $classAnnotation->getRoutePrefix(),
                'theadContent' => $theadContent,
                'title' => $classAnnotation->getTitle()['index']
            ]);
        }
        return $this->render("@Crudify/crud/index.html.twig", [
            'rows' => $rows,
            'tri' => $tri,
            'sort' => $sort,
            'showing' => $showing,
            'thead' => $thead,
            'count' => $count,
            'customOperation' => $customOperation,
            'hasCustomOperation' => $hasCustomOperation,
            'displaySearch' => $displaySearch,
            'theadCount' => count($thead) + 1,
            'tbody' => $tbody,
            'routePrefix' => $classAnnotation->getRoutePrefix(),
            'theadContent' => $theadContent,
            'title' => $classAnnotation->getTitle()['index']
        ]);
    }

    public function crudEdit(object $data,Request $request): Response
    {
        $classAnnotation=$this->annotationReader->getConfig($data);

        for($i=0; $i<count($classAnnotation->getChildren()); $i++){
            if($classAnnotation->getChildren()[$i]==null){
                $selection[$i]="not";
            }else{
                $selection[$i]=$classAnnotation->getChildren()[$i];
            }
        }
        $form = $this->createForm(AutomaticForm::class, $data);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $data->updatedAt = new DateTime();
            $this->em->flush();
            if ($this->events['update']) {
                $this->dispatcher->dispatch(new $this->events['update']($data));
            }
            $this->addFlash('success', 'Le contenu a bien été modifié');
            return $this->redirectToRoute($classAnnotation->getRoutePrefix() . '_index');

        }
        return $this->render("@Crudify/crud/edit.html.twig", [
            'data' => $data,
            'routePrefix' => $classAnnotation->getRoutePrefix(),
            'form' => $form->createView(),
            'title' => $classAnnotation->getTitle()['edit'],
            'formCustom'=> $classAnnotation->getCustomize()['form'],
            'folder'=> $classAnnotation->getCustomize()['folder'],
            'selection' => $selection,
        ]);


    }

    public function crudDelete(object $entity,Request $request)
    {
        if ($this->isCsrfTokenValid('suppression_token', $request->get('_token'))) {
            $classAnnotation=$this->annotationReader->getConfig($entity);
            $this->em->remove($entity);
            $this->em->flush();
            if ($this->events['delete']) {
                $this->dispatcher->dispatch(new $this->events['delete']($entity));
            }
            $this->addFlash('success', 'Le contenu a bien été supprimé');
            return $this->redirectToRoute($classAnnotation->getRoutePrefix() . '_index');
        }
}

    public function deleteChildren(object $entity)
    {
        $this->em->remove($entity);
        $this->em->flush();
    }

    public function crudClone(object $entity, Request $request): Response
    {
        if (isset($entity->slug)) unset($entity->slug);
        $clone = clone $entity;
        return $this->crudNew($clone,$request);
    }

    public function getRepository($data): ObjectRepository
    {
        return $this->em->getRepository($data);
    }

    protected function applySearch(string $search, $entityClass, QueryBuilder $query): QueryBuilder
    {
        $classAnnotation=$this->annotationReader->getConfig($entityClass);
        $query->where("LOWER(row.{$classAnnotation->getSearchField()[0]}) LIKE :search0");
        $query->setParameter('search0', "%" . strtolower($search) . "%");
        if(count($classAnnotation->getSearchField())>0){
            for ($i=1; $i<count($classAnnotation->getSearchField()); $i++) {
                $query->andWhere("LOWER(row.{$classAnnotation->getSearchField()[$i]}) LIKE :search{$i}");
                $query->setParameter('search{$i}', "%" . strtolower($search) . "%");
            }
        }

        return $query;
    }


}
