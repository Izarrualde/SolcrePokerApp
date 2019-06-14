<?php

namespace Solcre\lmsuy\Repository;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator as OrmPaginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator;

class BaseRepository extends EntityRepository
{

    protected $filters = [];

    public function addFilter($filter)
    {
        $this->filters[$filter->getName()] = $filter;
    }

    public function findBy(array $params, array $orderBy = null, $limit = null, $offset = null)
    {
        //Pre find by
        $filtersOptions = $this->preFindBy($params, $orderBy, $limit, $offset);
        //Legacy
        if (empty($filtersOptions['fields'])) { 
            $result = parent::findBy($params, $orderBy, $limit, $offset);

        } else {
            //Execute

            $query = $this->getFindByQuery($params, $orderBy, $filtersOptions['fields']);
            $result = $query->getResult();
        }

        //Post find by
        $this->postFindBy($filtersOptions);

        return $result;
    }

    protected function preFindBy(array &$params, array $orderBy = null, $limit = null, $offset = null)
    {
        if (isset($params['query'])) {
            $searchQuery = $params['query'];
        }
        //Prepare filter options
        $filtersOptions = [
            'fields' => isset($params['fields']) ? $params['fields'] : [],
            'expand' => isset($params['expand']) ? $params['expand'] : [],
        ];
        //Remove queries field from array to prevent entity conflicts
        unset($params['query'], $params['fields'], $params['expand'], $params['page'], $params['fetch_all']);

        //Search filter
        if (isset($searchQuery)) {
            //Enable filter
            $this->_em->getFilters()->enable('search');
            //Get search filter
            $searchFilter = $this->_em->getFilters()->getFilter('search');
            //Set filter value
            $searchFilter->setParameter('query', $searchQuery);
        } else {
            if ($this->_em->getFilters()->isEnabled('search')) {
                //Disable filter
                $this->_em->getFilters()->disable('search');
//        }
            }
        }
        return $filtersOptions;
    }

    protected function getFindByQuery(array $params, array $orderBy = null, $fieldsFilterQuery = null)
    {
        //Table alias
        $tableAlias = 'a';

        //Create query
        $qb = $this->createQueryBuilder($tableAlias);

        //Set fields select
        $qb->select($this->getFieldsSelect($tableAlias, $fieldsFilterQuery));

        //Add order by to dql
        if (isset($orderBy) && is_array($orderBy)) {
            foreach ($orderBy as $fieldName => $direction) {
                $qb->addOrderBy($tableAlias . '.' . $fieldName, $direction);
            }
        }

        //Add DQL dates
        $this->setDatesSql($qb, $params, $tableAlias);

        //Add DQL Wheres
        $this->setWhereSql($tableAlias, $qb, $params);

        return $qb->getQuery();
    }

    protected function getFieldsSelect($tableAlias, $fieldsFilterQuery)
    {
        //Select all fields by default
        $fieldsSelect = $tableAlias;

        //Check query fields
        if (! empty($fieldsFilterQuery)) {
            $fieldsFilter = is_string($fieldsFilterQuery) ? explode(',', $fieldsFilterQuery) : $fieldsFilterQuery;


            //parse selection str
            $selectedFields = ["id"];
            $fields = $this->_em->getClassMetadata($this->_entityName)->fieldNames;

            //Foreach field
            foreach ($fields as $key => $fieldName) {
                //Selected field?
                if (in_array($fieldName, $fieldsFilter)) {
                    $selectedFields[] = $fieldName;
                }
            }

            //Selet DQL base query
            $fieldsSelect = [sprintf('partial %s.{%s}', $tableAlias, implode(',', $selectedFields))];
        }

        return $fieldsSelect;
    }

