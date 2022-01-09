<?php

namespace App\Repository;

use App\Entity\Event;
use App\Entity\Gift;
use App\Entity\Recipient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Gift|null find($id, $lockMode = null, $lockVersion = null)
 * @method Gift|null findOneBy(array $criteria, array $orderBy = null)
 * @method Gift[]    findAll()
 * @method Gift[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GiftRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Gift::class);
    }

    public function findByRecipientAndEvent(
        Recipient $recipient,
        Event $event
    ): array {
        return $this
            ->createQueryBuilder('g')
            ->andWhere(':recipient member of g.recipients')
            ->andWhere('g.eventYear = :year')
            ->setParameters([
                'recipient' => $recipient,
                'year' => $event->getYear(),
            ])
            ->getQuery()
            ->getResult()
        ;
    }
}
