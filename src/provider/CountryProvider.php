<?php
declare(strict_types=1);

namespace workbunny\IpLocation\provider;

use GeoIp2\Database\Reader;
use MaxMind\Db\Reader\InvalidDatabaseException;

class CountryProvider extends AbstractProvider
{

    protected Reader $reader;

    /**
     * @throws InvalidDatabaseException
     */
    public function __construct()
    {
        parent::__construct();
        $this->reader = new Reader($this->path .  '/GeoLite2-Country.mmdb' , $this->language);
    }

    /**
     * 解析国家
     * @param string $ip
     * @return string
     * @author yanglong
     * @date 2022年9月14日16:39:14
     */
    public function analysis(string $ip): string
    {
        try {
            $record = $this->reader->country($ip);
            return $record->country->name;
        } catch (\Exception $exception) {
            return "--";
        }

    }
}