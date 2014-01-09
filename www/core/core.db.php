<?php
// функции работы с базой (@todo: синглтон класс)
require_once('config/config.php');

function ConnectDB()
{
    global $CONFIG;
    $hostname = ($_SERVER['REMOTE_ADDR']==="127.0.0.1") ? $CONFIG['host_local']     : $CONFIG['host_remote'];
    $username = ($_SERVER['REMOTE_ADDR']==="127.0.0.1") ? $CONFIG['username_local'] : $CONFIG['username_remote'];
    $password = ($_SERVER['REMOTE_ADDR']==="127.0.0.1") ? $CONFIG['password_local'] : $CONFIG['password_remote'];
    $database = ($_SERVER['REMOTE_ADDR']==="127.0.0.1") ? $CONFIG['database_local'] : $CONFIG['database_remote'];
    $link = mysql_connect($hostname,$username,$password);
    $CONFIG['flag_dbconnected'] = true;
    mysql_select_db($database, $link) or die("Could not select db: " . mysql_error());
    mysql_query("SET NAMES utf8", $link);
    return $link;
}

function CloseDB($link) // useless
{
    global $CONFIG;
    mysql_close($link) or Die("Не удается закрыть соединение с базой данных.");
    $CONFIG['flag_dbconnected'] = false;
}

function isConnectedDB()
{
    global $CONFIG;
    return $CONFIG['flag_dbconnected'];
}

function MakeInsert($arr,$table,$where="")
{
    $str = "INSERT INTO $table ";

    $keys = "(";
    $vals = "(";
    foreach ($arr as $key => $val) {
        $keys .= $key . ",";
        $vals .= "'".$val."',";
    }
    $str .= trim($keys,",") . ") VALUES " . trim($vals,",") . ") ".$where;
    return $str;
}

function MakeUpdate($arr,$table,$where="")
{
    $str = "UPDATE $table SET ";
    foreach ($arr as $key=>$val)
    {
        $str.= $key."='".$val."', ";
    };
    $str = substr($str,0,(strlen($str)-2)); // обрезаем последнюю ","
    $str.= " ".$where;
    return $str;
}

function DBLoginCheck($login, $password)
{
    global $CONFIG;
    // возвращает массив с полями "error" и "message"
    $link = ConnectDB(); // @todo: мы можем быть уже соединены с базой!!!!!!!!!!!! поэтому не закрываем его в конце!
    // логин мы передали точно совершенно, мы это проверили в скрипте, а пароль может быть и пуст
    // а) логин не существует
    // б) логин существует, пароль неверен
    // в) логин существует, пароль верен
    $userlogin = mysql_real_escape_string(mb_strtolower($login));
    $q_login = "SELECT `md5password`,`permissions`,`id` FROM users WHERE login = '$userlogin'";
    if (!$r_login = mysql_query($q_login)) { /* error catch */ }
    $err = mysql_errno($link);

    if (mysql_num_rows($r_login)==1) {
        // логин существует
        $user = mysql_fetch_assoc($r_login);
        if ($password == $user['md5password']) {
            // пароль верен
            $return = array(
                'error' => 0,
                'message' => 'User credentials correct! ',
                'id' => $user['id'],
                'permissions' => $user['permissions']
            );
        } else {
            // пароль неверен
            $return = array(
                'error' => 1,
                'message' => 'Пароль не указан или неверен! Проверьте раскладку клавиатуры! '
            );
        }
    } else {
        // логин не существует
        $return = array(
            'error' => 2,
            'message' => 'Пользователь с логином '.$login.' в системе не обнаружен! '
        );
    }
    return $return;
}

?>