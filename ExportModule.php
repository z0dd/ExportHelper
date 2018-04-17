<?php
/**
* Easy export some CSV files in ZIP archive
*/
class ExportModule
{
	// Arr with added files
	private $addedFiles = [];
	private $archive = NULL;

	// Path where all stored and maked files
	private $_savePath = "/var/www/repo/";

	private $_memoryLimit = '512M';
	private $_delimeter = ";";
	private $_enclosure = '"';
	private $_chmodMode = 0755;

	/* custom settings setter */
	public function __call($name, $arguments) {
        $action = substr($name, 0, 3);
        $property = '_' . lcfirst(substr($name, 3));

        if(!property_exists($this,$property)){
			$this->error('Undefined property  ' . $name);
        }

        switch ($action) {
            case 'get':	
            	return $this->{$property}; 
            break;
            
            case 'set':	
            	$this->{$property} = $arguments[0];
            break;

            default :
            	return FALSE;
        }
    }

    public function addSpecificFile($file)
    {
    	if (!is_file($file) || !is_readable($file))
    		$this->error("File {$file} is not exists or not readable");

    	$this->addedFiles[] = [
    		'filename' 		=> basename($file),
			'path'			=> $file,
			'is_specific'	=> true,
    	];

    	return $this;
    }

    /* create file over $this->make() and add it to files list */
    public function addFile(array $data, $fileName = NULL)
    {
    	$this->addedFiles[] = $this->make($data, $fileName);
    	return $this;
    }

    /* Return all added files */
    public function getFiles()
    {
    	return $this->addedFiles;
    }

    /* Make csv from array of rows */    
	public function make(array $data, $fileName = NULL)
	{
		ini_set('memory_limit', $this->memoryLimit);

		if (is_null($fileName)) 
			$fileName = $this->generateFileName();

		if (!is_dir($this->_savePath))
			$this->error("Path '{$this->_savePath}' is not valid");

		$filePath = $this->prepeareFile($this->_savePath.$fileName);

		$file = fopen($filePath, 'a+');

		foreach ($data as $row) {
			fputcsv($file, $row, $this->_delimeter, $this->_enclosure);
		}

		fclose($file);

		return [
			'filename' 	=> $fileName,
			'path'		=> $filePath,
		];
	}

	/* Make archive from added files */
	public function makeZip($filename = NULL, $deleteAddedFilesAfter = TRUE)
	{
		if (is_null($filename)) 
			$filename = $this->generateFileName('zip');

		if (empty($this->addedFiles))
			$this->error('No added files');

		$zip = new ZipArchive();

		$filePath = $this->prepeareFile($this->_savePath.$filename);

		if($zip->open($filePath, ZipArchive::CREATE) !== TRUE) 
			$this->error("Can't create archive file");

		foreach ($this->addedFiles as $file) {
			if ($file['is_specific']) {
				if (!$zip->addFile($file['path'], $file['filename']))
					$this->error("Error while adding specific file {$file['path']} to archive {$filePath}");
			}else{
				if (!$zip->addFromString($file['filename'], file_get_contents($file['path'])))
					$this->error("Error while adding file {$file['path']} to archive {$filePath}");
			}

			if ($deleteAddedFilesAfter)
				unlink($file['path']);
		}

		if ($zip->close() !== TRUE) {
			$this->error("Error while closing archive {$filePath}");
		}

		$this->archive = $filePath;

		return $this->archive;
	}

	// Make download file forcefully. Exit after that
	public static function forceDownload($file, $deleteAfterDownload = TRUE)
	{
		if (file_exists($file)) {
		    if (ob_get_level())
		     	ob_end_clean();
		    
		    header('Content-Description: File Transfer');
		    header('Content-Type: application/octet-stream');
		    header('Content-Disposition: attachment; filename=' . basename($file));
		    header('Content-Transfer-Encoding: binary');
		    header('Expires: 0');
		    header('Cache-Control: must-revalidate');
		    header('Pragma: public');
		    header('Content-Length: '.filesize($file));
		    
		    readfile($file);

		    if ($deleteAfterDownload)
		    	@unlink($file);
		    exit;
		}
	}

	public function downloadArchive($deleteAfterDownload = TRUE)
	{
		if (is_null($this->archive))
    		$this->error("You need make zip first");

    	return self::forceDownload($this->archive, $deleteAfterDownload);
	}

	/* Generate unique filename with current date, default exctension - csv */
	private function generateFileName($extension = 'csv')
	{
		return uniqid()."_".date('Ymd').".".$extension;
	}

	/* Check is file exsists, create if it's not */
	private function prepeareFile($filePath)
	{
		if (!is_file($filePath)) {
			if (!touch($filePath))
				$this->error("Can't create file '{$filePath}'");

			if (!chmod($filePath, $this->_chmodMode))
				$this->error("Can't chmod file '{$filePath}' after create");
		}

		return $filePath;
	}

	/* Error handler. Throw errors over trigger with trace */	
	private function error($message, $level = E_USER_NOTICE, $trace = NULL)
	{
		$trace = debug_backtrace();
		trigger_error($message.' in '.$trace[0]['file'].' on line '.$trace[0]['line'], $level);
	}
}