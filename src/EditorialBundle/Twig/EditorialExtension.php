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
        return $entity ? 'Upravit' : 'Vytvořit';
    }
}