    protected function setDatesSql($qb, &$params, $tableAlias = 'o')
    {

        $params = is_object($params) ? get_object_vars($params) : $params;
        if ($this->isParamSet($params, 'start_date') && $this->isParamSet($params, 'end_date')) {
            $qb->where($tableAlias . '.date BETWEEN :start_date AND :end_date');
            $qb->setParameter('start_date', $params['start_date']);
            $qb->setParameter('end_date', $params['end_date']);
        } else {
            if ($this->isParamSet($params, 'start_date')) {
                $qb->where($tableAlias . '.date >= :start_date');
                $qb->setParameter('start_date', $params['start_date']);
            } else {
                if ($this->isParamSet($params, 'end_date')) {
                    $qb->where($tableAlias . '.date <= :end_date');
                    $qb->setParameter('end_date', $params['end_date']);
                }
            }
        }
        unset($params['start_date'], $params['end_date']);
        return $qb;
    }

    protected function isParamSet(Array $params, $key)
    {
        return (isset($params[$key]) && ! empty($params[$key]));
    }

    protected function setWhereSql($tableAlias, &$qb, $params, Criteria &$criteria = null)
    {
        $addedParams = false;

        //Add DQL Wheres
        if (isset($params) && is_array($params)) {
            $criteria = empty($criteria) ? Criteria::create() : $criteria;

            foreach ($params as $fieldName => $fieldValue) {
                //is null?
                if (is_null($fieldValue)) {
                    $criteria->andWhere(Criteria::expr()->isNull($fieldName));
                } //Select in?
                else {
                    if (is_array($fieldValue)) {
                        $criteria->andWhere(Criteria::expr()->in($fieldName, $fieldValue));
                    } else {
                        $criteria->andWhere(Criteria::expr()->eq($tableAlias . '.' . $fieldName, $fieldValue));
                    }
                }

                $addedParams = true;
            }

            //Add dql criteria
            $qb->addCriteria($criteria);
        }

        return $addedParams;
    }

    protected function postFindBy($filtersOptions, $keepSqlFilters = false)
    {
        //Execute filters
        $this->filter($filtersOptions);

        //Disable filter if is enabled
        if (! $keepSqlFilters && $this->_em->getFilters()->isEnabled('search')) {
            //Disable filter
            $this->_em->getFilters()->disable('search');
        }
    }

    protected function filter(array $options)
    {
        if (count($this->filters) > 0) {
            //Created entity for filters
            $entityName = $this->getEntityName();
            $entity = new $entityName();

            //For each filter
            foreach ($this->filters as $name => $filter) {
                //Is filter interface?
                //if ($filter instanceof FilterInterface) {
                    //Can filter?
                    if ($filter->canFilter($options)) {
                        //Load options
                        $filter->prepareOptions($options);
                        //Filter
                        $filter->filter($entity);
                    } else {
                        //remove filter
                        $filter->removeFilter($entity);
                    }
               // }
            }
        }
    }

    public function findOneBy(array $params, array $orderBy = null)
    {
        //Prepare filter options
        $filtersOptions = [
            'fields' => isset($params['fields']) ? $params['fields'] : [],
            'expand' => isset($params['expand']) ? $params['expand'] : [],
        ];
        //Remove fields to prevent entity conflicts
        unset($params['fields'], $params['expand']);
        //Find one by
        $entity = parent::findOneBy($params, $orderBy);
        //Execute  filters
        $this->filter($filtersOptions);
        return $entity;
    }

    public function findByPaginated(array $params, array $orderBy = null, $limit = null, $offset = null)
    {
        //Pre find by
        $filtersOptions = $this->preFindBy($params, $orderBy, $limit, $offset);

        //Execute
        $query = $this->getFindByQuery($params, $orderBy, $filtersOptions['fields']);

        //Create doctrine paginator
        $doctrinePaginator = $this->getDoctrinePaginator($query);

        //Post find by
        $this->postFindBy($filtersOptions, true);

        return $doctrinePaginator;
    }

    protected function getDoctrinePaginator($query, $fetchJoinCollection = true)
    {
        $ormPaginator = new OrmPaginator($query, $fetchJoinCollection);
        return new DoctrinePaginator($ormPaginator);
    }
}