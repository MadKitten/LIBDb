<?php
require_once '../__required.php'; // $mysqli_link

// выводит в виде таблицы содержимое справочника 'books' в админку

$ref_name = 'books';

$query = "SELECT books.id AS book_id, books.title, books.date, contentpages, published, file_cover, file_title_ru, file_title_en, file_toc_ru, file_toc_en,
 COUNT(articles.book) AS book_articles_count
 FROM books LEFT JOIN articles ON books.id=articles.book
 GROUP BY books.id, books.title, books.year
 ORDER BY books.title DESC";

$res = mysqli_query($mysqli_link, $query) or die("Невозможно получить содержимое справочника! ".$query);
$ref_numrows = @mysqli_num_rows($res) ;

$ref_list = array();

if ($ref_numrows > 0) {
    while($ref_record = mysqli_fetch_assoc($res)) {
        $ref_list[$ref_record['book_id']] = $ref_record;
    }
}

CloseDB($link);
$return = <<<BAL_Start
<table border="1" width="100%">
BAL_Start;

$return .= <<<BAL_TH
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
BAL_TH;

if ($ref_numrows > 0)
{
    foreach ($ref_list as $r_id => $book)
    {
        $book_ready = ($book['published']!=0) ? "Да<br><small>(опубликован)</small>" : "Нет<br><small>(в работе)</small>";

        $return .= <<<BAL_EACH
<tr>
    <td class="centred_cell">{$book['book_id']}         </td>
    <td>{$book['title']}                                </td>
    <td class="centred_cell">{$book['date']}            </td>
    <td class="centred_cell">{$book['contentpages']}    </td>
    <td class="centred_cell">{$book_ready}              </td>
    <td class="centred_cell">
        <a href="/core/ref.articles.show.php#with_book={$book['book_id']}" target="_blank">
            {$book['book_articles_count']}
        </a>
    </td>
    <td>
        <a href="getimage.php?id={$book['file_cover']}" class="icon-jpg icon lightbox-image">Обложка</a>
        <br>
        <a href="getfile.php?id={$book['file_title_ru']}" class="icon-pdf icon">Титульник (рус)</a>
        <br>
        <a href="getfile.php?id={$book['file_title_en']}" class="icon-pdf icon">Титульник (англ)</a>
        <br>
        <a href="getfile.php?id={$book['file_toc_ru']}" class="icon-pdf icon">Оглавление (рус)</a>
        <br>
        <a href="getfile.php?id={$book['file_toc_en']}" class="icon-pdf icon">Английское оглавление</a>
    </td>
    <td class="centred_cell"><button class="action-edit button-edit" name="{$book['book_id']}">Edit</button></td>
</tr>
BAL_EACH;
    }

} else {
    $return .= <<<AAL_NOTHING
<tr><td colspan="8">Пока не добавили ни один сборник!</td></tr>
AAL_NOTHING;
}

$return .= <<<AAL_END
</table>
AAL_END;

print($return);
