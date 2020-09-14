<?php

require 'vendor/autoload.php';

################################################
// step 3: hook database up to api -> test using simple get
################################################

// creating a class to easily call functions and later easily move the class into its own file
class CRUD_operations {

    // creating the client and making a connection
    public $client;

    public function setClient() {
        $this->client = new MongoDB\Client(
        // yes, i am aware that having a generic password and that having it exposed is bad practice
        // due to the database being for testing purposes, and access being limited to IP, I feel this is okay for the time being
        // in a real world scenario, one would use ENV variables, and access them using $_ENV, allowing such variables to be hidden
        'mongodb+srv://admin:admin@mytestingden.foc2e.mongodb.net/?retryWrites=true&w=majority');
    }

    public function getClient() {
        return $this->client;
    }
    
    // creating the database
    public $db;

    public function setDatabase() {
        $this->db = $this->client->super_real_db;
    }

    public function getDatabase() {
        return $this->db;
    }

    // loading in the initial data
    public $initial_users; 

    public function setInitialUsers() {
        $this->initial_users = include_once('users.inc.php');
    }

    public function getInitialUsers() {
        return $this->initial_users;
    }

    public function setVariables() {
        $this->setClient();
        $this->setDatabase();
        $this->setInitialUsers();
    }

    ########################################
    // general database stuff

    // function to create fresh database
    public function reset_users_collection($users) {
        $this->db->users->drop();
        $this->db->users->insertMany($users);
    }

    ########################################
    // get options

    public function get_one_user_by_name($name) {
        // you cant use backticks, if you want to use a varialbe, use " instead
        return $this->db->users->findOne(['name' => "$name"], ['projection' => ['_id' => 0]]);
    }

    public function get_all_users_by_name($name) {
        return $this->db->users->find(['name' => "$name"], ['projection' => ['_id' => 0]])->toArray();
    }

    public function get_all_users() {
        return $this->db->users->find([], ['projection' => ['_id' => 0]])->toArray();
    }

    ########################################
    // update options
    // replace options
    // delete options

};

// create object with the functions etc
$CRUD_operations = new CRUD_operations();

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
                        echo MongoDB\BSON\toJSON(MongoDB\BSON\fromPHP($document));
                        
                    } 

                    // return warning
                    else {
                        echo "please use ?name=desiredname to specify the desired user \n";
                    }
        
                    break;

                // make changes to a user
                case 'PUT':

                    break;

                // delete a user
                case 'DELETE':

                    break;

                // default
                default:
                    echo 'only GET, PUT and DELETE are available';


            }
            break;
        
        // add a new user
        case '/new_user':

            // next check method
            switch($request_method) {

                // create a new user
                case 'POST':

                    if (isset($body->new_user)) {
                        $new_user = $body->new_user;
                        if (isset($new_user->name) && isset($new_user->bio) && isset($new_user->username)) {
                            $document = $CRUD_operations->add_one_user(json_encode($new_user));
                            echo MongoDB\BSON\toJSON(MongoDB\BSON\fromPHP($document));

                        }
                    }

                    break;

                default:
                    echo "only POST is available, in the form: \n
                        'new_user' : \n
                            'name' : 'name', \n
                            'bio' : 'bio description', \n
                            'username' : 'username' \n
                        ";

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
                        echo MongoDB\BSON\toJSON(MongoDB\BSON\fromPHP($document));
                        
                    } 

                    // return warning
                    else {
                        echo "please use ?name=desiredname to specify the desired user \n";
                    }
        
                    break;


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
            echo "either /name or /reset are available \n";
            break;

    };
};



// step 4: setup api to allow crud operations
// step 5: hook up api crud operations to allow api to make changes
// step 6: move db class to it's own file
// step 7: move on to learning and building an Angular FE
