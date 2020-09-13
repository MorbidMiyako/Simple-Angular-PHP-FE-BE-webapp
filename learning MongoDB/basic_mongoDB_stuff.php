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
// var_dump($db);

########################
// step 2: use CRUD operations to practice changing stored data
########################

// CRUD operations:
// insert, find, update, delete


########################
// insert

// if the collection doesnt exist yet, it will be created automatically

// insertMany loops over an array and inserts each element into the specified collection, insertOne just inserts whats between the first square brackets ([ ]) 
$insertMany = $db->first_db->insertMany([
    [
        'item' => 'journal',
        'qty' => 50,
        'tags' => ['blank', 'red', 'blue', 'green'],
        'size' => ['h' => 14, 'w' => 21, 'uom' => 'cm'],
        'object' => [['key1' => 'value1.1', 'key2' => 'value2.1'], ['key1' => 'value1.2', 'key2' => 'value2.2'], ['key1' => 'value1.3', 'key2' => 'value2.3']]
    ],
    [
        'item' => 'mat',
        'qty' => 85,
        'tags' => ['gray'],
        'size' => ['h' => 27.9, 'w' => 35.5, 'uom' => 'cm'],
        'object' => [['key1' => 'mat_is_different', 'key2' => 'value2.1'], ['key1' => 'value1.2', 'key2' => 'value2.2'], ['key1' => 'value1.3', 'key2' => 'value2.3']]
    ],
    [
        'item' => 'mousepad',
        'qty' => 25,
        'tags' => ['gel', 'blue'],
        'size' => ['h' => 19, 'w' => 22.85, 'uom' => 'cm'],
        'object' => [['key1' => 'value1.1', 'key2' => 'value2.1'], ['key1' => 'value1.2', 'key2' => 'value2.2'], ['key1' => 'value1.3', 'key2' => 'value2.3']]
    ],
// added the mat item a second time, in order to test which would be returned by findOne
    [
        'item' => 'mat',
        'qty' => 25,
        'tags' => ['blue'],
        'size' => ['h' => 27.9, 'w' => 35.5, 'uom' => 'cm'],
        'object' => [['key1' => 'mat_is_different', 'key2' => 'value2.1'], ['key1' => 'value1.2', 'key2' => 'value2.2'], ['key1' => 'value1.3', 'key2' => 'value2.3']]
    ],
    [
        'item' => 'out of stock',
        'qty' => 0,
    ],
    [
        'item' => 'out of stock',
        'qty' => 0,
    ],
    [
        'item' => 'out of stock',
        'qty' => 0,
    ],
]);

// the returned variable from insertMany/insertOne details the success of the operation, and the resulting id's created
var_dump($insertMany);


########################
// find

// find one returns the first instance that matches the key->value pair
// you cant use backticks, if you want to use a varialbe, use " instead
$findMat = $db->first_db->findOne(['item' => 'mat']);
var_dump($findMat);

// if no match is found, returns NULL
$findPaper = $db->first_db->findOne(['item' => 'paper']);
var_dump($findPaper);

// to search for an array containing a value, use $all operator, for an exact matching array omit this operator 
$findMousepadByTag = $db->first_db->findOne(['tags' => ['$all' => ["gel"]]]);
var_dump($findMousepadByTag);

// to search for a lower key->value pair, chain using dots
$findMatByW = $db->first_db->findOne(['size.w' => 35.5]);
var_dump($findMatByW);

// this works if the next key is inside an array too
$findMatByW = $db->first_db->findOne(['object.key1' => 'mat_is_different']);
var_dump($findMatByW);

// one can also set multiple requirements
$findMousepadByQtyAndKey1 = $db->first_db->findOne(
    [
        'qty' => 25,
        'object.key1' => 'value1.1',
    ],
);
var_dump($findMousepadByQtyAndKey1);

// // findAll
// returns value containing mongoDB\Cursor class
$findAllMat = $db->first_db->find(['item' => 'mat']);

// a simple var_dump gives information regarding the request, however no information regarding the documents that meet the desired criteria
var_dump($findAllMat);

// using the toArray() function that is attached to the cursor class returns an array containing the documents meeting the query parameters
# var_dump($findAllMat->toArray());

// using foreach allows for quick access to each document, the toArray() step has to be skipped for this
foreach($findAllMat as $mat) {
    var_dump($mat);
};

////////////////////////////////////////
// adding projections

