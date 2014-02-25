<?php
require_once('../core.php');
require_once('../core.db.php');
require_once('../core.kwt.php');

$SID = session_id();
if(empty($SID)) session_start();
if (!isLogged()) header('Location: /core/');

$ref_filestorage = 'filestorage';

$result['message'] = '';
$result['error'] = 0;

if (!IsSet($_POST['caller'])) {
    $result['error'] = 1; $result['message'] .= 'Unknown caller!'; print(json_encode($result)); exit();
}

$link = ConnectDB();

$q = array(
    'udc' => mysql_escape_string($_POST['udc']),
    'title_en' => mysql_escape_string($_POST['title_en']),
    'title_ru' => mysql_escape_string($_POST['title_ru']),
    'title_uk' => mysql_escape_string($_POST['title_uk']),
    'abstract_en' => mysql_escape_string($_POST['abstract_en']),
    'abstract_ru' => mysql_escape_string($_POST['abstract_ru']),
    'abstract_uk' => mysql_escape_string($_POST['abstract_uk']),
    'keywords_en' => mysql_escape_string($_POST['keywords_en']),
    'keywords_ru' => mysql_escape_string($_POST['keywords_ru']),
    'keywords_uk' => mysql_escape_string($_POST['keywords_uk']),
    'refs_ru' => mysql_escape_string($_POST['refs_ru']),
    'refs_en' => mysql_escape_string($_POST['refs_en']),
    'refs_uk' => mysql_escape_string($_POST['refs_ru']),
    'book' => mysql_escape_string($_POST['book']),
    'add_date' => mysql_escape_string($_POST['add_date']),
    'topic' => mysql_escape_string($_POST['topic']),
    'pages' => mysql_escape_string($_POST['pages'])
);

// теперь нам нужно вставить данные в БАЗУ (пока что с учетом вставки файла в БЛОБ)
$qstr = MakeInsert($q,'articles');
$res = mysql_query($qstr, $link) or Die("Невозможно вставить данные в базу  ".$qstr);
$new_id = mysql_insert_id() or Die("Не удалось получить id последней добавленной записи!");

if (IsSet($_FILES)) {

    $insert_data = array(
        'username' => $pdf_username = $_FILES['pdffile']['name'],
        'tempname' => ($_SERVER['REMOTE_ADDR']==="127.0.0.1") ? str_replace('\\','\\\\',$_FILES['pdffile']['tmp_name']) : $_FILES['pdffile']['tmp_name'],
        'filesize' => $_FILES['pdffile']['size'],
        'relation' => $new_id,
        'filetype' => $_FILES['pdffile']['type'],
        'collection' => 'articles'
    );

    $insert_data['content'] = mysql_escape_string(floadpdf($insert_data['tempname']));

    $q = MakeInsert($insert_data, $ref_filestorage);

    mysql_query($q, $link) or Die("Death on $q");
    $pdf_id = mysql_insert_id() or Die("Не удалось получить id последней добавленной записи!");

    $q = "UPDATE articles SET pdfid=$pdf_id  WHERE id=$new_id";
    mysql_query($q, $link) or Die("Death on $q");
} else {
    $result['error'] = 1;
    $result['message'] .= "Не выбран файл для загрузки или ошибка передачи данных! <br>\r\n";
}

// потом обновить кросс-таблицу
// в едите нужно удалить старые значения, потом добавить новые
if (IsSet($_POST['authors'])) {
    $authors = $_POST['authors'];
    foreach ($authors as $n => $author) {
        $qa = "INSERT INTO cross_aa (author,article) VALUES ($author, $new_id)";
        mysql_query($qa , $link) or Die('error at '.$qa);
    }
} else {
    $result['error'] = 1;
    $result['message'] .= "Не указаны авторы!<br>\r\n";
}

CloseDB($link);

if ($result['error'] == 0) {
    $override = array(
        'time' => 10,
        'target' => '/core/ref.articles.show.php',
        'buttonmessage' => 'Вернуться к списку статей',
        'message' => 'Статья добавлена'
    );
} else {
    $override = array(
        'time' => 10,
        'target' => '/core/ref.articles.show.php',
        'buttonmessage' => 'Вернуться к списку статей',
        'message' => $result['message']
    );
}
$tpl = new kwt('../ref.all.timed.callback.tpl');
$tpl->override($override);
$tpl->out();

?>