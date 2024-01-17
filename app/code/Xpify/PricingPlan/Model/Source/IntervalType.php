<?php
declare(strict_types=1);

namespace Xpify\PricingPlan\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

class IntervalType implements OptionSourceInterface
{
    const INTERVAL_EVERY_30_DAYS = 'EVERY_30_DAYS';
    const INTERVAL_ANNUAL = 'ANNUAL';
    const INTERVAL_ONE_TIME = 'ONE_TIME';

    private static $mapping = [
        self::INTERVAL_EVERY_30_DAYS => 'Every 30 days',
        self::INTERVAL_ANNUAL => 'Annual',
        self::INTERVAL_ONE_TIME => 'One time',
    ];

    /**
     * @param string $key
     * @return string
     */
    public static function getIntervalLabel(string $key): string
    {
        return isset(self::$mapping[$key]) ? __(self::$mapping[$key])->render() : $key;
    }

    /**
     * Check provided key is valid or not
     *
     * @param string $key
     * @return bool
     */
    public static function isValidInterval(string $key): bool
    {
        return isset(self::$mapping[$key]);
    }

    /**
     * @return array[]
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::INTERVAL_ONE_TIME,
                'label' => self::getIntervalLabel(self::INTERVAL_ONE_TIME),
            ],
            [
                'value' => self::INTERVAL_EVERY_30_DAYS,
                'label' => self::getIntervalLabel(self::INTERVAL_EVERY_30_DAYS),
            ],
            [
                'value' => self::INTERVAL_ANNUAL,
                'label' => self::getIntervalLabel(self::INTERVAL_ANNUAL),
            ],
        ];
    }
}
