<?php
// функции ядра

/**
 * @param $filename
 * @return string
 */
function floadpdf($filename)
{
    $fh = fopen($filename,"rb");
    $real_filesize = filesize($filename);
    $blobdata = fread($fh, $real_filesize);
    fclose($fh);
    return $blobdata;
}

/**
 *
 */
function floadfile($filename)
{
    $fh = fopen($filename,"rb");
    $real_filesize = filesize($filename);
    $blobdata = fread($fh, $real_filesize);
    fclose($fh);
    return $blobdata;
}



/**
 * @param bool $debugmode
 * @return bool
 */
function isAjaxCall($debugmode=false)
{
    $debug = (isset($debugmode)) ? $debugmode : false;
    return ((!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) && (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) || ($debug);
}

/**
 * @param $url
 */
function Redirect($url)
{
    if (headers_sent() === false) header('Location: '.$url);
}

/**
 * @return int
 */
function isLogged()
{
    // вот тут мы проверямем куки и сессию на предмет "залогинились ли мы"
    $we_are_logged = !empty($_SESSION);
    $we_are_logged = $we_are_logged && isset($_SESSION['u_id']);
    $we_are_logged = $we_are_logged && $_SESSION['u_id'] !== -1;
    $we_are_logged = $we_are_logged && isset($_COOKIE['u_libdb_logged']);
    return (int) $we_are_logged ;
}

/**
 * @param $str
 */
function printr($str)
{
    echo '<pre>'.print_r($str,true).'</pre>';
}

/* Три функции возврата данных в option соотв. селекта */
/* объявление переехало в frontend_.php */

/**
 * @param $row
 * @param $lang
 * @param $withoutid
 * @return string
 */
function returnBooksOptionString($row, $lang, $withoutid)
{
    // @todo: ВАЖНО: ТУТ ЗАДАЕТСЯ ФОРМАТ ВЫВОДА ДАННЫХ В СЕЛЕКТ (оформить функцией на основе шаблона? )
    // по идее можно и с шаблоном, но ну нафиг
    /*     switch ($lang) {
            case 'en': {
                $name = $row['name_en'];
                $title = $row['title_en'];
                break;
            }
            case 'ru': {
                $name = $row['name_ru'];
                $title = $row['title_ru'];
                break;
            }
            case 'uk': {
                $name = $row['name_uk'];
                $title = $row['title_uk'];
                break;
            }
        } */
    $id = ($withoutid==1) ? '' : "[{$row['id']}] " ;

    $title = ($row['title'] != '') ? $row['title'] : 'Unnamed';

    return $id."\"$title\"";
}

/**
 * @param $row
 * @param $lang
 * @param $withoutid
 * @return string
 */
function returnAuthorsOptionString($row, $lang, $withoutid)
{
    // @todo: ВАЖНО: ТУТ ЗАДАЕТСЯ ФОРМАТ ВЫВОДА ДАННЫХ В СЕЛЕКТ (оформить функцией на основе шаблона? )
    // по идее можно и с шаблоном, но ну нафиг
    $id = ($withoutid==1) ? '' : "[{$row['id']}] " ;
    switch ($lang) {
        case 'en': {
            $name = $row['name_en'];
            $title = $row['title_en'];
            break;
        }
        case 'ru': {
            $name = $row['name_ru'];
            $title = $row['title_ru'];
            break;
        }
        case 'uk': {
            $name = $row['name_uk'];
            $title = $row['title_uk'];
            break;
        }
    }
    return $id."$name $title";

}

/**
 * @param $row
 * @param $lang
 * @param $withoutid
 * @return string
 */
function returnTopicsOptionString($row, $lang, $withoutid)
{
    // @todo: ВАЖНО: ТУТ ЗАДАЕТСЯ ФОРМАТ ВЫВОДА ДАННЫХ В СЕЛЕКТ (оформить функцией на основе шаблона? )
    // по идее можно и с шаблоном, но ну нафиг
    $title = '';
    switch ($lang) {
        case 'en': {
            $title = $row['title_en'];
            break;
        }
        case 'ru': {
            $title = $row['title_ru'];
            break;
        }
        case 'uk': {
            $title = $row['title_uk'];
            break;
        }
    }
    $id = ($withoutid==1) ? '' : "[{$row['id']}] " ;
    $title = ($title != '') ? $title : '< NONAME >';

    return $id.$title;
}

function returnNewsOptionString($row, $lang, $withoutid) // © Thomas Moroh
{
    $id = ($withoutid==1) ? '' : "[{$row['id']}] " ;
    switch ($lang) {
        case 'en': {
            $name = $row['text_en'];
            $title = $row['title_en'];
            break;
        }
        case 'ru': {
            $name = $row['text_ru'];
            $title = $row['title_ru'];
            break;
        }
        case 'uk': {
            $name = $row['text_uk'];
            $title = $row['title_uk'];
            break;
        }
    }
    return $id."$name $title";
}

/*
sell also:
http://www.tools4noobs.com/online_php_functions/date_parse/
http://php.fnlist.com/date_time/mktime
http://www.php.net/manual/ru/function.mktime.php
*/

function ConvertDateToArray($str_date)
{
    if (function_exists('date_parse_from_format')) {
        $date_as_array = date_parse_from_format('d/m/Y',$str_date);
    } else {
        $date_as_array = date_parse($str_date);
    }
    return $date_as_array;
}

function ConvertDateToTimestamp($str_date, $format="d/m/Y")
{
    if (function_exists('date_parse_from_format')) {
        $date_array = date_parse_from_format('d.m.Y',$str_date);
    } else {
        $date_array = date_parse($str_date);
    }
    return mktime(12, 0, 0, $date_array['month'], $date_array['day'], $date_array['year']);
}

function ConvertToHumanBytes($size) {
    $filesizename = array(" Bytes", " K", " M", " G", " T", " P", " E", " Z", " Y");
    return $size ? round($size / pow(1024, ($i = floor(log($size, 1024)))), 0) . $filesizename[$i] : '0 Bytes';
}

?>