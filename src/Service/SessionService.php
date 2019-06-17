<?php
Namespace Solcre\lmsuy\Service;

Use \Solcre\lmsuy\Entity\SessionEntity;
Use Doctrine\ORM\EntityManager;

class SessionService extends BaseService {

	public function __construct(EntityManager $em)
	{
		parent::__construct($em);
	}

	public function add($data, $strategies = null)
	{
        $data['date'] = new \DateTime($data['date']);
        $data['startTime'] = new \DateTime($data['startTime']);
        $data['startTimeReal'] = new \DateTime($data['startTimeReal']);
        $data['endTime'] = new \DateTime($data['endTime']);

		$session = new SessionEntity();
		$session->setDate($data['date']);
		$session->setTitle($data['title']);
		$session->setDescription($data['description']);
		$session->setSeats($data['seats']);
		$session->setStartTime($data['startTime']);
		$session->setStartTimeReal($data['startTimeReal']);
		$session->setEndTime($data['endTime']);

		$this->entityManager->persist($session);
		$this->entityManager->flush($session);
	}

	public function update($data, $strategies = null)
	{

        $data['created_at'] = new \DateTime($data['created_at']);
        $data['start_at'] = new \DateTime($data['start_at']);
        $data['real_start_at'] = new \DateTime($data['real_start_at']);
        $data['end_at'] = new \DateTime($data['end_at']);

		$session = parent::fetch($data['idSession']);
		$session->setDate($data['created_at']);
		$session->setTitle($data['title']);
		$session->setDescription($data['description']);
		$session->setSeats($data['count_of_seats']);
		$session->setStartTime($data['start_at']);
		$session->setStartTimeReal($data['real_start_at']);
		$session->setEndTime($data['end_at']);

		$this->entityManager->persist($session);
		$this->entityManager->flush($session);
	}

	public function delete($id, $entityObj = null)
	{	
		$session = $this->entityManager->getReference('Solcre\lmsuy\Entity\SessionEntity', $id);

		$this->entityManager->remove($session);
		$this->entityManager->flush();
	}
}