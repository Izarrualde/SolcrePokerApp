<?php
namespace Solcre\lmsuy\Service;

use \Solcre\lmsuy\Entity\UserEntity;
use Doctrine\ORM\EntityManager;
use Solcre\lmsuy\Exception\UserHadActionException;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;

class UserService extends BaseService
{

    public function __construct(EntityManager $em)
    {
        parent::__construct($em);
    }

    public function add($data, $strategies = null)
    {
        $user = new UserEntity();
        $user->setPassword($data['password']);
        $user->setName($data['firstname']);
        $user->setLastname($data['lastname']);
        $user->setEmail($data['email']);
        $user->setUsername($data['username']);
        $user->setMultiplier($data['multiplier']);
        $user->setIsActive($data['active']);
        $user->setHours($data['hours']);
        $user->setPoints($data['points']);
        $user->setSessions($data['sessions']);
        $user->setResults($data['results']);
        $user->setCashin($data['cashin']);

        $this->entityManager->persist($user);
        $this->entityManager->flush($user);
    }

    public function update($data, $strategies = null)
    {
        $user = parent::fetch($data['id']);
        $user->setName($data['name']);
        $user->setLastname($data['lastname']);
        $user->setEmail($data['email']);
        $user->setUsername($data['username']);

        $this->entityManager->persist($user);
        $this->entityManager->flush($user);
    }

    public function delete($id, $entityObj = null)
    {

        $user = $this->entityManager->getReference('Solcre\lmsuy\Entity\UserEntity', $id);

        try {
            $this->entityManager->remove($user);
            $this->entityManager->flush();
        } catch (ForeignKeyConstraintViolationException $e) {
             throw new UserHadActionException();
        }
    }
}
