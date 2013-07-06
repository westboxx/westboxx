<!DOCTYPE html>
<html lang="en" class="no-js">
    <head>
        <meta charset="utf-8">
        <meta HTTP-EQUIV="Content-Type" Content="text/html; charset=utf-8">

        <title><?=$main->title?></title>
        <meta name="keywords" content="<?=htmlspecialchars($main->meta_keywords)?>">
        <meta name="description" content="<?=htmlspecialchars($main->meta_description)?>">

        <script src="/static/modernizr-2.6.2.min.js"></script>

        <link rel="stylesheet" type="text/css" href="/static/jquery-ui-1.8.23/css/ui-lightness/jquery-ui-1.8.23.custom.css"></link>
        <script src="/static/jquery-1.8.0.min.js"></script>
        <script src="/static/jquery-ui-1.8.23/js/jquery-ui-1.8.23.custom.min.js"></script>

        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <!--[if lte IE 9]><link rel="stylesheet" href="/static/1140_cssgrid_2/css/ie.css" type="text/css" media="screen" /><![endif]-->
        <link rel="stylesheet" href="/static/1140_cssgrid_2/css/1140.css" type="text/css" media="screen" />
        <script src="/static/1140_cssgrid_2/js/css3-mediaqueries.js"></script>

        <link href="http://fonts.googleapis.com/css?family=Droid+Sans:400,700" rel="stylesheet" type="text/css">
        <link href='http://fonts.googleapis.com/css?family=Oxygen' rel='stylesheet' type='text/css'>

        <link href="/static/style.css" rel="stylesheet" type="text/css">
    </head>
    <body>
        <div class="container">
            <div class="row header">
                <div class="threecol">
                    <div class="header-logo">
                        <h1><a href="/">westboxx</a></h1>
                        <span>You work, we translate</span>
                    </div>
                </div>
                <div class="ninecol last">
                    <div class="header-content">
                        <p>Telefon: <a href="tel:+498807310">+49 88 07 310</a></p>
                        <p>Fax: <a href="fax:+4988078953">+49 88 07 8953</a></p>
                        <p><a href="mailto:mail@westboxx.ch">mail@westboxx.ch</a></p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="twocol menu-side">
                    <?php $menu = load_path('/core/menu/side'); ?>
                    <?=eval('?>'.$menu->code)?>
                    <?=eval('?>'.$menu->content)?>
                </div>

                <div class="tencol last content">
                    <? if(EDIT_MODE): ?>
                    <style type="text/css">
                    input, textarea {
                        width:100%;
                    }
                    textarea {
                        height:500px;
                        font-size:inherit;
                        font-family:inherit;
                        font-weight:inherit;
                    }
                    textarea.code {
                        height:300px;
                        font-family:"Monospace";
                    }
                    table {
                        width:99%;
                    }
                    table th {
                        width:150px;
                    }
                    textarea.input {
                        height:55px;
                    }
                    </style>
                    <script src="/static/tinymce/jscripts/tiny_mce/jquery.tinymce.js"></script>
                    <script type="text/javascript">
                    $(function() {
                        $('textarea.input').keypress(function(e) {
                            if(e.which == 13) return false;
                        });
                        $('textarea.tinymce').tinymce({
                            script_url: '/static/tinymce/jscripts/tiny_mce/tiny_mce.js',
                            theme: 'advanced',
                            plugins: 'autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,advlist',

                            content_css: '/static/style.css',
                            body_class: 'content',

                            apply_source_formatting: true,
                            remove_linebreaks: false,

                            setup: function(ed) {
                                 ed.onBeforeSetContent.add(function(ed, o) {
                                     o.content = o.content.replace(/<\?php((?:.|\s)*?)\?>/gim, "&lt;?php $1 ?&gt;");
                                     o.content = o.content.replace(/<\?=(.*?)\?>/gi, "&lt;?=$1?&gt;");
                                 });
                            }
                        });
                    });
                    </script>

                    <form method="post" action="<?=htmlspecialchars($main->url())?>?edit=1">
                        <input type="hidden" name="save" value="1">
                        <table>
                            <tr>
                                <th>Path</th>
                                <td><input type="text" name="path" value="<?=htmlspecialchars($main->path())?>"></td>
                            </tr>
                            <tr>
                                <th>Title</th>
                                <td><input type="text" name="title" value="<?=htmlspecialchars($main->title)?>"></td>
                            </tr>
                            <tr>
                                <th>Catpion</th>
                                <td><input type="text" name="caption" value="<?=htmlspecialchars($main->caption)?>"></td>
                            </tr>
                            <tr>
                                <th>Meta Keywords</th>
                                <td><textarea class="input" name="meta_keywords"><?=htmlspecialchars($main->meta_keywords)?></textarea></td>
                            </tr>
                            <tr>
                                <th>Meta Description</th>
                                <td><textarea class="input" name="meta_description"><?=htmlspecialchars($main->meta_description)?></textarea></td>
                            </tr>
                            <tr class="gray">
                                <th>Häufige Wortvorkommen</th>
                                <td><?=keywords::render_article($main)?></td>
                            </tr>
                            <tr>
                                <th colspan="2">Content</th>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <textarea class="tinymce" name="content"><?=htmlspecialchars($main->content)?></textarea>
                                </td>
                            </tr>
                            <tr>
                                <th colspan="2">Code</th>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <textarea class="code" name="code"><?=htmlspecialchars($main->code)?></textarea>
                                </td>
                            </tr>
                        </table>
                        <button type="submit">Save</button>
                    </form>

                    <? else: ?>

                    <? if($main->caption): ?>
                    <h1 id="<?=preg_replace('~[^a-z0-9]~i', '_', $main->caption)?>"><?=$main->caption?></h1>
                    <? endif; ?>
                    <?=eval('?>'.$main->code)?>
                    <?=eval('?>'.$main->content)?>

                    <?php if(IS_ADMIN and !$main->read_only): ?>
                    <div style="text-align:right;font-size:10px"><a href="?edit=1">Artikel bearbeiten</a></div>
                    <?php endif; ?>

                    <? endif; ?>
                </div>
            </div>

            <div class="row">
                <?php $footer = load_path('/core/footer'); ?>
                <?=eval('?>'.$footer->code)?>
                <?=eval('?>'.$footer->content)?>
            </div>
        </div>
    </body>
</html>
