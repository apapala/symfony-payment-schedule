<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\PaymentInstruction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PaymentInstructionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PaymentInstruction::class);
    }

    public function store(PaymentInstruction $instruction): void
    {
        $this->getEntityManager()->persist($instruction);
        $this->getEntityManager()->flush();
    }
}
