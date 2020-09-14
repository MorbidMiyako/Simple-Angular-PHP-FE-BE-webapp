<?php

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
        // you cant use backticks, if you want to use a variable's values as string, use " instead
        // $sth = "something" | '$sth' => '$sth' | "$sth" => "something"
        return $this->db->users->findOne(['name' => "$name"]);
    }

    public function get_all_users_by_name($name) {
        return $this->db->users->find(['name' => "$name"])->toArray();
    }

    public function get_all_users() {
        return $this->db->users->find([])->toArray();
    }

    ########################################
    // post option

    public function add_one_user($new_user) {
        return $this->db->users->insertOne($new_user);
    }

    public function add_users($new_users) {
        return $this->db->users->insertMany($new_users);
    }

    ########################################
    // update options

    public function change_user($id, $new_values) {
        return $this->db->users->updateOne(['_id' => new MongoDb\BSON\ObjectId($id)], ['$set' => $new_values]);
    }

    ########################################
    // delete options

    public function delete_user($id) {
        return $this->db->users->deleteOne(["_id" => new MongoDb\BSON\ObjectId($id)]);
    }

};

return new CRUD_operations();
