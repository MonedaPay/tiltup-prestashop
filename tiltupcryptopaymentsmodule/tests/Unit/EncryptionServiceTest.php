<?php

use \PHPUnit\Framework\TestCase;

class EncryptionServiceTest extends TestCase
{
    public function testGenerateHmac()
    {
        // When
        $actual = EncryptionService::generateHmac('test');
    }
}
