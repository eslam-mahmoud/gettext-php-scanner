<?php

/*
 * if directory exist
 * scan directory
 * for each result if directory scan it
 * if file get it
 * for each line parse it
 * if pache regex get text add it to array
 * creat file with the result mach .pot files
 */

/*
 * TODO
 * let patters start with many functions like __ & _e
 * enhance pattern
 * accept array of directories
 */
class poedit {

    //Scan the curnt directory.
    var $directory = './';
    //var $pattern = '/__\(\'([a-zA-Z0-9\s\'\"\_\,\`\?\!\.\-]*)\'\)/';
    //Pattern to match __('any text')
    var $pattern = '/__\(\'[a-zA-Z0-9\s\'\"\_\,\`\?\!\.\-]*\'\)/';

    function scan_dir($directory, $pattern) {
	$lines = array();

	if ($handle = opendir($directory)) {
	    while (false !== ($file = readdir($handle))) {
		if ($file != "." && $file != "..") {
		    //echo "<br><br>" . $file . "<br>";
		    $file = $directory . $file;
		    if (is_dir($file)) {
			$sub_lines = $this->scan_dir($file . '/', $pattern);
			$lines = array_merge($lines, $sub_lines);
		    } else {
			$fh = fopen($file, 'r') or die($php_errormsg);
			$i = 1;
			while (!feof($fh)) {
			    // read each line and trim off leading/trailing whitespace
			    if ($s = trim(fgets($fh, 16384))) {
				// match the line to the pattern
				if (preg_match($pattern, $s, $matches)) {
				    foreach ($matches as $k => $v) {
					//lines cleaning
					$v = str_replace(array('__(\'', '\')'), '', $v);
					if (!in_array($v, $lines)) {
					    $lines[] = $v;
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
		    }
		}
	    }
	    closedir($handle);
	}

	return $lines;
    }

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

}

/*
 * Example of how to use this class
 * 
 */
$poedit = new poedit();
$lines = $poedit->scan_dir($poedit->directory, $poedit->pattern);
echo count($lines) . ' lines have been collected and need to be translated <br>';
if($poedit->creat_po($lines))
    echo '"lang.po" file has been created in the same directory of this script fined it at <a href="lang.po">download lang.po</a>';
else
    echo 'Error could not create the file please check if you have the right permetions';
?>