# ip归宿地

## 示例

```php
use workbunny\IpLocation\Location;

try {

    $location = Location::$instance;

    var_dump($location->city("1.1.1.1"));
 }catch (\workbunny\IpLocation\exception\IpLocationException $exception)
 {
 
 }

```
