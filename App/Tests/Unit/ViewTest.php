<?php

namespace Test;

use PHPUnit\Framework\TestCase;

/**
 */
final class ViewTest extends TestCase
{
    public function testRender()
    {
        $this->expectOutputString('this is Sparta!');
        \Framework\View::renderTemplate('Test/index.twig.html', []);
    }
}
