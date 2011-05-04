<?php

class INB_Database_Element {
	
	private $_id;
	
	private $_date;
	
	private $_file;
	
	private $_comment = "";
	
	private $_metadata = array();
	
	public function __construct($id, $file, $date = null) {
		$this->_id = $id;
		$this->setFile($file);
		$this->setDate($date);
	}
	
	public function getId() {
		return $this->_id;
	}
	
	public function getDate() {
		return $this->_date;
	}
	
	public function setDate($date) {
		if (is_numeric($date)) {
			$date = date(DATE_ATOM, $date);
		}
		
		$this->_date = $date;
		
		return $this;
	}
	
	public function setDateNow() {
		return $this->setDate(time());
	}
	
	public function getFile() {
		return $this->_file;
	}
	
	public function setFile($file) {
		$this->_file = $file;
		
		return $this;
	}
	
	public function getComment() {
		return $this->_comment;
	}
	
	public function setComment($comment) {
		$this->_comment = $comment;
		
		return $this;
	}
	
	public function getMetadata() {
		return $this->_metadata;
	}
	
	public function setMetadata($metadata, $value = null) {
		if (is_array($metadata)) {
			$this->_metadata = $metadata;
		} elseif (!is_null($metadata)) {
			$this->_metadata[$metadata] = $value;
		} else {
			throw new OutOfBoundsException("La clef \$metadata est invalide.");
		}
		
		return $this;
	}
	
	public function toArray() {
		$ret = array(
			'id'		=> $this->getId(),
			'date'		=> $this->getDate(),
			'file'		=> $this->getFile()
		);
		
		if ($this->_comment) {
			$ret['comment'] = $this->_comment;
		}
		
		if (!empty($this->_metadata)) {
			$ret['metadata'] = $this->_metadata;
		}
		
		return $ret;
	}
	
	public static function createFromArray(array $array) {
		$element = new INB_Database_Element($array['id'], $array['file'], $array['date']);
		
		if (isset($array['comment'])) {
			$element->setComment($array['comment']);
		}
		
		if (isset($array['metadata'])) {
			$element->setMetadata($array['metadata']);
		}
		
		return $element;
	}
	
}