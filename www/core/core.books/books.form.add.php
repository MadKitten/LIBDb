<?php
define('__ACCESS_MODE__', 'admin');
require_once '../__required.php'; // $mysqli_link

$max_upload_filesize = FileStorage::getRealMaxUploadFileSize();

?>
<html lang="ru">
<head>
    <title>Сборники -- добавление</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <script type="text/javascript" src="../_assets/jquery-1.10.2.min.js"></script>
    <script type="text/javascript" src="../_assets/jquery-ui-1.10.3.custom.min.js"></script>
    <script type="text/javascript" src="../_assets/jquery.ui.datepicker.rus.js"></script>

    <link rel="stylesheet" type="text/css" href="../_assets/jquery-ui-1.10.3.custom.min.css">
    <link rel="stylesheet" type="text/css" href="../_assets/core.admin.css">
    <link rel="stylesheet" type="text/css" href="books.css">

    <script type="text/javascript" src="../../frontend.js"></script>
    <script type="text/javascript" src="../../frontend.options.js"></script>


    <script type="text/javascript">
        $(document).ready(function () {
        $("#actor-exit").on('click',function(event){
            window.location.href = '../list.books.show.php';
        });
        $("#actor-remove").on('click',function(event){
            // window.location.href = 'books.action.remove.php?id='+author_id;
            alert('false');
        });
        $("#form_book").submit(function(){
            var bValid = true;
            if ($('input[name="file_cover"]').val() == '') {
                ShowErrorMessage('Обязательно укажите файл с обложкой (изображение в формате JPG/GIF/PNG) ! ');
                bValid = false;
            }
            if (!strpos($('input[name="file_title_ru"]').val() , '.pdf')) {
                ShowErrorMessage('Файл с кириллическим титульным листом должен быть в формате PDF! ');
                bValid = false;
            }
            if (!strpos($('input[name="file_title_en"]').val() , '.pdf')) {
                ShowErrorMessage('Файл с английским титульным листом должен быть в формате PDF! ');
                bValid = false;
            }
            if (!strpos($('input[name="file_toc_ru"]').val() , '.pdf')) {
                ShowErrorMessage('Файл с кириллическим оглавлением должен быть в формате PDF! ');
                bValid = false;
            }
            if (!strpos($('input[name="file_toc_en"]').val() , '.pdf')) {
                ShowErrorMessage('Файл с английским оглавлением должен быть в формате PDF! ');
                bValid = false;
            }
            return bValid;
        });
        // WIDGETS
        $("#book_datepicker").datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'dd.mm.yy',
            minDate: '01.01.2003',
            maxDate: '01.01.2025',
            showButtonPanel: true
        });
        $("#book_title").focus();

    });
    </script>
</head>
<body>
<form action="books.action.insert.php" method="post" enctype="multipart/form-data" id="form_book">
    <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $max_upload_filesize; ?>">
    <fieldset class="fields_area rounded">
        <legend>Данные о сборнике</legend>
        <div class="field">
            <label for="book_title_en">Title:</label>
            <input type="text" name="book_title_en" id="book_title_en">
        </div>
<?php if (Config::get('frontend/theme/book:use_lang_depended_title', false)) { ?>
        <div class="field">
            <label for="book_title_ru">Название (RU):</label>
            <input type="text" name="book_title_ru" id="book_title_ru">
        </div>
        <div class="field">
            <label for="book_title_ua">Название (UA):</label>
            <input type="text" name="book_title_ua" id="book_title_ua">
        </div>
        <hr>
<?php } ?>        
        <div class="field">
            <label for="book_datepicker">Дата выпуска:</label>
            <input type="text" class="book_datepicker" id="book_datepicker" name="book_publish_date">
        </div>
        <div class="field">
            <label for="book_contentpages">Страницы со статьями:</label>
            <input type="text" name="book_contentpages" id="book_contentpages">
        </div>
        <div class="field">
            <label for="is_book_ready">
                Выпущен ли сборник:
            </label>
            <select name="is_book_ready" id="is_book_ready"><option value="0">Нет (в работе)</option><option value="1">Да (опубликован)</option></select>
        </div>
    </fieldset>

    <fieldset class="fields_area rounded">
        <legend>Файлы</legend>
        <div class="field">
            <label for="file_cover">Обложка (изображение)</label>
            <input type="file" name="file_cover" id="file_cover" size="80" required>
            <button class="file-unlink" name="file_cover" disabled>X</button>
        </div>
        <div class="field">
            <label for="file_title_ru">Титульный лист, кириллический (PDF-file)</label>
            <input type="file" name="file_title_ru" id="file_title_ru" size="80" required>
            <button class="file-unlink" name="file_title_ru" disabled>X</button>
        </div>
        <div class="field">
            <label for="file_title_en">Титульный лист, английский (PDF-file)</label>
            <input type="file" name="file_title_en" id="file_title_en" size="80" required>
            <button class="file-unlink" name="file_title_en" disabled>X</button>
        </div>
        <div class="field">
            <label for="file_toc_ru">Оглавление (PDF-file)</label>
            <input type="file" name="file_toc_ru" id="file_toc_ru" size="80" required>
            <button class="file-unlink" name="file_toc_ru" disabled>X</button>
        </div>
        <div class="field">
            <label for="file_toc_en">Table of contents (PDF-file)</label>
            <input type="file" name="file_toc_en" id="file_toc_en" size="80" required>
            <button class="file-unlink" name="file_toc_en" disabled>X</button>
        </div>
    </fieldset>
    <fieldset class="fields_area rounded">
        <legend>Управление</legend>
        <button type="button" class="button-large" id="actor-exit"><strong>ВЕРНУТЬСЯ К СПИСКУ СБОРНИКОВ</strong></button>
        <button disabled type="button" class="button-large" id="actor-remove"><strong>УДАЛИТЬ СБОРНИК</strong></button>
        <button type="submit" class="button-large"><strong>ДОБАВИТЬ СБОРНИК</strong></button>
    </fieldset>
</form>

</body>
</html>