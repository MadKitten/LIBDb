<?php
require_once('../core.php');
require_once('../db.php');

if (!IsSet($_POST['ref_name'])) {
    $result['error'] = 1; $result['message'] = 'Unknown caller!'; print(json_encode($result)); exit();
}

$link = ConnectDB();

$q = array(
    'title' => mysql_escape_string($_POST['title']),
    'shortname' => mysql_escape_string($_POST['shortname'])
);
$qstr = MakeInsert($q,$_POST['ref_name']);
$res = mysql_query($qstr, $link) or Die("Unable to insert data to DB!".$qstr);
$new_id = mysql_insert_id() or Die("Unable to get last insert id!");

$result['message'] = $qstr;
$result['error'] = 0;

print(json_encode($result));
CloseDB($link);
?>