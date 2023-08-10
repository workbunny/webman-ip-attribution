<?php
declare(strict_types=1);

namespace Tests;

use Workbunny\WebmanIpAttribution\Exceptions\IpAttributionException;
use Workbunny\WebmanIpAttribution\Location;

class LocationTest extends AbstractTest
{

    public function testGetLocationByV4()
    {
        $res = (new Location())->getLocation('8.8.8.8');

        $this->assertEquals([
            'country' => 'United States',
            'city' => 'Los Angeles',
            'asn' => 'GOOGLE',
            'continent' => 'North America',
            'timezone' => 'America/Chicago',
        ], $res);
    }

    public function testGetLocationByV6()
    {
        $res = (new Location())->getLocation('::0808:0808');

        $this->assertEquals([
            'country' => 'United States',
            'city' => 'Los Angeles',
            'asn' => 'GOOGLE',
            'continent' => 'North America',
            'timezone' => 'America/Chicago',
        ], $res);
    }

    public function testGetLocationLocalIpV4()
    {
        $this->expectException(IpAttributionException::class);
        $this->expectExceptionMessage('Ip Not Found.');
        $this->expectExceptionCode(0);

        $res = (new Location())->getLocation('127.0.0.1');
        $this->assertEquals([], $res);
    }

    public function testGetLocationLocalIpV6()
    {
        $this->expectException(IpAttributionException::class);
        $this->expectExceptionMessage('Ip Not Found.');
        $this->expectExceptionCode(0);

        $res = (new Location())->getLocation('::7f00:0001');
        $this->assertEquals([], $res);
    }

    public function testGetLocationPrivateIpV4()
    {
        $this->expectException(IpAttributionException::class);
        $this->expectExceptionMessage('Ip Not Found.');
        $this->expectExceptionCode(0);

        $res = (new Location())->getLocation('192.168.0.1');
        $this->assertEquals([], $res);
    }

    public function testGetLocationPrivateIpV6()
    {
        $this->expectException(IpAttributionException::class);
        $this->expectExceptionMessage('Ip Not Found.');
        $this->expectExceptionCode(0);

        $res = (new Location())->getLocation('::c0a8:0001');
        $this->assertEquals([], $res);
    }
}
