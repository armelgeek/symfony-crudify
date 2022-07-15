<?php

namespace ArmelWanes\Crudify\Helper\Paginator;

use ArmelWanes\Crudify\Helper\Paginator\PaginatorInterface;
use Doctrine\ORM\Query;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Implémentation basée sur KnpPaginatorBundle
 */
class KnpPaginator implements PaginatorInterface
{

    private  $paginator;
    private  $requestStack;
    private  $sortableFields = [];

    public function __construct(\Knp\Component\Pager\PaginatorInterface $paginator,
RequestStack $requestStack)
    {
        $this->paginator = $paginator;
        $this->requestStack = $requestStack;
    }

    public function paginate(Query $query,$pageCount=10)
    {
        $request = $this->requestStack->getCurrentRequest();
        $page = $request ? $request->query->getInt('page', 1) : 1;
        return $this->paginator->paginate($query, $page,  ($pageCount!=null?$pageCount:10), [
            'sortFieldWhitelist' => $this->sortableFields,
            'filterFieldWhitelist'=> [],
        ]);
    }
     public function allowSort(array $fields)
    {
        $this->sortableFields = array_merge($this->sortableFields, $fields);
        return $this;
    }
}
