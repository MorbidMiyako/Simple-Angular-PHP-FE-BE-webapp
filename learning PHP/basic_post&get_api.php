<?php 

# original filename basic_post&get_api.php 

/*

########################################
running a server in php:
########################################

its incredibly simple:

1) server file has to be called index.php and exist in your root project fold
2) run the following line in your terminal of choice:
    php -S localhost:5000

    (you can choose the link yourself, along with the port, but local host tends to be pretty standard)

if you want to run this server, either drag it up, or dive down and rename the file to index.php

########################################
outline basic api
########################################

create basic api

get and post functionality

get:

    GET to localhost:5000/?name=funny
    returns funny

post:

    respond text send in body 
    format: 

        {
            "text": "some very intriguing text that surely not only you will be reading"
        }
*/

/*
########################################
outline plan to build api
########################################

register which request method is used

implement switch statement to react accordingly to the request method

create response for GET and POST request method

*/

/*
#########################################
accessing headers and body
#########################################

$_GET returns what is requested trough the URL
$_POST returns what is in the object 
correction, it accesses form-data
if one wants to access the body, file_get_contents('php://input')

*/

//lets store the request method
$request_method = $_SERVER['REQUEST_METHOD'];
echo "request method";
echo "\n";
echo $request_method;
echo "\n";
##############
# $_REQUEST contains get, post and cookies, dropped it to see how it was structured, doesnt allow to see what information belongs to get or post aside from get information being before post information.
// var_dump($_REQUEST);
// echo "\n";

response($request_method);

function response($request_method) {
    switch($request_method) {
        case 'GET': 
            echo "getting" , $_GET['name'];
            echo "\n";

            // break statements are essential for switches, good reminder haha.
            // this seems to allow the choice of chaining cases witht he same output, saving the hassle of writing break statements wouldn't prevent this from being possible.
            break;
        case 'POST':
            // access and store the file that gets streamed
            $body = file_get_contents('php://input');
            // decode json, set second parameter to true.
            // this returns associative array instead of an object, there doesnt seem to be much of a difference between the two aside from OOP applications.
            // personally more familiar with the ['key'] method from js and py, hence the preference.
            $body = json_decode($body, true);
            echo "you send the following text:\n";
            echo $body['text'];
            echo "\n";
            break;
        default:
            echo "request_method unsupported";
            echo "\n";
            break;

    }
}
