<?php
require_once '../__required.php'; // $mysqli_link

$ref_name = 'books';

$book_id = $_POST['book_id'];

//@todo: use Datetime->

$q = array(
    'title'         => mysqli_real_escape_string($mysqli_link, $_POST['book_title']),
    'contentpages'  => mysqli_real_escape_string($mysqli_link, $_POST['book_contentpages']),
    'published_status'  => mysqli_real_escape_string($mysqli_link, $_POST['is_book_ready']),
    'published_date'    => DateTime::createFromFormat('d.m.Y', $_POST['book_publish_date'])->format('Y-m-d'),

    // 'date'          => mysqli_real_escape_string($mysqli_link, $_POST['book_date']), // конвертировать в date format
    //@todo:date убрать
    // 'year'          => substr(mysqli_real_escape_string($mysqli_link, $_POST['book_date']), 6, 4), // тоже не нужно, будем брать
    // 'timestamp'     => ConvertDateToTimestamp(mysqli_real_escape_string($mysqli_link, $_POST['book_date'])), // зачем?

    //@todo: убрать, этим занимается БД
    // 'stat_date_update' => ConvertTimestampToDate() // это задача БД
);

$qstr = MakeUpdate($q, $ref_name, " WHERE id = {$book_id}");
$res = mysqli_query($mysqli_link, $qstr) or Die("Невозможно обновить данные в базе  ".$qstr);

if (count($_FILES)>0) {
    // Если массив $_FILES не пуст - это означает, что файлы присоединили.
    // И, что самое главное, в EDIT - их "разлинковывали" и добавляли новые!
    // неважно сколько файлов пришло из формы - обработаем массив с ними в цикле

    foreach ($_FILES as $a_file => $a_data) {
        FileStorage::addFile($a_data, $book_id, 'books', $a_file);
    }
    $result['error'] = 0;
    $result['message'] = "Данные обновлены, новые файлы в базу добавлены!";
} else {
    $result['error'] = 1;
    // файлы не менялись, хотя прочие данные обновлены
    $result['message'] = "Данные обновлены!";
}

kwLogger::logEvent('Update', 'books', $book_id, "Updated book, id = {$book_id}");


if (isAjaxCall()) {
    print(json_encode($result));
} else {

    $template_dir = '$/core/_templates';
    $template_file = "ref.all_timed_callback.html";

    $template_data = array(
        'time'          => Config::get('callback_timeout') ?? 15,
        'target'        => '../list.books.show.php',
        'button_text'   => 'Вернуться к списку сборников',
    );

    $template_data['message'] = ($result['error'] == 0) ? 'Сборник обновлен' : $result['message'];


    echo \Websun\websun::websun_parse_template_path($template_data, $template_file, $template_dir);
}
