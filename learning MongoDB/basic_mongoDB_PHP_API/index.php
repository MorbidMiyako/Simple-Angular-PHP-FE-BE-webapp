<?php

require '../../vendor/autoload.php';

################################################
// step 3: hook database up to api -> test using simple get
// step 4: setup api to allow crud operations
// step 5: hook up api crud operations to allow api to make changes
################################################

// creating a class to easily call functions and later easily move the class into its own file
// see db_helpers

// create object with the functions etc
$CRUD_operations = require_once('db_helpers.inc.php');

// set variables
$CRUD_operations->setVariables();

// set input variables
$request_method = $_SERVER['REQUEST_METHOD'];
// explode splits into array, we just want the stuff before the first ?
$request_uri = explode("?", $_SERVER['REQUEST_URI'])[0];
// prefer getting the object for consistency
$body = json_decode(file_get_contents('php://input'));

// call server function
server($request_method, $request_uri, $body ,$CRUD_operations);

// server function
function server($request_method, $request_uri, $body, $CRUD_operations) {
    
    // first check which uri was used
    switch($request_uri) {

        // name to get user by name
        // want to change this to user, but dont want to change postman links lel
        case '/name':

            // next check method
            switch($request_method) {

                // get, just return the user
                case 'GET': 

                    // checks if the desired variable is there
                    if (isset($_GET['name'])){
                        echo "getting ", $_GET['name'], "\n";

                        // preferalby one would like to check if NULL is returned, to display custom message
                        $document = $CRUD_operations->get_one_user_by_name($_GET['name']);

                        if($document == NULL) {
                            echo "user not found";
                            break;
                        }
                        // from PHP variable to BSON to JSON
                        echo MongoDB\BSON\toJSON(MongoDB\BSON\fromPHP($document));
                        
                    } 

                    // return warning
                    else {
                        echo "please use ?name=desiredname to specify the desired user \n";
                    }
        
                    break;

                // make changes to a user
                case 'PATCH':

                    if (isset($body->user) && isset($body->new_values)) {
                        $user = $body->user;
                        $id = $user->id;
                        $new_values = $body->new_values;

                        // check user object
                        foreach(array_keys((array)$user) as $key) {
                            if (!($key == 'id')) {
                                // exit just stops the script and outputs a string, in these cases its just: STOP
                                // too lazy to put them everywhere, just where if statements should break
                                exit("only name, bio and username can be changed");
                            }
                        }

                        // check new values object
                        // this is why I prefer ts, and I'm hyped for Deno.js
                        foreach(array_keys((array)$new_values) as $key) {
                            if (!($key == 'name' || $key == 'bio' || $key == 'username')) {
                                // exit just stops the script and outputs a string, in these cases its just: STOP
                                exit("only name, bio and username can be changed");
                            }
                            if (gettype($new_values->$key) != 'string') {
                                
                                // exit just stops the script and outputs a string, in these cases its just: STOP
                                exit("only a string can be set as the new value");
                            }
                        }

                        $document = $CRUD_operations->change_user($id, $new_values);
                        var_dump($document);
                    }
                    else{
                        echo "put requires the following body: \n
                        'user' : {\n
                            'id' : 'id' \n
                            } \n
                            'new_values: {\n 
                            'variable_to_change : 'new_value' \n
                            }\n
                        }\n

                        variables that can be changed: \n
                            'name', 'bio' and 'username' \n
                        ";
                    }

                    break;

                // delete a user
                case 'DELETE':

                    if (isset($body->id)) {
                        $id = $body->id;
                        $document = $CRUD_operations->delete_user($id);
                        var_dump($document);
                    }
                    else {
                        echo "delete takes in an id: \n
                            'id' : 'id' \n
                            ";
                    }

                    break;

                // default
                default:
                    echo 'only GET, PUT and DELETE are available';


            }
            break;
        
        // add a new user
        case '/new_user':

            // got annoyed at having to put this in multiple places
            $post_error = "only POST is available, in the form: \n
                'new_user' : {\n
                    'name' : 'name', \n
                    'bio' : 'bio description', \n
                    'username' : 'username' \n
                }\n";
        
            // next check method
            switch($request_method) {

                // create a new user
                case 'POST':

                    if (isset($body->new_user)) {
                        $new_user = $body->new_user;
                        if (isset($new_user->name) && isset($new_user->bio) && isset($new_user->username)) {

                            $document = $CRUD_operations->add_one_user($new_user);
                            var_dump($document);

                        }
                        else {
                            echo $post_error;
                        }
                    }
                    else {
                        echo $post_error;
                    }

                    break;

                default:
                    echo $post_error;
            }
            break;

        // add new users, naming to prevent accidentally mixing the two up
        case '/add_users':

            // got annoyed at having to put this in multiple places
            $post_error = "only POST is available, in the form: \n
                'new_user' : [{\n
                    'name' : 'name', \n
                    'bio' : 'bio description', \n
                    'username' : 'username' \n
                    },{ \n
                    'name' : 'name', \n
                    'bio' : 'bio description', \n
                    'username' : 'username' \n
                }] \n";

            // next check method
            switch($request_method) {

                // create a new user
                case 'POST':

                    if (isset($body->new_users)) {
                        $new_users = $body->new_users;
                        foreach($new_users as $new_user) {
                            // only break if condition isn't met
                            if (!isset($new_user->name) && !isset($new_user->bio) && !isset($new_user->username)) {
                                exit($post_error);
                            }
                        }
                        $document = $CRUD_operations->add_users($new_users);
                        var_dump($document);
                    }
                    else {
                        echo $post_error;
                    }

                    break;

                default:
                    echo $post_error;
            }
            break;

        // allname to get all users by name
        case '/allname':

            // next check method
            switch($request_method) {

                // get, just return the user
                case 'GET': 

                    // checks if the desired variable is there
                    if (isset($_GET['name'])){
                        echo "getting ", $_GET['name'], "\n";

                        // preferalby one would like to check if NULL is returned, to display custom message
                        $document = $CRUD_operations->get_all_users_by_name($_GET['name']);

                        if($document == NULL) {
                            echo "user not found";
                            break;
                        }

                        echo MongoDB\BSON\toJSON(MongoDB\BSON\fromPHP($document));
                        
                    } 

                    // return warning
                    else {
                        echo "please use ?name=desiredname to specify the desired user \n";
                    }
        
                    break;
                
                default:
                    echo "only GET is available";


            }
            break;


        // all to get all users
        case '/all':

            // next check method
            switch($request_method) {

                // get, just return the user
                case 'GET': 

                    $document = $CRUD_operations->get_all_users();
                    echo MongoDB\BSON\toJSON(MongoDB\BSON\fromPHP($document));
        
                    break;

                default:
                    echo "only get is available";

            }
            break;

        // reset to reset the database
        case '/reset':

            switch($request_method) {

                // no special checks, yet, credentials might be an option later
                case 'GET':
                    $CRUD_operations->reset_users_collection($CRUD_operations->getInitialUsers());
                    // echo `reset the database and inserted the following users `;
                    // var_dump($CRUD_operations->getInitialUsers());
                    break;

                default:
                    echo "/reset only has a GET option, this resets the database \n";
                    break;
            }
            break;

        // something to work on later, just a thought
        case '/help':
        // this way both help and default will return this message
        default:
            echo "either /name, /allname, /all, /new_user, /add_users and /reset are available \n";
            break;

    };
};

// step 6: move db class to it's own file
// step 7: move on to learning and building an Angular FE

?>
