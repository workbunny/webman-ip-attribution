<?php
declare(strict_types=1);

namespace Workbunny\WebmanIpAttribution;

use GeoIp2\Database\Reader;
use GeoIp2\Exception\AddressNotFoundException;
use GeoIp2\Model\Asn;
use GeoIp2\Model\City;
use GeoIp2\Model\Country;
use Workbunny\WebmanIpAttribution\Exceptions\IpAttributionException;
use InvalidArgumentException;
use MaxMind\Db\Reader\InvalidDatabaseException;

/**
 * @desc  根据ip获取归宿地
 * @date 2022/9/14
 * @author sunsgne
 */
class Location
{
    const DB_CITY = 'city';
    const DB_COUNTRY = 'country';
    const DB_ASN = 'asn';

    /** @var string  ip数据库文件路径 */
    protected string $path;

    /** @var string[] 语言 */
    protected array $language;

    /** @var string */
    protected string $default;

    /** @var Reader[] */
    protected static array $readers = [];

    /** @var string[]  */
    protected array $db
        = [
            self::DB_CITY    => "/GeoLite2-City.mmdb",
            self::DB_ASN     => "/GeoLite2-ASN.mmdb",
            self::DB_COUNTRY => "/GeoLite2-Country.mmdb",
        ];


    /**
     * @param array|null $config = [
     *  'default'  => '--',
     *  'language' => ['zh-CN'],
     *  'db-country' => './src/GeoLite2-Country.mmdb',
     *  'db-city'    => './src/GeoLite2-Country.mmdb',
     *  'db-asn'     => './src/GeoLite2-Country.mmdb',
     * ]
     */
    public function __construct(?array $config = null)
    {
        /** @var  *path geoip2数据库文件路径 */
        $this->path = dirname(__DIR__) . "/database";
        $config = $config ?? (
            function_exists('config') ?
            config("plugin.workbunny.webman-ip-attribution.app", []) :
            []
        );

        $this->language = $config['language'] ?? ['en'];
        $this->default = $config['default'] ?? '--';

        $this->db[self::DB_COUNTRY] = $config['db-country'] ?? $this->db[self::DB_COUNTRY];
        $this->db[self::DB_CITY] = $config['db-city'] ?? $this->db[self::DB_CITY];
        $this->db[self::DB_ASN] = $config['db-asn'] ?? $this->db[self::DB_ASN];
    }

    /**
     * @param string $db
     * @return Reader
     * @throws InvalidDatabaseException
     * @datetime 2022/09/15 21:24
     * @author chaz6chez<chaz6chez1993@outlook.com>
     */
    public function getReader(string $db): Reader
    {
        if(!isset($this->db[$db])){
            throw new InvalidArgumentException('invalid db.', 1);
        }
        if(!(self::$readers[$db] ?? null) instanceof Reader){
            self::$readers[$db] = new Reader($this->path . '/' . $this->db[$db], $this->language);
        }
        return self::$readers[$db];
    }

    /**
     * @param string|null $db
     * @return void
     * @datetime 2022/09/16 11:12
     * @author chaz6chez<chaz6chez1993@outlook.com>
     */
    public function removeReader(?string $db = null): void
    {
        if($db === null){
            foreach (self::$readers as $reader){
                $reader->close();
            }
            self::$readers = [];
        }elseif(isset(self::$readers[$db])){
            self::$readers[$db]->close();
            unset(self::$readers[$db]);
        }
    }


    /**
     * @param string $ip
     * @return array
     * @datetime 2022/9/14 18:55
     * @author sunsgne
     */
    public function getLocation(string $ip): array
    {
        return [
            self::DB_COUNTRY => ($city = $this->city($ip))->country->name ?? $this->default,
            self::DB_CITY    => $city->city->name ?? $this->default,
            self::DB_ASN     => $this->asn($ip)->autonomousSystemOrganization ?? $this->default,
            'continent'      => $city->continent->name ?? $this->default,
            'timezone'       => $city->location->timeZone ?? $this->default,
        ];
    }


    /**
     * @param string $ip
     * @return City
     * @datetime 2022/9/14 17:22
     * @author sunsgne
     */
    public function city(string $ip): City
    {
        if(filter_var($ip, FILTER_VALIDATE_IP)){
            try {
                return $this->getReader(self::DB_CITY)->city($ip);
            } catch (InvalidDatabaseException $e) {
                throw new IpAttributionException( 'Reader Exceptions. ', -1, $e);
            } catch (AddressNotFoundException $e){
                throw new IpAttributionException( 'Ip Not Found. ', 0, $e);
            }
        }else{
            throw new InvalidArgumentException( 'Invalid ip. ', 1);
        }
    }

    /**
     * @param string $ip
     * @return Asn
     * @datetime 2022/9/14 17:22
     * @author sunsgne
     */
    public function asn(string $ip): Asn
    {
        if(filter_var($ip, FILTER_VALIDATE_IP)){
            try {
                return $this->getReader(self::DB_ASN)->asn($ip);
            } catch (InvalidDatabaseException $e) {
                throw new IpAttributionException( 'Reader Exceptions. ', -1, $e);
            } catch (AddressNotFoundException $e){
                throw new IpAttributionException( 'Ip Not Found. ', 0, $e);
            }
        }else{
            throw new InvalidArgumentException( 'Ip Invalid. ', 1);
        }
    }

    /**
     * @param string $ip
     * @return Country
     * @datetime 2022/9/14 17:22
     * @author sunsgne
     */
    public function country(string $ip): Country
    {
        if(filter_var($ip, FILTER_VALIDATE_IP)){
            try {
                return $this->getReader(self::DB_COUNTRY)->country($ip);
            } catch (InvalidDatabaseException $e) {
                throw new IpAttributionException( 'Reader Exceptions. ', -1, $e);
            } catch (AddressNotFoundException $e){
                throw new IpAttributionException( 'Ip Not Found. ', 0, $e);
            }
        }else{
            throw new InvalidArgumentException( 'Ip Invalid. ', 1);
        }
    }
}
