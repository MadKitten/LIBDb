<?php
require_once('../core.php');
require_once('../core.db.php');

// отдает JSON объект для селектора 'books'
// this script is duplicated with books.action.getoptionlist.php,
// differences is returned data format. @todo: optimise .js & remove this

$lang = isset($_GET['lang']) ? $_GET['lang'] : 'ru';

$lang = getAllowedValue( $lang, array(
    'ru', 'en', 'ua', 'uk'
));

$withoutid = isset($_GET['withoutid']) ? 1 : 0;


$link = ConnectDB();

$query = "SELECT * FROM books ORDER BY SUBSTRING(title, 6, 2)";
$result = mysql_query($query) or die($query);
$ref_numrows = @mysql_num_rows($result) ;

if ($ref_numrows>0)
{
    $data['error'] = 0;
    while ($row = mysql_fetch_assoc($result))
    {
        $data['data'][ $row['id'] ] = returnBooksOptionString($row,$lang,$withoutid); // see core.php
    }
} else {
    $data['data'][1] = "Добавьте книги (сборники) в базу!!!";
    $data['error'] = 1;
}

CloseDB($link);
print(json_encode($data));
?>