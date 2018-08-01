<?php
require_once '../__required.php'; // $mysqli_link
if (!IsSet($_POST['ref_name'])) {
    $result['error'] = 1; $result['message'] = 'Unknown caller!'; print(json_encode($result)); exit();
}


$q = array(
    'title_en' => mysqli_real_escape_string($mysqli_link, $_POST['title_en']),
    'title_ru' => mysqli_real_escape_string($mysqli_link, $_POST['title_ru']),
    'title_uk' => mysqli_real_escape_string($mysqli_link, $_POST['title_uk']),
    'rel_group' => mysqli_real_escape_string($mysqli_link, $_POST['rel_group']),
);
$qstr = MakeInsert($q,$_POST['ref_name']);
$res = mysqli_query($mysqli_link, $qstr) or Die("Unable to insert data to DB!".$qstr);
$new_id = mysqli_insert_id() or Die("Unable to get last insert id!");

kwLogger::logEvent('Add', 'topics', $new_id, "Topic added, id = {$new_id}");

$result['message'] = $qstr;
$result['error'] = 0;

print(json_encode($result));