<?php

namespace App\DataFixtures;

use App\Entity\Currency;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CurrencyFixtures extends Fixture
{
    public const CURRENCY_GBP = 'currency_gbp';
    public const CURRENCY_PLN = 'currency_pln';

    public function load(ObjectManager $manager): void
    {
        $gbp = new Currency();
        $gbp->setCode('GBP');
        $gbp->setName('British Pound');
        $manager->persist($gbp);
        $this->addReference(self::CURRENCY_GBP, $gbp);

        $pln = new Currency();
        $pln->setCode('PLN');
        $pln->setName('Polish Złoty');
        $manager->persist($pln);
        $this->addReference(self::CURRENCY_PLN, $pln);

        $manager->flush();
    }
}
