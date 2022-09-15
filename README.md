# ip归宿地

## 配置
```php
return [
    'enable' => true,
    //加载配置项
    'config'  => "default",
    'default' => [
        //语言
        "language" => ['zh-CN'],
        //mdb数据路径,(默认不指定，加载包内库)
        "mdbFileDir" => "",
        //未获取时默认标识
        "defaultIdentifier" => "NON"
    ],
];

```

## 示例

### 获取ip所在城市
```php
use workbunny\IpLocation\Location;

try {

    $location = Location::$instance;

    var_dump($location->city("1.1.1.1"));
    
 }catch (\workbunny\IpLocation\exception\IpLocationException $exception){
 
 }

```
