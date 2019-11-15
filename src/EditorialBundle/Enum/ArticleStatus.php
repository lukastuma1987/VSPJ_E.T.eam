<?php

namespace EditorialBundle\Enum;

abstract class ArticleStatus
{
    const STATUS_DECLINED = 0;
    const STATUS_NEW = 1;
    const STATUS_ASSIGNED = 2;
    const STATUS_REVIEWERS_ASSIGNED = 10;
    const STATUS_REVIEWS_FILLED = 20;
    const STATUS_RETURNED = 30;
    const STATUS_NEW_VERSION = 40;
    const STATUS_ACCEPTED = 50;

    private static $statusNames = [
        self::STATUS_DECLINED => 'Zamítnuto',
        self::STATUS_NEW => 'Nový',
        self::STATUS_ASSIGNED => 'Přijato do recenzního řízení',
        self::STATUS_REVIEWERS_ASSIGNED => 'Předáno recenzentům',
        self::STATUS_REVIEWS_FILLED => 'Hodnocení vyplněna',
        self::STATUS_RETURNED => 'Vráceno k přepracování',
        self::STATUS_NEW_VERSION => 'Nová verze',
        self::STATUS_ACCEPTED => 'Přijato',
    ];

    private static $statusClass = [
        self::STATUS_DECLINED => 'declined',
        self::STATUS_NEW => 'new',
        self::STATUS_ASSIGNED => 'assigned',
        self::STATUS_REVIEWERS_ASSIGNED => 'reviewers-assigned',
        self::STATUS_REVIEWS_FILLED => 'reviews-filled',
        self::STATUS_RETURNED => 'returned',
        self::STATUS_NEW_VERSION => 'new-version',
        self::STATUS_ACCEPTED => 'accepted',
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
