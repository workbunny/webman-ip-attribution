<?php
declare(strict_types=1);

namespace workbunny\IpLocation;

use GeoIp2\Database\Reader;
use GeoIp2\Exception\AddressNotFoundException;
use MaxMind\Db\Reader\InvalidDatabaseException;
use workbunny\IpLocation\exception\IpLocationException;

/**
 * @desc  根据ip获取归宿地
 * @date 2022/9/14
 * @author sunsgne
 */
class Location
{



    /** @var string  ip数据库文件路径 */
    protected string $path;

    /** @var array|mixed|string[] 语言 */
    protected array $language = ['zh-CN'];

    protected string $defaultIdentifier = "--";

    protected static $readers;

    protected static $readerCity;
    protected static $readerAsn;
    protected static $readerCountry;


    private function __construct()
    {
        /** @var  *path geoip2数据库文件路径 */
        $this->path = dirname(__DIR__) . "/src/libs";

        if (function_exists('config')) {
            $default = config("plugin.sunsgne.ip-location.app.config") ?? "default";
            $config  = config("plugin.sunsgne.ip-location.app." . $default) ?? [];

            $this->language = isset($config["language"])
                ? (!empty($config["language"]) ? $config["language"] : $this->language)
                : $this->language;

            $this->path = isset($config["mdbFileDir"])
                ? (!empty($config["mdbFileDir"]) ? $config["mdbFileDir"] : $this->path)
                : $this->path;

            $this->defaultIdentifier = isset($config["defaultIdentifier"])
                ? (!empty($config["defaultIdentifier"]) ? $config["defaultIdentifier"] : $this->defaultIdentifier)
                : $this->defaultIdentifier;
        }

    }

    /**
     * @param string $dbName
     * @param string $readerType
     * @return void
     * @throws InvalidDatabaseException
     * @datetime 2022/9/15 15:01
     * @author zhulianyou
     */
    private function instance(string $dbName , string $readerType)
    {

        if (!self::$readers[$readerType] instanceof Reader)
        {
            self::$readers[$readerType] = new Reader($this->path . $dbName , $this->language);
        }
    }


    /**
     * @param string $ipAddress
     * @return array
     * @datetime 2022/9/14 18:55
     * @author sunsgne
     */
    public function getLocation(string $ipAddress): array
    {
        return [
            "country" => $this->country($ipAddress),
            "city"    => $this->city($ipAddress),
            "asn"     => $this->asn($ipAddress)
        ];
    }


    /**
     * @param string $ipAddress
     * @return string
     * @datetime 2022/9/14 17:22
     * @author sunsgne
     */
    public function city(string $ipAddress): string
    {
        if (false === $this->verifyIp($ipAddress)) {
            throw new IpLocationException('Please check whether the parameter ip address conforms to the ip number segment specification.');
        }

        try {
            $this->instance( '/GeoLite2-City.mmdb' , "city");


            $reader = self::$readers["city"]->city($ipAddress);


//            $reader = new Reader($this->path . '/GeoLite2-City.mmdb', ['zh-CN']);
//            $record = $reader->city($ipAddress);
            return $reader->city->name ?? $this->defaultIdentifier;
        } catch (InvalidDatabaseException|AddressNotFoundException $e) {
            throw new IpLocationException($e->getMessage(), $e->getCode());
        }
    }


    /**
     * @param string $ipAddress
     * @return false|mixed|string
     * @datetime 2022/9/14 18:44
     * @author sunsgne
     */
    public function asn(string $ipAddress)
    {
        if (false === $this->verifyIp($ipAddress)) {
            throw new IpLocationException('Please check whether the parameter ip address conforms to the ip number segment specification.');
        }

        try {
            $reader = new Reader($this->path . '/GeoLite2-ASN.mmdb', ['zh-CN']);
            $record = $reader->asn($ipAddress);
            return $record->autonomousSystemOrganization ?? $this->defaultIdentifier;
        } catch (AddressNotFoundException|InvalidDatabaseException $e) {
            throw new IpLocationException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param string $ipAddress
     * @return string
     * @datetime 2022/9/14 18:44
     * @author sunsgne
     */
    public function country(string $ipAddress): string
    {
        if (false === $this->verifyIp($ipAddress)) {
            throw new IpLocationException('Please check whether the parameter ip address conforms to the ip number segment specification.');
        }
        try {
            $reader = new Reader($this->path . '/GeoLite2-Country.mmdb', ['zh-CN']);
            $record = $reader->country($ipAddress);
            return $record->country->name ?? $this->defaultIdentifier;
        } catch (AddressNotFoundException|InvalidDatabaseException $e) {
            throw new IpLocationException($e->getMessage(), $e->getCode());
        }
    }


    /**
     * 验证IP是否合法
     * @param string $ip ip地址（0.0.0.0-255.255.255.255）
     * @return bool
     * @datetime 2022/9/14 18:34
     * @author sunsgne
     */
    public function verifyIp(string $ip): bool
    {
        return (bool)preg_match('/^(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)(?:[.](?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)){3}$/', $ip);
    }
}