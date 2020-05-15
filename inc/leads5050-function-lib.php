<?php
/**
 * Description: Various functions used by our Plugins
 * Author: Clinton [Leads5050]
 * License: GPLv3
 * Copyright 2020 CreatorSEO (email : info@creatorseo.com)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 3, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You can find a copy of the GNU General Public License at the link
 * http://www.gnu.org/licenses/gpl.html or write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 */


/**
 * Create a dynamic option list based on an option name, an array of elements and a default element
 * @param array $elements elements to display in the drop-down
 * @param string $name name of the option field
 * @param string $selected default or selected option
 * @param string $class optional class name for the option field
 * @param string $display - echo or return the html
 * @return string - return sting if $display is false
 */
function leads5050_dynamic_options($elements,$name,$selected,$class='',$display=true){
	$out = '';
	if (strlen($name)){
		if (is_array($elements)){
			$out .= '<select name="'.$name.'"';
			$out .= strlen($class)? ' class="'.$class.'"': '';
			$out .= '>';
			$out .= ($selected=='')? '<option selected="selected">-- Select --</option>': '';
			foreach ($elements as $emt){
				$chk=($emt==$selected)? 'selected="selected"': '';
				$out .= '<option value="'.$emt.'" '.$chk.'>'.$emt.'</option>';
			}
			$out .= '</select>';
		} else {
			$out = '<p>ERROR: Elements not defined for list</p>';
		}
	} else {
		$out .= 'ERROR: Control name not specified';
	}
	if ($display) echo $out;
	else return $out;
}

/**
 * Recursive sanitation of an array
 *
 * @param $array
 *
 * @return mixed
 */
function leads5050_sanitize_array($array) {
	foreach ( $array as $key => &$arr ) {
		if ( is_array( $arr ) ) {
			$arr = leads5050_sanitize_array($arr);
		}
		else {
			$arr = sanitize_text_field( $arr );
		}
	}
	return $array;
}

/**
 * Create a dynamic option list based on an option name, an array of elements and a default element
 * this differs from leads5050_dynamic_options in that the array provided has elements as the index and
 * values in a named column
 * @param array $elements elements to display in the drop-down
 * @param string $name name of the option field
 * @param string $selected default or selected option
 * @param string $column is the name of the column to select in $arr
 * @param string $class optional class name for the option field
 * @param string $display - echo or return the html
 * @return string - return sting if $display is false
 */
function leads5050_dynamic_options_att($elements,$name,$selected,$column,$class='',$display=true){
	$out = '';
	if (strlen($name) && strlen($column)){
		if (is_array($elements)){
			$out .= '<select name="'.$name.'"';
			$out .= strlen($class)? ' class="'.$class.'"': '';
			$out .= '>';
			$out .= ($selected=='')? '<option selected="selected">-- Select --</option>': '';
			foreach ($elements as $emt=>$arr){
				if (isset($arr[$column])){
					$chk=($emt==$selected)? 'selected="selected"': '';
					$out .= '<option value="'.$emt.'" '.$chk.'>'.$arr[$column].'</option>';
				}
			}
			$out .= '</select>';
		} else {
			$out = '<p>ERROR: Elements not defined for list</p>';
		}
	} else {
		$out .= 'ERROR: Control name not specified';
	}
	if ($display) echo $out;
	else return $out;
}

/**
 * Create a input field based on the type provided
 * @param string $type input field type (allowed types are 'text','number','date','datetime')
 * @param string $name name of the input field
 * @param string $value default value
 * @param string $min minimum value
 * @param string $maxn maximum value
 * @param string $class optional class name for the option field
 * @param string $display - echo or return the html
 * @return string - return sting if $display is false
 */
