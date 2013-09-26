<?php
/******************************************************************
* Projectname:   PHP JSON-P Class 
* Version:       1.0
* Author:        Radovan Janjic <rade@it-radionica.com>
* Last modified: 10 05 2013
* Copyright (C): 2013 IT-radionica.com, All Rights Reserved
*
* GNU General Public License (Version 2, June 1991)
*
* This program is free software; you can redistribute
* it and/or modify it under the terms of the GNU
* General Public License as published by the Free
* Software Foundation; either version 2 of the License,
* or (at your option) any later version.
*
* This program is distributed in the hope that it will
* be useful, but WITHOUT ANY WARRANTY; without even the
* implied warranty of MERCHANTABILITY or FITNESS FOR A
* PARTICULAR PURPOSE. See the GNU General Public License
* for more details.
* 
* Description:
* 
* JSONP or "JSON with padding" is a communication technique 
* used in JavaScript programs which run in Web browsers. 
* It provides a method to request data from a server in a different domain, 
* something prohibited by typical web browsers because of the same origin policy.
* Read more on: 
* http://en.wikipedia.org/wiki/JSONP
*
* Example:
*
* $JSONP = new JSONP;
* $JSONP->encode(array('asdf', 'asfd'));
*
******************************************************************/

class JSONP {

	/** Javascript callback function name
	 * @var string
	 */
	var $paramCallback = 'callback';
	
	/** Javascript assinged variable name
	 * @var string
	 */
	var $paramAssign = 'assing';
	
	/** Javascript variable name REGEX
	 * @var string
	 */
	var $jsVarName = '/^[\p{L}\p{Nl}$_][\p{L}\p{Nl}$\p{Mn}\p{Mc}\p{Nd}\p{Pc}]*$/i';
	
	/** Javascript function name REGEX
	 * @var string
	 */
	var $jsFuncName = '/^[$A-Z_][0-9A-Z_$]*$/i';

	/** JSON-P encode and print with headers
	 * @param mixed $data
	 * @param boolean $print
	 * @param boolean $header
	 * @return JSON-P formated string
	 */
	function encode($data, $print = TRUE, $header = TRUE) {
		// Define json_encode for PHP < 5.2
		if (!function_exists('json_encode')) {
			function json_encode($data) {
				switch ($type = gettype($data)) {
					case 'NULL': 
						return 'null';
					case 'boolean': 
						return ($data ? 'true' : 'false');
					case 'integer':
					case 'double':
					case 'float': 
						return $data;
					case 'string': 
						return '"' . addslashes($data) . '"';
					case 'object': 
						return json_encode(get_object_vars($data));
					case 'array':
						$output_index_count = 0;
						$output_indexed = $output_associative = array();
						foreach ($data as $key => $value) {
							$output_indexed[] = json_encode($value);
							$output_associative[] = json_encode($key) . ':' . json_encode($value);
							if ($output_index_count !== NULL && $output_index_count++ !== $key) {
								$output_index_count = NULL;
							}
						}
						return ($output_index_count !== NULL) ? '[' . implode(',', $output_indexed) . ']' : '{' . implode(',', $output_associative) . '}';
					default:
						return NULL;
				}
			}
		}
		
		$return = NULL;
		$jsonp = TRUE;
		if(isset($_GET[$this->paramCallback]) && @preg_match($this->jsFuncName, $_GET[$this->paramCallback], $callback)) {
			$return = $callback[0]. '(' . json_encode($data) . ');';
		} else {
			if (isset($_GET[$this->paramAssign]) && @preg_match($this->jsVarName, $_GET[$this->paramAssign], $assign)) {
				$return = 'var ' . $assign[0] . ' = ' . json_encode($data) . ';';
			} else {
				$jsonp = FALSE;
				$return = json_encode($data);
			}
		}
		
		if ($print) {
			if ($header) {
				header('Cache-Control: no-cache, must-revalidate');
				header('Content-Type: application/' . ($jsonp ? 'javascript' : 'json'));
			}
			echo $return;
		} else {
			return $return;
		}
	}
}