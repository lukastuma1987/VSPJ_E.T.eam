<?php

namespace EditorialBundle\Enum;

abstract class ArticleStatus
{
    const STATUS_DECLINED = 0;
    const STATUS_NEW = 1;
    const STATUS_ASSIGNED = 2;
    const STATUS_REVIEWERS_ASSIGNED = 10;

    private static $statusNames = [
        self::STATUS_DECLINED => 'Zamítnuto',
        self::STATUS_NEW => 'Nový',
        self::STATUS_ASSIGNED => 'Přijato do recenzního řízení',
        self::STATUS_REVIEWERS_ASSIGNED => 'Předáno recenzentům',
    ];

    private static $statusClass = [
        self::STATUS_DECLINED => 'declined',
        self::STATUS_NEW => 'new',
        self::STATUS_ASSIGNED => 'assigned',
        self::STATUS_REVIEWERS_ASSIGNED => 'reviewers-assigned',
    ];

    public static function getStatusName($status)
    {
        return array_key_exists($status, self::$statusNames) ? self::$statusNames[$status] : 'Unknown';
    }

    public static function getStatusClass($status)
    {
        return array_key_exists($status, self::$statusClass) ? self::$statusClass[$status] : 'unknown';
    }
}
