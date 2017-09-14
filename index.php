<?php
//define default file and create it.
define('DATABASE', 'database/database.json');
include_once 'class/jsonSQL.php';
$db = new jsonSQL();
$table = 'news';
// You should be create unique data id. This is expample
$dataId = 'SXHBQ653862SXQ';
$data = [
    "id" => 1,
    "newsid" => "SXHBQ653862SXQ",
    "category" => "football",
    "image" => "example-news-football.jpg",
    "title" => "Example Footbal Data Title",
    "desc" => "Example football data description",
];
// Create table
$db->select($table);
// Check and set data
// if there are changes in your data, or if new data is to should be add, it checks and performs these operations.
$value = $db->insert($table)->set($dataId,$data)->getValue();
// Add data
$db->add($value);

// List Data
$valueList = $db->select('news')->result();

// Filter for category
$valueFilter = $db->select('news')->filter('category', 'football')->result();

// Sort by id (DESC, ASC) , default ASC
$valueSort = $db->select('news')->sort('id', SORT_DESC)->result();

// Offset and Limit
$valueLimit = $db->select('news')->limit(0,10)->result();
$valueLimitFilter = $db->select('news')->filter('category', 'football')->limit(0,10)->result();