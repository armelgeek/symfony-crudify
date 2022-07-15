<?php

namespace ArmelWanes\Crudify\Helper\Paginator;

use Doctrine\ORM\Query;

/**
 * Interface pour la pagination permettant de faire le lien avec la librairie externe
 */
interface PaginatorInterface
{

   public function allowSort(array $fields);

    public function paginate(Query $query,$pageCount=10);

}
