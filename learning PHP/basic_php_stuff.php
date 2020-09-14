<?php
// installation of php on windows is a slight mess, but pretty straight forward:
/* download the zip file, put the folder in you main directory (usually C:\)
	open or create a php.ini file, take the contents of either php.ini-production or php.ini-development
	when you want to delve deeper into using their different versions for their different purposes, you can
	this isnt required yet however 

	uncomment some extensions you would like to be able to use (around line 900)

	##########################
	if you want to add other dependencies, such as mongodb, drag their dll into the ext folder
	!!!!!!!!!!!!!!!!!!!!!!
	MAKE SURE TO CHOSE THE RIGHT DLL, try different version before changing other solutions
	!!!!!!!!!!!!!!!!!!!!!!
	next, add the following line to the extensions (around line 945)

	extension=extensionName (the text between php_ and .dll, this line would work for an extension with the file name: php_extensionName.dll)

	(before, it was required to write the full file name, however this is now only supported for legacy reasons)


	for instance for mongodb
	name of the file dropped into ext: php_mongodb.dll 
	hence the added line to php.ini will be:
	extension=mongodb

	###########################

	in order to actually use extensions in your php projects, download and install composer (fairly simple using their installer)
	next run composer require extensionspecifics

	in the case of mongodb:
		$ composer require mongodb\mongodb

	this will create a folder, a composer.json and composer.lock, similar to npm when working with JavaScript

	next simply include the following line at the top of your file:
	require 'vendor/autoload.php';

	now you should be all set to go!
/*
###############################
Seeing how messy the objects can be in PHP
###############################

$array = array(
	"foo" => "bar",
	2 => "hey",
	"5" => "nah",
	"okay?",
	"6.5" => "bs",
	6.5 => "actual bs"
);

Print var_dump($array);

*/

/*
###############################
implode and explode are join and split functions
###############################

Print implode("\n ",$_SERVER);
Print "\n";
Print '$_SERVER';
Print "$_SERVER";
*/

/*
###############################
the essence of a PHP server -> echo or Print gives response -> echo is faster than Print
###############################

response(200,"Hello, World!");

function response($status,$status_message)
{
	echo $status_message;
}

*/
