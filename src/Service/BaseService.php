<?php
namespace Solcre\lmsuy\Service;

use Doctrine\ORM\EntityManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator;
use Exception;
use ReflectionClass;

abstract class BaseService
{
    protected $entityManager;
    protected $repository;
    protected $entityName;
    protected $configuration;
    protected $currentPage = 1;
    protected $itemsCountPerPage = 25;

    public function __construct(EntityManager $entityManager)
    {
        $this->configuration = array();
        $this->entityManager = $entityManager;
        $this->entityName = $this->getEntityName();
        $this->repository = $this->entityManager->getRepository($this->entityName);
    }

    public function getEntityName()
    {
        $namespaceName = (new ReflectionClass($this))->getNamespaceName();
        $className = (new ReflectionClass($this))->getShortName();
        if (substr_count($className, 'Service') > 1) {
            $pos = strrpos($className, "Service");
            if ($pos !== false) {
                $entityName = substr_replace($className, '', $pos, strlen("Service"));
            }
        } else {
            $entityName = substr($className, 0, strpos($className, "Service"));
        }
        $entityNamespace = str_replace('Service', 'Entity', $namespaceName);
        return $entityNamespace . '\\' . $entityName . "Entity";
    }

    public function getEntityManager()
    {
        return $this->entityManager;
    }

    public function setEntityManager($entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getRepository()
    {
        return $this->repository;
    }

    public function setRepository($repository)
    {
        $this->repository = $repository;
    }

    public function getConfiguration()
    {
        return $this->configuration;
    }

    public function setConfiguration($configuration)
    {
        $this->configuration = $configuration;
    }

    public function addFilter(FilterInterface $filter)
    {
        if ($this->repository instanceof BaseRepository) {
            $this->repository->addFilter($filter);
        }
    }

    public function add($data, $strategies = null)
    {
        $entityObj = $this->prepareEntity($data, $strategies);
        $this->entityManager->persist($entityObj);
        $this->entityManager->flush();
        return $entityObj;
    }

    public function prepareEntity($data, $strategies = null)
    {
        $class = $this->getEntityName();
        $hydrator = new DoctrineObject($this->entityManager);
        if (!empty($strategies)) {
            foreach ($strategies as $strategy) {
                $hydrator->addStrategy($strategy['field'], $strategy['strategy']);
            }
        }
        return $hydrator->hydrate($data, new $class);
    }

    public function fetchOne($id, $params = [])
    {
        $params['id'] = $id;
        return $this->repository->findOneBy($params);
    }

    public function fetchBy($params = [], $orderBy = null)
    {
        return $this->repository->findOneBy($params, $orderBy);
    }

    public function fetchAll($params = null, $orderBy = null)
    {
        if (!empty($params) || !empty($orderBy)) {
            return $this->repository->findBy((array)$params, $orderBy);
        }
        return $this->repository->findAll();
    }

    public function delete($id, $entityObj = null)
    {
        if (empty($entityObj)) {
            $entityObj = $this->fetch($id);
        }
        $this->entityManager->remove($entityObj);
        $this->entityManager->flush();
        return true;
    }

    public function fetch($id)
    {
        $entity = $this->repository->find($id);
        if (!empty($entity) && $entity instanceof $this->entityName) {
            return $entity;
        } 
        throw new Exception($this->entityName . " Entity not found", 404);
        
    }

    public function update($id, $data)
    {
        throw new Exception("Method not implemented", 400);
    }

    public function getReference($id)
    {
        if (empty($id)) {
            return null;
        }
        return $this->entityManager->getReference($this->entityName, $id);
    }
}