<?php
require_once('../core.php');
require_once('../core.db.php');
require_once('../core.kwt.php');

$ref_name = 'staticpages';
$id = isset($_POST['id']) ? $_POST['id'] : Die('Unknown ID. ');

// printr($_POST);

$link = ConnectDB();
$q = array(
    'alias' => mysql_escape_string($_POST['alias']),
    'comment' => mysql_escape_string($_POST['comment']), //@todo: NOW экранирование кавычек
    'title_en' => mysql_escape_string($_POST['title_en']),
    'title_ru' => mysql_escape_string($_POST['title_ru']),
    'title_uk' => mysql_escape_string($_POST['title_uk']),
    'content_en' => mysql_escape_string($_POST['content_en']),
    'content_ru' => mysql_escape_string($_POST['content_ru']),
    'content_uk' => mysql_escape_string($_POST['content_uk']),
);

$qstr = MakeUpdate($q, $ref_name, "WHERE id=$id");

if ($res = mysql_query($qstr, $link)) {
    $result['message'] = $qstr;
    $result['error'] = 0;
}
else {
    Die("Unable to insert data to DB!  ".$qstr);
}

CloseDB($link);

if (isAjaxCall()) {
    print(json_encode($result));
} else {
    if ($result['error'] == 0) {
        // use template
        $override = array(
            'time' => 10,
            'target' => '/core/ref.pages.show.php',
            'buttonmessage' => 'Вернуться к списку страниц',
            'message' => 'Данные обновлены'
        );
        $tpl = new kwt('../ref.all.timed.callback.tpl');
        $tpl->override($override);
        $tpl->out();
    }
}
?>