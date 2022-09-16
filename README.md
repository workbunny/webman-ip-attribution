<p align="center"><img width="260px" src="https://chaz6chez.cn/images/workbunny-logo.png" alt="workbunny"></p>

**<p align="center">workbunny/webman-ip-attribution</p>**

**<p align="center">🐇  Webman plugin for IP attribution query. 🐇</p>**

# Webman plugin for IP attribution query.

<div align="center">
    <a href="https://github.com/workbunny/webman-ip-attribution/actions">
        <img src="https://github.com/workbunny/webman-ip-attribution/actions/workflows/CI.yml/badge.svg" alt="Build Status">
    </a>
    <a href="https://github.com/workbunny/webman-ip-attribution/releases">
        <img alt="Latest Stable Version" src="http://poser.pugx.org/workbunny/webman-ip-attribution/v">
    </a>
    <a href="https://github.com/workbunny/webman-ip-attribution/blob/main/composer.json">
        <img alt="PHP Version Require" src="http://poser.pugx.org/workbunny/webman-ip-attribution/require/php">
    </a>
    <a href="https://github.com/workbunny/webman-ip-attribution/blob/main/LICENSE">
        <img alt="GitHub license" src="http://poser.pugx.org/workbunny/webman-ip-attribution/license">
    </a>
</div>


## 简介

- `/database` 最后更新时间：2022-09-14 **(每周五通过小版本更新)**
- `webman-ip-attribution` 是基于`geoip2`作为底层，依托 `mmdb数据库` 查询ip归属地及asn信息
- 本项目基于[geoip2/geoip2](https://github.com/maxmind/GeoIP2-php)，感谢[MaxMind](https://github.com/maxmind) 工作组的开源
## 安装
```shell
composer require workbunny/webman-ip-attribution
```
## 使用

### 范例
```php
use Workbunny\WebmanIpAttribution\Location;

try {
     $location = new Location();
    var_dump($location->getLocation("1.1.1.1"));
    // array:3 [
    //"country" => "韩国"
    //"city" => null
    //"asn" => "NAVER Cloud Corp."]
]

    
 }catch (\Workbunny\WebmanIpAttribution\exception\IpLocationException $exception){
 
 }
```

### 获取IP所在城市
```php
use Workbunny\WebmanIpAttribution\Location;

try {
   $location = new Location();
    var_dump($location->city("1.1.1.1")->city->name);
    //string > 北京市
 }catch (\Workbunny\WebmanIpAttribution\exception\IpLocationException $exception){
 
 }
```
### 获取IP地址ASN信息
```php
use Workbunny\WebmanIpAttribution\Location;

try {
    $location = new Location();
    var_dump($location->asn("1.1.1.1")->autonomousSystemOrganization);
    //string > NAVER Cloud Corp.
 }catch (\Workbunny\WebmanIpAttribution\exception\IpLocationException $exception){
 
 }
```
### 获取IP所在国家
```php
use Workbunny\WebmanIpAttribution\Location;

try {
    $location = new Location();
    var_dump($location->country("1.1.1.1")->country->name);
    //string > 中国
 }catch (\Workbunny\WebmanIpAttribution\exception\IpLocationException $exception){
 
 }
```

更多用法和示例参照[geoip2/geoip2](https://github.com/maxmind/GeoIP2-php)；


## webman 中使用

### config配置文件
```php
return [
    'enable' => true,
    
    'default'  => '--',
    'language' => ['zh-CN'],

    'db-country' => null,
    'db-city'    => null,
    'db-asn'     => null,
];

```
### 应用
```php
use Workbunny\WebmanIpAttribution\Location;

try {
    $location = G(Location::class);
    var_dump($location->country("1.1.1.1"));
    var_dump($location->asn("1.1.1.1"));
    var_dump($location->city("1.1.1.1"));
 }catch (\Workbunny\WebmanIpAttribution\exception\IpLocationException $exception){
 
 }
```
### 运行
```shell
./webman start
OR
php start.php start
```