// specifying selection using projection, this works the same for findOne and find
$findJournalWithProjection = $db->first_db->findOne(
    // first argument remains the desired key->value pairs that are desired to match
    ['item' => 'journal'],
    
    // projection sets what values you want returned
    ['projection' => [
        // setting to 1 means returning
        // when you select a value to return, it will only return what you selected
        'item' => 1,
        // aside from '_id', this variable is always returned unless specifically told not to
        // setting to 0 means not returning
        '_id' => 0,
        // when wanting a value returned from an array, use the $slice operator, first from which index to start selecting, then what index to last select
        // remember this syntax, other operators can be found here https://docs.mongodb.com/manual/reference/operator/
        'tags' => ['$slice' => [1,2]],
        // when wanting a value returned from an object, use . to chain keys (dot property accessor), similar to javascript
        'size.uom' => 1,
        // this also works if the next key is nested inside an array
        'object.key1' => 1,
        // however if one wants to select only certain elements of this array, this should be done using aggregation
        // 'object.key1' => ['$slice' => 1],
    ]]
);
var_dump($findJournalWithProjection);

// by only setting to 0, everything else will be returned
$findMousepadWithZeroProjection = $db->first_db->findONe(
    ['item' => 'mousepad'],
    ['projection' => [
        'id' => 0,
        'object' => 0,
    ]]
);
var_dump($findMousepadWithZeroProjection);

########################
// update and replace

////////////////////////////////////
// update

// update can both add a value or update an existing one

// before and after comparison
$before = $db->first_db->findOne(['item' => 'mousepad'],['projection' => ['qty' => 1,'new' => 1, '_id' => 0]]);
var_dump($before);

// update one or all work essentially the same, just either the first matching intance or all matchin instances get updated
$updateMousepadQty = $db->first_db->updateOne(
    ['item' => 'mousepad'], 
    ['$set' => [
                    // these will be the changed values
                    'qty' => 28, 
                    // if the key is new, it will be added instead
                    'new' => 'brand new'
                ]],
    // if no document is found, it will insert
    // upsert -> update or insert, this option is optional
    ['upsert' => true],
);
// returned value is information regarding the change
var_dump($updateMousepadQty);

$after = $db->first_db->findOne(['item' => 'mousepad'],['projection' => ['qty' => 1, 'new' => 1, '_id' => 0]]);
var_dump($after);

$updateAllWithRating = $db->first_db->updateMany(
    // leave selector emtpy to select all
    [],
    ['$set' => ['rating' => 'unrated']],
);
var_dump($updateAllWithRating);

//////////////////////////////////////
// // replace

// replaces the document, this differs from deleting and inserting since the id will remain the same by using replace
// replace differs from update in that replace will create an object with only the given values while update will keep the not given values the same

// before and after comparison
$before = $db->first_db->findOne(['_id' => new MongoDb\BSON\ObjectId('5f5d34cbd4590000ad0037d5')] , ['projection' => ['qty' => 1,'new' => 1]]);
var_dump($before);

// replace one works essentially the same as update
// there is only replace one, 
$replaceSecondMat = $db->first_db->replaceOne(
    // use this method to get a document by its id
    ['_id' => new MongoDb\BSON\ObjectId('5f5d34cbd4590000ad0037d5')], 
    [
        // these will be the new values for the document, alongside with the original _id
        'item' => "out of stock",
        'qty' => 0, 
    ],
);
// returned value is information regarding the change
var_dump($replaceSecondMat);

$after = $db->first_db->findOne(['_id' => new MongoDb\BSON\ObjectId('5f5d34cbd4590000ad0037d5')] , ['projection' => ['qty' => 1,'new' => 1]]);
var_dump($after);

########################
// delete

// there are several options to delete, either you delete one, you delete many, or you delete a whole collection

// delete one deletes first that matches its query selector
// delete returns statistics regarding the operation
$deleteFirstOutOfStock = $db->first_db->deleteOne(['item'=> 'out of stock']);
var_dump($deleteFirstOutOfStock);

// delete many deletes all matches
$deleteAllOutOfStock = $db->first_db->deleteMany(['item'=> 'out of stock']);
var_dump($deleteAllOutOfStock);

// to delete a complete collection, use drop()
// remove() is a less complete deletion, since it keeps indecies etc, it also allows for arguments to specify what to delete
// finally deleteMany with an empty query would also delete all documents
// drop is the only one that actually deletes the whole collection
$db->first_db->drop();
var_dump($db->firs_db);

// step 3: hook database up to api -> test using simple get
// step 4: setup api to allow crud operations
// step 5: hook up api crud operations to allow api to make changes
// step 6: move on to learning and building an Angular FE
