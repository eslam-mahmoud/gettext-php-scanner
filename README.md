GetText PHP scanner
======
Created by Eslam Mahmoud <http://eslam.me> <contact@eslam.me>

## Description

PHP class to scan files/project and create or update .po file, used for localization. Could be used to scan any type of files, It will extract all strings like __('Hello World') Or _e("Hello again.").


## Configuration
* $directory to be scanned accept array of directories or single string directory
* $file_extensions an array of allowed files extensions to be scanned