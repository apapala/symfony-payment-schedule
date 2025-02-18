<?php

namespace App\PaymentRule;

enum RuleType: string
{
    case JUNE_RULE = 'june_rule';
    case MONTH_TO_MONTH_RULE = 'month_to_month_rule';
    case DELAY_MONTH_RULE = 'delay_month_rule';
    case ONE_PAYMENT_RULE = 'one_payment_rule';
}
