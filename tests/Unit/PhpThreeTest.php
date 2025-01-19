<?php

namespace iamariezflores\phpthree;

use PHPUnit\Framework\TestCase;

class PhpThreeTest extends TestCase
{
    public function testAddObject()
    {
        $three = new PhpThree();

        $three->addObject(
            'BoxGeometry',
            ['width' => 1, 'height' => 1, 'depth' => 1],
            'MeshBasicMaterial',
            ['color' => '#ff0000'],
            [0, 0, 0]
        );

        $this->assertCount(1, $three->getObjects());
    }
}
