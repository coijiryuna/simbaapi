<?php

use PHPUnit\Framework\TestCase;
use simba\api\Laravel\SimbaManager;
use simba\api\Libraries\Muzakki;

final class SimbaManagerTest extends TestCase
{
    public function testMuzakkiForwarding()
    {
        $manager = new SimbaManager(null);
        $muzakki = $manager->muzakki();

        $this->assertInstanceOf(Muzakki::class, $muzakki);
    }
}