function leads5050_input_field($type, $name, $value, $min=0, $max=0, $class='', $display=true){
	$out = '';
	if (strlen($name)){
		$type = in_array($type,array('text','number','date','datetime'))? $type: 'text';
		$out ='<input type="'.$type.'" name="'.$name.'" value="'.$value.'" ';
		$out .= strlen($class)? ' class="'.$class.'"': '';
		if ($min>0) {
			if ($type=='text'){
				$out .= '';
			} elseif ($type=='number') {
				$out .= ' min="'.$min.'"';
			}
		}
		if ($max>0) {
			if ($type=='text'){
				//$out .= ' maxlength="'.$max.'"';
				$out .= ' size="'.$max.'"';
			} elseif ($type=='number') {
				$out .= ' max="'.$max.'"';
			}
		}
		$out .= ' />';
	} else {
		$out .= 'ERROR: Control name not specified';
	}
	if ($display) echo $out;
	else return $out;
}

/**
 * Recursive function for renaming array keys anywhere in a multi-dimensional array
 *
 * @param array $arr - multidimensional array where changes are to be made
 * @param array $replace of the form array(oldkey => newkey)
 *
 * @return array - array after replacement
 */
function leads5050_replace_array_keys( array $arr, array $replace ) {
	$return = array();
	foreach ( $arr as $key => $value ) {
		foreach ( $replace as $oldKey => $newKey ) {
			if ( $key === $oldKey ) {
				$key = $newKey;
			}
		}
		if ( is_array( $value ) ) {
			$value = leads5050_replace_array_keys( $value, $replace );
		}
		$return[ $key ] = $value;
	}
	return $return;
}

if (!function_exists( 'leads5050_sort_array_by_column' )) {
/**
 * Sort a multi-dimensional array by column name. Keys are preserved.
 * @param $arr - array to sort
 * @param $col - name of the column to sort
 * @param int $dir direction of the sort (use SORT_ASC or SORT_DESC)
 */
function leads5050_sort_array_by_column(&$arr, $col, $dir = SORT_ASC) {
	$sort_col = array();
	foreach ($arr as $key=> $row) {
		$sort_col[$key] = $row[$col];
	}
	array_multisort($sort_col, $dir, $arr);
}
}

/**
 * Send a debug message to the console (logto = 1) otherwise log to a file called debug_file.txt in the root
 * This can be viewed by switching on the developer tools console in the browser.
 * @param Label on the debug console $label
 * @param object to display $object
 * @param priority number for indent $priority
 * @param logto number $logto (0=php log file, 1=console)
 */
function leads5050_debug_log( $label = null, $object = null, $priority = 1, $logto = 0 ) {
	$priority = $priority < 1 ? 1 : $priority;
	$logto    = $logto > 0 ? true : false;
	$message  = json_encode( $object, JSON_PRETTY_PRINT );
	$stamp    = date( 'Y-m-d H:i:s' );
	$label    = "[" . $stamp . "] " . ( $label ? ( " " . $label . ": " ) : ': ' );
	if ( $logto ) {
		echo "<script>console.log('" . str_repeat( "-", $priority - 1 ) . $label . "', " . $message . ");</script>";
	} else {
		//error_log($label."".$message);
		error_log( $label . "" . $message . "\r\n", 3, ABSPATH . "debug_file.txt" );
	}
}

/**
 * Save a debug or log result to the option table against record leads5050_log.
 * Records may be created, deleted, replaced or appended.
 * @param string attribute attribute name
 * @param string $value the value / results to be written to the log file
 * @param string $action action to perform to the log file (update, delete, replace)
 * @param boolean $use_time time log format true - include a date as Y-m-d H:i:s (default) false - use micro-time stamp
 * @param integer $max_elements maximum number of elements in the attribute array
 */
