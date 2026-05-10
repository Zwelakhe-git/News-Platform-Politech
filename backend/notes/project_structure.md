first, the composer.json file.
check your psr-4
```json
"psr-4": {
    "Thunderpc\\Htdocs\\": "src/",
    "Thunderpc\\Vkurse\\": "VKurse/src",
    "Thunderpc\\Vkurse\\Tests\\": "VKurse/tests"
}
```
check the file in the VKurser/src folder. give it the namespace `Thunderpc\Vkurse`.
if there are any subfolders in the src folder, give the files there the namespace
`Thunderpc\Vkurse\Subfoldername` - first letter is in upper case. if there are any sub folders in that, chain them.

give the files in the tests folder the namespace `Thunderpc\Tests`.
include the autoloader from the the folder where the composer.json is located.

## the file name should match the class name, с учетом регистра. но это не обязятельно для тестового файла.
for example.
VKurse/src/admin/utils
```php
// Utils.php
namespace Thunderpc\Vkurse\Admin\Utils;
class Utils{
    ...
}
```

VKurse/test
```php
//filenameTest.php
namespace Thunderpc\Vkurse\Tests;
use Thunderpc\Vkurse\Admin\Utils\Utils;
```
