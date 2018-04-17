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

And more...
```php
$exportModule
	->addFile($fooData)
	->addFile($barData)
	->addFile($bazData);
```

And get you archive
```php
$archive = $exportModule->makeZip('archive.zip');
echo $archive; // /var/www/repo/archive.zip
```

And you can download it forcefully:
```php
ExportModule::forceDownload($archive);
```

Or use it in case:
```php
ExportModule::forceDownload(
	$exportModule
	->addFile($foo)
	->addFile($bar)
	->makeZip();
);
```