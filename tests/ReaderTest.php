<?php
declare(strict_types=1);

namespace Tests;

use Workbunny\WebmanIpAttribution\Location;

/**
 * @runTestsInSeparateProcesses
 */
class ReaderTest extends AbstractTest
{

    public function testCreateReader()
    {
        $location = new Location();
        $this->assertCount(0, $location->getReaders());

        $location->createReader(Location::DB_COUNTRY);
        $location->createReader(Location::DB_CITY);
        $location->createReader(Location::DB_ASN);
        $this->assertCount(3, $location->getReaders());
    }

    public function testCreateReaderInstance()
    {
        $location = new Location();
        $this->assertCount(0, $location->getReaders());

        $location->createReader(Location::DB_COUNTRY);
        $this->assertCount(1, $location->getReaders());

        $location->createReader(Location::DB_COUNTRY);
        $location->createReader(Location::DB_COUNTRY);
        $this->assertCount(1, $location->getReaders());

        unset($location);
        gc_collect_cycles();

        $location = new Location();
        $this->assertCount(1, $location->getReaders());
    }

    public function testCreateReaderInvalidArgument()
    {
        $this->expectException(\InvalidArgumentException::class);

        $location = new Location();
        $location->createReader('abc');
        $this->assertCount(0, $location->getReaders());
    }
}
