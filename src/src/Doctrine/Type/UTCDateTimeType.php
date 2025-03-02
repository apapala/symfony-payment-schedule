<?php

declare(strict_types=1);

namespace App\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\DateTimeType;
use Doctrine\DBAL\Types\Exception\InvalidFormat;
use Doctrine\DBAL\Types\Exception\InvalidType;

class UTCDateTimeType extends DateTimeType
{
    private static ?\DateTimeZone $utc = null;

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if (null === $value) {
            return $value;
        }

        if ($value instanceof \DateTime) {
            return $value->setTimezone($this->getUtc())->format($platform->getDateTimeFormatString());
        }

        throw InvalidType::new($value, static::class, ['null', \DateTime::class]);
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?\DateTime
    {
        if (null === $value || $value instanceof \DateTime) {
            return $value;
        }

        $dateTime = \DateTime::createFromFormat(
            $platform->getDateTimeFormatString(),
            $value, $this->getUtc()
        );

        if (false !== $dateTime) {
            return $dateTime;
        }

        try {
            return new \DateTime($value, $this->getUtc());
        } catch (\Throwable $e) {
            throw InvalidFormat::new($value, static::class, $platform->getDateTimeFormatString(), $e);
        }
    }

    private function getUtc(): \DateTimeZone
    {
        return self::$utc ??= new \DateTimeZone('UTC');
    }
}
