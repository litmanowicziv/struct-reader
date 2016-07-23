<?php

set_include_path("./../". PATH_SEPARATOR . ini_get("include_path"));

require_once 'vendor/autoload.php';
require_once 'src/Feed.php';

// TODO : Using fgetCSV() : http://php.net/manual/en/function.fgetcsv.php
class CSVFeed extends Feed {
	private $_header;
	private $_delimiter;

	function __construct($file, $delimiter) {
		parent::__construct();
		
		if (($this->_file_handler = fopen($file, "r")) == false)
			throw new Exception("Error: Invalid file");

		if (isset($delimiter))
			$this->_delimiter = $delimiter;
		else throw new Exception("Error: Not defined delimiter");
		
		if (($this->_header = fgetcsv($this->_file_handler, 0, $this->_delimiter)) == false)
			throw new Exception("Reading of Header was failed");
		
		if (isDebugEnv() & ENV_DEBUG_PRINTED)
			new dBug($this->_file_handler);
	}

	function __destruct() {
		fclose($this->_file_handler);
	}

	function valid() {
		if (parent::valid())
			return !feof($this->_file_handler);
	}

	function next() {
		if (($this->_entity = fgetcsv($this->_file_handler, 0, $this->_delimiter)) == false)
			throw new Exception("Error: product failed on read");

		// Handling blamk row
		if (empty(implode($this->_entity)))
			throw new Exception("Error: Record is empty");

		$this->_entity = array_combine($this->_header, $this->_entity);
		parent::next();
	}

	function rewind() {
		parent::rewind();
		rewind($this->_file_handler);

		if (($this->_header = fgetcsv($this->_file_handler, 0, $this->_delimiter)) == false)
			throw new Exception("Reading of Header was failed");
	}
	
}
