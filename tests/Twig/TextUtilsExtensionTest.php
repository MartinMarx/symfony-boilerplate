<?php

namespace App\Tests\Twig;

use App\Twig\TextUtilsExtension;
use PHPUnit\Framework\TestCase;

class TextUtilsExtensionTest extends TestCase
{
    public function testTruncate()
    {
        $extension = new TextUtilsExtension();

        $text = '';
        $expectedLength = 10;
        $ellipsis = 'TEST';

        for ($i = 0; $i < $expectedLength * 2; ++$i) {
            $text .= "$i";
        }
        $result = $extension->truncate($text, $expectedLength, $ellipsis);

        $this->assertEquals($expectedLength, strlen($result), sprintf('Result string : %s', $result));
        $this->assertStringEndsWith($ellipsis, $result);
    }
}
