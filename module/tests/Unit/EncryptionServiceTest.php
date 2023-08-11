<?php

use \PHPUnit\Framework\TestCase;

class EncryptionServiceTest extends TestCase
{
    public function testGenerateHmac()
    {
        $actual = EncryptionService::generateHmac('test');
    }
}
