<?php
declare(strict_types=1);

namespace Tests;

use Workbunny\WebmanIpAttribution\Location;

/**
 * @runTestsInSeparateProcesses
 */
class ReaderTest extends AbstractTest
{

    /**
     * 普通的创建reader测试
     * @return void
     */
    public function testCreateReader()
    {
        $location = new Location();
        $this->assertCount(0, $location->getReaders());

        $location->createReader(Location::DB_COUNTRY);
        $location->createReader(Location::DB_CITY);
        $location->createReader(Location::DB_ASN);
        $this->assertCount(3, $location->getReaders());
    }

    /**
     * 创建reader的单例测试
     * @return void
     */
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

    /**
     * 使用非法参数创建reader的测试
     * @return void
     */
    public function testCreateReaderInvalidArgument()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('invalid db.');
        $this->expectExceptionCode(1);

        $location = new Location();
        $location->createReader('abc');
        $this->assertCount(0, $location->getReaders());
    }

    /**
     * 移除所有reader的测试
     * @return void
     */
    public function testRemoveReader()
    {
        $location = new Location();
        $location->createReader(Location::DB_COUNTRY);
        $location->createReader(Location::DB_CITY);
        $location->createReader(Location::DB_ASN);
        $this->assertCount(3, $location->getReaders());

        $location->removeReader();
        $this->assertCount(0, $location->getReaders());
    }

    /**
     * 移除单个reader的测试
     * @return void
     */
    public function testRemoveReaderBySingle()
    {
        $location = new Location();
        $location->createReader(Location::DB_COUNTRY);
        $location->createReader(Location::DB_CITY);
        $location->createReader(Location::DB_ASN);
        $this->assertCount(3, $location->getReaders());

        $location->removeReader(Location::DB_ASN);
        $this->assertCount(2, $location->getReaders());

        $location->removeReader(Location::DB_ASN);
        $this->assertCount(2, $location->getReaders());

        $location->removeReader(Location::DB_COUNTRY);
        $this->assertCount(1, $location->getReaders());
    }
}