function leads5050_update_log_file( $attribute, $value, $action = 'append', $use_date=true, $max_elements=200){
	$option_name='leads5050_log'; //name of the option to use
	$stamp = $use_date? date('Y-m-d H:i:s'): 'M'.(microtime(true)*1000);
	$attribute = strtoupper($attribute);
	$value = json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
	$action = strtolower($action);
	$max_elements = $max_elements>0 && $max_elements<250? $max_elements: 200;
	if (strlen($attribute) && ($action == 'delete' || strlen($value))) {
		if ( ( $data = get_option( $option_name ) ) !== false ) {
			// The option already exists, so we just update it.
			if ($action == 'delete'){
				if (isset($data[$attribute])){
					unset($data[ $attribute ]);
				}
			} else {
				if (isset($data[$attribute])){
					//attribute exists
					if ($action == 'replace') {
						$data[ $attribute ] = array( $stamp => $value );
					} else {
						//$action == 'append' is the catch-all
						if (count($data[$attribute])>$max_elements){
							$start = count($data[$attribute])-$max_elements;
							$data[$attribute] = array_slice($data[$attribute], $start);
							//array_shift($data[$attribute]);
						}
						$data[$attribute][$stamp] = $value;
					}
				} else {
					//attribute does not exist, add it
					$data[$attribute] = array( $stamp => $value );
				}
			}
			update_option( $option_name, $data );
		} else {
			// The option hasn't been added yet. We'll add it with $autoload set to 'no'.
			$data = array(
				$attribute => array( $stamp => $value )
			);
			add_option( $option_name, $data);
		}
	}
}

/**
 * Encrypt or decrypt a string the easy way
 *
 * @param string $input string to encrypt or decrypt
 * @param boolean $encrypt -  true to encrypt and false to decrypt
 * @return string - the encrypted or decrypted value based on the $encrypt setting
 */
function leads5050_easy_crypt($input, $encrypt=false){
	$trans = array("a"=>"N","b"=>"M","c"=>"L","d"=>"K","e"=>"J","f"=>"I","g"=>"H","h"=>"G","i"=>"F","j"=>"E","k"=>"D","l"=>"C","m"=>"B","n"=>"A",
	               "A"=>"n","B"=>"m","C"=>"l","D"=>"k","E"=>"j","F"=>"i","G"=>"h","H"=>"g","I"=>"f","J"=>"e","K"=>"d","L"=>"c","M"=>"b","N"=>"a");
	if (strlen($input)){
		if ($encrypt){
			$result = strtr(base64_encode($input), $trans);
		} else {
			//decrypt
			$result = base64_decode(strtr($input, array_flip($trans)));
		}
	} else {
		$result = false;
	}
	return $result;
}

/**
 * Create a table based on a headers array and an array of corresponding fields
 * Note: If a record field is labelled 'summary' then this will be a fullwidth row
 *
 * @param array $headers - format [field=>label]
 * @param array $records - format [index => [field=>label]]
 * @param string $class - include a class if not blank
 * @param bool $escape escape the text (default) or allow tags
 *
 * @return string - html to create the table
 */
function leads5050_easy_table($headers, $records, $class='', $escape = true){
	$out = ''; $num = 0;
	if (is_array($headers) && count($headers)){
		$keys = array_keys($headers);
		$out .= '<table ' . (strlen($class)? ('class="'.$class.'" '):'') . '>';
		$out .= '<tbody>';
		$out .= '<tr>';
		foreach ( $headers as $k => $header ) {
			if (strtolower($k) != 'summary'){
				$out .= '<th>' . $header . '</th>';
				$num++;
			}
		}
		$out .= '</tr>';
		if (is_array($records) && count($records)){
			foreach ( $records as $j => $record ) {
				$summary = '';
				$out .= '<tr>';
				foreach ( $keys as $key ) {
					if ($key != 'summary'){
						if ($escape){
							$out .= '<td>' . ((isset($record[$key]) && strlen($record[$key]))? esc_html($record[$key]): '-') . '</td>';
						} else {
							$out .= '<td>' . ((isset($record[$key]) && strlen($record[$key]))? $record[$key]: '-') . '</td>';
						}
					} else {
						$summary = substr($record[$key],0,256);
					}
				}
				$out .= '</tr>';
				if (strlen($summary)){
					$out .= '<tr><td colspan = "'.$num.'">'.$summary.'</td></tr>';
				}
			}
		}
		$out .= '</tbody>';
		$out .= '</table>';
	} else {
		$out .= '<p>Table not correctly specified</p>';
	}
	return $out;
}
