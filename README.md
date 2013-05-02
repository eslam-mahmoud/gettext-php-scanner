GetText PHP scanner
======
Created by Eslam Mahmoud <http://eslam.me> <contact@eslam.me>

## Description

PHP class to scan files/project and create or update .po file, used for localization. Could be used to scan any type of files, It will extract all strings like __('Hello World') Or _e("Hello again.").


## Configuration
* $directory to be scanned accept array of directories or single string directory
* $file_extensions an array of allowed files extensions to be scanned


## How to use it?
```php
    //Example of how to use this class
    require_once './gettext.php';
    $gettext = new gettext();
    $lines = $gettext->scan_dir();
    echo count($lines) . ' lines have been collected and need to be translated <br>';
    
    if ($gettext->create_po($lines))
        echo '"' . $gettext->file_name . '" file has been created in the same directory of this script find it at <a href="' . $gettext->file_name . '">download ' . $gettext->file_name . '</a>';
    else
        echo 'Error could not create the file please check if you have the right permissions';
```
