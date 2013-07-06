<?php

$title = 'Admin Logout';
$caption = 'Admin Logout';
$meta_keywords = '';
$meta_description = '';

if(!IS_ADMIN) {
    header('Location: /');
    die;
}

if(@$_POST['logout']) {
    setcookie(
        'admin',
        'deleted',
        -24*60*60,
        '/',
        '.'.$_SERVER['HTTP_HOST'],
        false,
        true);
    header('Location: /admin.html');
    die;
}

?>
<form method="post">
    <input type="hidden" name="logout" value="1"/>
    <button type="submit">Logout</button>
</form>
