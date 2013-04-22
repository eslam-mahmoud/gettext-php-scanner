<?php

/*
 * Developer: Eslam Mahmoud contact@eslam.me
 * URL: http://eslam.me
 * Discription: Class to scan directory or file and extract all strings need to be translated.
 */

class poedit {

    //Default scan the curnt directory, accept string as directory path or array or directories
    //Directory path mast end with '/'
    var $directory = './';
    //Pattern to match
    //(__('pattern should get me :)'),'pattern should not get me !!') and if there another __('text need translation') in the same line it will be there 
    private $pattern = '/(__|_e)\((\'|\")(.+?)(\'|\")\)/';
    //Files extensions to scan, accept Array()
    var $file_extensions = false;

    //Scan the directory and sub directories
    //Try to match every line with the pattern
    function scan_dir($directory) {
	$lines = array();

	if (is_array($directory)) {
	    foreach ($directory as $k => $dir) {
		$sub_lines = $this->scan_dir($dir);
		$lines = array_merge($lines, $sub_lines);
	    }

	    return $lines;
	}

	if (!is_dir($directory))
	    return false;

	if ($handle = opendir($directory)) {
	    //Get every file or sub directory in the defined directory
	    while (false !== ($file = readdir($handle))) {
		if ($file == "." || $file == "..")
		    continue;

		//echo "<br><br>" . $file . "<br>";
		$file = $directory . $file;
		//If sub directory call this function recursively
		if (is_dir($file)) {
		    $sub_lines = $this->scan_dir($file . '/');
		    $lines = array_merge($lines, $sub_lines);
		} else {
		    $file_lines = $this->parse_file($file);

		    if ($file_lines)
			$lines = array_merge($lines, $file_lines);
		}
	    }
	    closedir($handle);
	}

	return $lines;
    }

    //Creat the .po file named lang.po
    function creat_po($lines = array()) {
	if (count($lines) < 1)
	    return false;

	touch('./lang.po');
	$file = fopen('./lang.po', 'w+') or die('could not open file');
	foreach ($lines as $k => $line) {
	    fwrite($file, 'msgid "' . $line . '"' . "\n" . 'msgstr ""' . "\n\n");
	}
	fclose($file);

	return true;
    }

    //parse file to get lines
    function parse_file($file = false) {
	if (!$file || !is_file($file))
	    return false;

	//check the file extension, if there and not the same as file extension skip the file
	if ($this->file_extensions && is_array($this->file_extensions)) {
	    $pathinfo = pathinfo($file);
	    if (!in_array($pathinfo['extension'], $this->file_extensions))
		return false;
	}

	$lines = array();
	//Open the file
	$fh = fopen($file, 'r') or die($php_errormsg);
	$i = 1;
	while (!feof($fh)) {
	    // read each line and trim off leading/trailing whitespace
	    if ($s = trim(fgets($fh, 16384))) {
		// match the line to the pattern
		
		if (preg_match_all($this->pattern, $s, $matches)) {
		    //$matches[0] -> full pattern
		    //$matches[1] -> method __ OR _e
		    //$matches[2] -> ' OR "
		    //$matches[3] -> array ('text1', 'text2')
		    //$matches[4] -> ' OR "
		    if (!isset($matches[3]))
			continue;

		    foreach($matches[3] as $k=>$text){
			if (!in_array($text, $lines)) {
			    $lines[] = $text;
			}
		    }
		} else {
		    // complain if the line didn't match the pattern 
		    error_log("Can't parse $file line $i: $s");
		}
	    }
	    $i++;
	}
	fclose($fh) or die($php_errormsg);

	return $lines;
    }

}

/*
 * Example of how to use this class
 */
$poedit = new poedit();
$lines = $poedit->scan_dir($poedit->directory);
echo count($lines) . ' lines have been collected and need to be translated <br>';
if ($poedit->creat_po($lines))
    echo '"lang.po" file has been created in the same directory of this script find it at <a href="lang.po">download lang.po</a>';
else
    echo 'Error could not create the file please check if you have the right permissions';
?>