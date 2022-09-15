<?php
declare(strict_types=1);

namespace workbunny\IpLocation\provider;

use GeoIp2\Database\Reader;
use GeoIp2\Exception\AddressNotFoundException;
use MaxMind\Db\Reader\InvalidDatabaseException;

class AsnProvider extends AbstractProvider
{

    protected Reader $reader;

    /**
     * @throws InvalidDatabaseException
     */
    public function __construct()
    {
        parent::__construct();
        $this->reader = new Reader($this->path .  '/GeoLite2-ASN.mmdb' , $this->language);
    }
    /**
     * 获取运营商
     * @param string $ip
     * @return string
     * @author yanglong
     * @date 2022年9月14日16:39:14
     */
    public function analysis(string $ip): string
    {
        try {
            $record = $this->reader->asn($ip);
            return $record->autonomousSystemOrganization ?: '--';
        } catch (\Exception $exception) {
            return '局域网';
        }

    }
}