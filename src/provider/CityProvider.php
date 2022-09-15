<?php
declare(strict_types=1);

namespace workbunny\IpLocation\provider;

use GeoIp2\Database\Reader;
use MaxMind\Db\Reader\InvalidDatabaseException;

class CityProvider extends AbstractProvider
{

    protected Reader $reader;

    /**
     * @throws InvalidDatabaseException
     */
    public function __construct()
    {
        parent::__construct();
        $this->reader = new Reader($this->path .  '/GeoLite2-City.mmdb' , $this->language);
    }

    /**
     * 获取城市
     * @param string $ip
     * @return string
     * @author yanglong
     * @date 2022年9月14日16:39:14
     */
    public function analysis(string $ip): string
    {
        try {
            $record = $this->reader->city($ip);
            return $record->city->name ?: '--';
        } catch (\Exception $exception) {
            return '本地';
        }
    }
}