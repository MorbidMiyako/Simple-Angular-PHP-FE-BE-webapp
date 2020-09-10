<?php

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
