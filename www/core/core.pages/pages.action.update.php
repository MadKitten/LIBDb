<?php
require_once '../__required.php'; // $mysqli_link

$ref_name = 'staticpages';
$id = isset($_POST['id']) ? $_POST['id'] : Die('Unknown ID. ');

$q = array(
    'alias'         => mysqli_real_escape_string($mysqli_link, $_POST['alias'] ?? ''),
    'comment'       => mysqli_real_escape_string($mysqli_link, $_POST['comment'] ?? ''),
    'title_en'      => mysqli_real_escape_string($mysqli_link, $_POST['title_en'] ?? ''),
    'title_ru'      => mysqli_real_escape_string($mysqli_link, $_POST['title_ru'] ?? ''),
    'title_uk'      => mysqli_real_escape_string($mysqli_link, $_POST['title_uk'] ?? ''),
    'content_en'    => mysqli_real_escape_string($mysqli_link, $_POST['content_en'] ?? ''),
    'content_ru'    => mysqli_real_escape_string($mysqli_link, $_POST['content_ru'] ?? ''),
    'content_uk'    => mysqli_real_escape_string($mysqli_link, $_POST['content_uk'] ?? ''),
);

$qstr = MakeUpdate($q, $ref_name, " WHERE id={$id} ");

if ($res = mysqli_query($mysqli_link, $qstr)) {
    $result['message'] = $qstr;
    $result['error'] = 0;
    kwLogger::logEvent('Update', 'pages', $id, "Static page updated, id = {$id}");
}
else {
    Die("Unable to insert data to DB!  ".$qstr);
}


if (isAjaxCall()) {
    print(json_encode($result));
} else {
    if ($result['error'] == 0) {

        $template_dir = '$/core/_templates';
        $template_file = "ref.all_timed_callback.html";

        $template_data = array(
            'time'          => $CONFIG['callback_timeout'] ?? 15,
            'target'        => '../list.pages.show.php',
            'button_text'   => 'Вернуться к списку страниц',
            'message'       => 'Статическая страница обновлена'
        );
        echo \Websun\websun::websun_parse_template_path($template_data, $template_file, $template_dir);
    }
}