<?php

namespace EditorialBundle\Enum;

abstract class ArticleStatus
{
    const STATUS_NEW = 1;
    const STATUS_ASSIGNED = 2;

    private static $statusNames = [
        self::STATUS_NEW => 'Nový',
        self::STATUS_ASSIGNED => 'Přijato do recenzního řízení',
    ];

    private static $statusClass = [
        self::STATUS_NEW => 'new',
        self::STATUS_ASSIGNED => 'assigned',
    ];

    public static function getStatusName($status)
    {
        return array_key_exists($status, self::$statusNames) ? self::$statusNames[$status] : 'Unknown';
    }

    public static function getStatusClass($status)
    {
        return array_key_exists($status, self::$statusClass) ? self::$statusClass[$status] : 'Unknown';
    }
}
