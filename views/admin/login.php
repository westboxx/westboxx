<?php

$title = 'Admin Login';
$caption = 'Admin Login';
$meta_keywords = '';
$meta_description = '';

if(IS_ADMIN) {
    header('Location: /');
    die;
}

/* TODO: remove instant login */
if(true or @$_POST['user'] == 'foo' and @$_POST['pass'] == 'bar') {
    setcookie(
        'admin',
        'h98gjoesfirst.lheur.hgrof',
        time()+60*60*24*365*5,
        '/',
        '.'.$_SERVER['HTTP_HOST'],
        false,
        true);
    header('Location: /admin.html');
    die;
}

?>
<form method="post">
    User: <input type="text" name="user"/><br>
    Pass: <input type="password" name="pass"/><br>
    <button type="submit">Login</button>
</form>
