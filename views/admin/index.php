<?php

$title = 'Admin';
$caption = 'Admin';
$meta_keywords = '';
$meta_description = '';

if(!IS_ADMIN) {
    header('Location: /admin/login.html');
    die;
}

?>
<ul class="ul-no-margin">
    <li><a href="/admin/logout.html">Logout</a></li>
    <li><a href="/admin/articles.html">Artikel</a></li>
</ul>

