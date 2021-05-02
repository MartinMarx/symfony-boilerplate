<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TextUtilsExtension extends AbstractExtension
{
    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): iterable
    {
        yield new TwigFunction('truncate', [$this, 'truncate']);
    }

    public function truncate(string $text, int $length, string $ellipsis = '...'): string
    {
        if (strlen($text) <= $length) {
            return $text;
        }

        return substr($text, 0, $length - strlen($ellipsis)).$ellipsis;
    }
}
