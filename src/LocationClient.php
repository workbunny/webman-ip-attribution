<?php
declare(strict_types=1);

namespace workbunny\IpLocation;

use workbunny\IpLocation\exception\IpLocationException;
use workbunny\IpLocation\provider\AsnProvider;
use workbunny\IpLocation\provider\CityProvider;
use workbunny\IpLocation\provider\CountryProvider;

/**
 * @purpose 根据ip地址解析国家地区和运营商
 * @author yanglong
 * @date 2022年9月14日16:38:53
 * @property CityProvider $city
 * @property CountryProvider $country
 * @property AsnProvider $asn
 */
class LocationClient
{
    /**
     * 别名
     * @var array|string[]
     */
    protected array $alias
        = [
            'city'    => CityProvider::class,
            'country' => CountryProvider::class,
            'asn'     => AsnProvider::class,
        ];

    /**
     * 配置
     * @var array
     */
    protected array $configs = [];

    /**
     * 提供者存储数组
     * @var array
     */
    protected array $providers = [];

    /**
     * 初始化
     * @param array|null $configs
     */
    public function __construct(?array $configs = null)
    {
        $this->configs = $configs ?? [];
    }

    /**
     * 调用提供者
     * @param string $name
     * @return mixed
     */
    public function __get(string $name)
    {
        if (!isset($name) || !isset($this->alias[$name])) {
            throw new IpLocationException("{$name} is invalid.");
        }

        if (isset($this->providers[$name])) {
            return $this->providers[$name];
        }
        $class = $this->alias[$name];
        return $this->providers[$name] = $this->configs ?
            new $class($this, $this->configs) :
            new $class($this);
    }
}