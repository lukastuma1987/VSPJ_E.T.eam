<?php

namespace EditorialBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class EditorialExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('operation', [$this, 'getOperation']),
        ];
    }

    public function getOperation($entity)
    {
        $exists = $entity && is_object($entity) && method_exists($entity, 'getId') && $entity->getId();

        return $exists ? 'Upravit' : 'Vytvořit';
    }
}
