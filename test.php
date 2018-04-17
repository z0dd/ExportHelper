<?php 
require "ExportModule.php";
$data = [
	['foo', 'bar', 'baz', 'qwe', 'asd', 'zxc'], //row
	['foo', 'bar', 'baz', 'qwe', 'asd', 'zxc'], //another one
	['foo', 'bar', 'baz', 'qwe', 'asd', 'zxc'], //...
];

$exportModule = new ExportModule;

// Simple use
$exportModule
	->addFile($data)
	->makeZip('archive.zip');

// Adding many files
$exportModule
	->addFile($fooData)
	->addFile($barData)
	->addFile($bazData)
	->makeZip();

// Using settings savePath, delimeter, chmodMode and more.
$exportModule->setSavePath('/var/www/repo');
$exportModule->getSavePath();

// Force download your any file 
ExportModule::forceDownload($anyFilePath);
