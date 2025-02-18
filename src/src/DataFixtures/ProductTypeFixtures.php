<?php

namespace App\DataFixtures;

use App\Entity\ProductType;
use App\PaymentRule\RuleType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductTypeFixtures extends Fixture
{
    public const PRODUCT_TYPE_SUBSCRIPTION = 'product_type_subscription';
    public const PRODUCT_TYPE_COURSE = 'product_type_course';
    public const PRODUCT_TYPE_CONSULTATION = 'product_type_consultation';

    public function load(ObjectManager $manager): void
    {
        $subscription = new ProductType();
        $subscription->setName('Premium Subscription');
        $subscription->setCode('premium_sub');
        $subscription->setDefaultPaymentRule(RuleType::MONTH_TO_MONTH_RULE->value);
        $manager->persist($subscription);
        $this->addReference(self::PRODUCT_TYPE_SUBSCRIPTION, $subscription);

        $course = new ProductType();
        $course->setName('Training Course');
        $course->setCode('training_course');
        $course->setDefaultPaymentRule(RuleType::DELAY_MONTH_RULE->value);
        $manager->persist($course);
        $this->addReference(self::PRODUCT_TYPE_COURSE, $course);

        $consultation = new ProductType();
        $consultation->setName('Expert Consultation');
        $consultation->setCode('expert_consult');
        $consultation->setDefaultPaymentRule(RuleType::ONE_PAYMENT_RULE->value);
        $manager->persist($consultation);
        $this->addReference(self::PRODUCT_TYPE_CONSULTATION, $consultation);

        $manager->flush();
    }
}
