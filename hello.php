<?php

########################
// step 1: create mongoDB and connect to it
########################

//composer installed mongoDB extension into the project, this line allows the file access to the extension
require 'vendor/autoload.php';

$client = new MongoDB\Client(
    // yes, i am aware that having a generic password and that having it exposed is bad practice
    // due to the database being for testing purposes, and access being limited to IP, I feel this is okay for the time being
    // in a real world scenario, one would use ENV variables, and access them using $_ENV, allowing such variables to be hidden
    'mongodb+srv://admin:admin@mytestingden.foc2e.mongodb.net/?retryWrites=true&w=majority');

// setup of a mongoDB:
// cluster -> what the client connects to -> folder containing databases or "collections"
// database -> contains lists or "collections"
// collection -> contains the final information in the form of objects or "documents" (documents seems to reference the method of storage)

//select desired database
$db = $client->my_first_db;

//prints information about the db, shows its connected successfully
var_dump($db);

########################
// step 2: use CRUD operations to practice changing stored data
// step 3: hook database up to api -> test using simple get
// step 4: setup api to allow crud operations
// step 5: hook up api crud operations to allow api to make changes
// step 6: move on to learning and building an Angular FE
