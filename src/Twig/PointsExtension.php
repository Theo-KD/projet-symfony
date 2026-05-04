<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class PointsExtension extends AbstractExtension implements GlobalsInterface
{
    public function getGlobals(): array
    {
        // Plus tard, on mettra les points du client connecté
        return [
            'points' => 150
        ];
    }
}
