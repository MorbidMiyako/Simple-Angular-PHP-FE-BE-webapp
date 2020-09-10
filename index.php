<?php 

/*
########################################
outline basic api
########################################

create basic api

get and post functionality

get:

    simply respond Hello World

post:

    respond text send in body 
    format: 

        {
            text: text_to_respond
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

//lets store the request method
$request_method = $_SERVER['REQUEST_METHOD'];
echo "request method";
echo "\n";
echo $request_method;
echo "\n";

response($request_method);

function response($request_method) {
    switch($request_method) {
        case 'GET': 
            echo "GET";
            echo "\n";
            // break statements are essential for switches, good reminder haha
            // this seems to favour the choice of chaining cases witht he same output over saving the hassle of writing break statements.
            break;
        case 'POST':
            echo "POST";
            echo "\n";
            break;
        default:
            echo "request_method unsupported";
            echo "\n";
            break;

    }
}
