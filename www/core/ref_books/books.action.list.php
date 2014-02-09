<?php
require_once('../core.php');
require_once('../core.db.php');
require_once('../core.kwt.php');

// выводит в виде таблицы содержимое справочника 'books' в админку

$link = ConnectDB();

$ref_name = 'books';

$query = "SELECT books.id AS book_id, books.title, books.date, contentpages, published, file_cover, file_title, file_toc,
COUNT(articles.book) AS book_articles_count
FROM books LEFT JOIN articles ON
books.id=articles.book
GROUP BY books.id, books.title, books.year";

$res = mysql_query($query) or die("Невозможно получить содержимое справочника! ".$query);
$ref_numrows = @mysql_num_rows($res) ;

if ($ref_numrows > 0) {
    while($ref_record = mysql_fetch_assoc($res)) {
        $ref_list[$ref_record['book_id']] = $ref_record;
    }
} else {
    $ref_message = 'Пока не добавили ни один сборник!';
}


CloseDB($link);
?>
<table border="1" width="100%">
    <tr>
        <th width="5%">(id)</th>
        <th width="20%">Название или номер сборника</th>
        <th width="10%">Дата/год выпуска</th>
        <th width="10%">Страницы со статьями</th>
        <th width="15%">Сборник готов? </th>
        <th width="10%">Кол-во статей</th>
        <th width="15%">Файлы</th>
        <th width="7%">Управление</th>
    </tr>
    <?php
    if ($ref_numrows > 0) {
        foreach ($ref_list as $r_id => $book)
        {
            //@todo: MOVE TO TEMPLATE
            $book_ready = ($book['published']!=0) ? "Да<br><small>(опубликован)</small>" : "Нет<br><small>(в работе)</small>";
            echo <<<REF_ANYROW
<tr>
    <td class="centred_cell">{$book['book_id']}         </td>
    <td>{$book['title']}                                </td>
    <td class="centred_cell">{$book['date']}            </td>
    <td class="centred_cell">{$book['contentpages']}    </td>
    <td class="centred_cell">{$book_ready}              </td>
    <td class="centred_cell">
        <a href="/?fetch=articles&with=book&id={$book['book_id']}" target="_blank">
            {$book['book_articles_count']}
        </a>
    </td>
    <td>
        <a href="getimage.php?id={$book['file_cover']}" class="icon-jpg icon lightbox-image">Обложка</a>
        <br>
        <a href="getfile.php?id={$book['file_title']}" class="icon-pdf icon">Титульник</a>
        <br>
        <a href="getfile.php?id={$book['file_toc']}" class="icon-pdf icon">Оглавление</a>
    </td>
    <td class="centred_cell"><button class="edit_button" name="{$book['book_id']}">Edit</button></td>
</tr>
REF_ANYROW;
        }
        echo "</table>";
    } else {
        echo <<<REF_NUMROWS_ZERO
<tr><td colspan="8">$ref_message</td></tr>
REF_NUMROWS_ZERO;
    }

    ?>

</table>