# ExportHelper
Simple class for easy create ZIP archives with CSV files.

Include class:
```php
require "ExportModule.php";
$exportModule = new ExportModule;
```

You can add some settings:
```php
$exportModule->setMemoryLimit("512M");
$exportModule->setDelimeter(';');
$exportModule->setEnclosure('"');
$exportModule->setChmodMode(0755);
$exportModule->setSavePath('/var/www/repo');
```

And you can get them:
```php
$exportModule->getSavePath();
```

Add some text data:
```php
$data = [
	['foo', 'bar', 'baz', 'qwe', 'asd', 'zxc'], //row
	['foo', 'bar', 'baz', 'qwe', 'asd', 'zxc'], //another one
	['foo', 'bar', 'baz', 'qwe', 'asd', 'zxc'], //...
];

$exportModule->addFile($data);
```

You can add a specific file to:
```php
$exportModule->addSpecificFile($filepath);
```

And more...
```php
$exportModule
	->addSpecificFile($someFile)
	->addFile($fooData)
	->addFile($barData)
	->addFile($bazData)
	->addSpecificFile($anotherFile);
```

And get you archive
```php
echo  $exportModule->makeZip('archive.zip'); // /var/www/repo/archive.zip
```

And you can download it forcefully:
```php
$exportModule->downloadArchive();
```

Or use it in case:
```php
$exportModule
	->addFile($foo)
	->addFile($bar)
	->makeZip();

$exportModule->downloadArchive();
```

You can use static func to force download any files
```php
ExportModule::forceDownload($filename);
```