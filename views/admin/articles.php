<?php

$title = 'Admin Seiten';
$caption = 'Admin Seiten';
$meta_keywords = '';
$meta_description = '';

if(!IS_ADMIN) {
    header('Location: /');
    die;
}

$aa = db()->query("SELECT * FROM article ORDER BY path");
?>

<ul>
    <?php while($a = $aa->fetch_object()):
    $a = new article($a, false); ?>
    <li>
        <a href="<?=htmlspecialchars($a->url())?>"><?=htmlspecialchars($a->url())?></a>
        <ul class="ul-no-margin">
            <li title="Title"><?=htmlspecialchars($a->title)?></li>
            <li title="Caption"><?=htmlspecialchars($a->caption)?></li>
            <li title="Meta keywords"><?=htmlspecialchars($a->meta_keywords)?><br></li>
            <li title="Meta description"><?=htmlspecialchars($a->meta_description)?></li>
            <li title="Häufige Wortvorkommen" class="gray"><?=keywords::render_article($a)?></li>
        </ul>
    </li>
    <?php endwhile; ?>
</ul>
