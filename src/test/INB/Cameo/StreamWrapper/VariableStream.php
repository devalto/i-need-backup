<?php
/**
 * Copyright (c) 2010 ADA Consultants Inc
 * 
 * == Définition ==
 * 
 * Dans le texte suivant, "ADA" représente ADA Consultants Inc.
 * 
 * == License ==
 * 
 * L'ensemble du code source contenu dans ce fichier est propriété exclusive de 
 * ADA. Toute copie complète ou partielle et toute distribution par une tierce 
 * partie sera considéré comme un vol de propriété intellectuelle de ADA.
 * 
 * L'utilisation du logiciel est restreint aux détenteurs de permission 
 * d'utilisation donné par ADA.
 * 
 * La modification du logiciel est restreint aux détenteurs de permission de 
 * modification du code source donné par ADA.
 * 
 * À moins d'avis contraire écrit de ADA, toute modification non approuvée du 
 * code source par une tierce partie annule toutes les obligations et garanties de
 * bons fonctionnement de l'application.
 * 
 * == Nous contacter ==
 * 
 * Pour toutes questions concernant ces conditions, veuillez nous contacter en
 * utilisant les coordonnées suivantes :
 * 
 * ADA Consultants Inc
 * 432 René-Lévesque Ouest
 * Québec, QC
 * Canada, G1S 1S3
 * http://www.ada-consult.com
 * 1 (418) 907-5904
 * ada@ada-consult.com
 * 
 */

/**
 * Taken from http://ca3.php.net/manual/en/function.stream-wrapper-register.php
 *
 * @author fordiman@gmail.com
 */
class Cameo_StreamWrapper_VariableStream {

	private $position;

	private $varname;

	public function stream_open($path, $mode, $options, &$opened_path) {
		$url = parse_url($path);
		$this->varname = $url["host"];
		$this->position = 0;

		return true;
	}

	public function stream_read($count) {
		$p=&$this->position;
		$ret = substr($GLOBALS[$this->varname], $p, $count);
		$p += strlen($ret);

		return $ret;
	}

	public function stream_write($data){
		$v = &$GLOBALS[$this->varname];
		$l = strlen($data);
		$p = &$this->position;
		$v = substr($v, 0, $p) . $data . substr($v, $p += $l);
		return $l;
	}

	public function stream_tell() {
		return $this->position;
	}

	public function stream_eof() {
		return $this->position >= strlen($GLOBALS[$this->varname]);
	}
	
	public function stream_seek($offset, $whence) {
		$l = strlen(&$GLOBALS[$this->varname]);
		$p = &$this->position;
		switch ($whence) {
			case SEEK_SET: 
				$newPos = $offset;
				break;
			case SEEK_CUR: 
				$newPos = $p + $offset;
				break;
			case SEEK_END: 
				$newPos = $l + $offset;
				break;
			default:
				return false;
		}
		$ret = ($newPos >=0 && $newPos <=$l);
		if ($ret) {
			$p = $newPos;
		}
		return $ret;
	}
	
	public function stream_stat() {
		return $this->url_stat('var://' . $this->varname);
	}
	
	public function url_stat($path, $flags) {
		$url = parse_url($path);
		$varname = $url['path'];
		
		if (!isset($GLOBALS[$varname])) {
			return false;
		}
		
		return array(
			'dev' => 0,
			'ino' => 0,
			'mode' => 0,
			'nlink' => 0,
			'uid' => 0,
			'gid' => 0,
			'rdev' => 0,
			'size' => strlen($GLOBALS[$varname]),
			'atime' => time(),
			'mtime' => time(),
			'ctime' => time(),
			'blksize' => -1,
			'blocks' => -1
			
		);
	}

	public static function register() {
		stream_wrapper_register('var', __CLASS__);
	}
}