<?php

require_once 'config.php';

session_start();

function my_mail($from, $to, $subject, $message) {
    mail(
        $to,
        "=?UTF-8?B?".base64_encode($subject)."?=",
        base64_encode($message),
        "From: $from\r\n".
        "MIME-Version: 1.0\r\n".
        "Content-Type: text/plain; charset=UTF-8\r\n".
        "Content-Transfer-Encoding: base64\r\n");
}






define('LANG', 'de');
define('IS_ADMIN', true or @$_COOKIE['admin'] == 'h98gjoesfirst.lheur.hgrof');
define('EDIT_MODE', IS_ADMIN and !empty($_GET['edit']));





class article {
    public $read_only = false;
    private static $columns = array('path', 'title', 'caption', 'meta_keywords', 'meta_description', 'content', 'code');

    private static function prepare_path($path) {
        $path = preg_replace('~\?.*$~', '', $path);
        $path = preg_replace('~\.html?\??.*\s*~i', '', $path);
        $path = array('') + array_filter(array_map('urldecode', array_map('trim', explode('/', $path))), function($s) {
            return $s;
        });
        return $path;
    }
    public static function load($path, $read_only = false, $try_fs = true, $try_db = true) {
        if(!is_array($path)) {
            $path = self::prepare_path($path);
        }
        $path = implode('/', $path);
        $path = preg_replace('~\?.*$~', '', $path);
        $path = preg_replace('~\.html?$~', '', $path);
        if(!$path) {
            $path = '/';
        }

        if($try_fs) {
            if(file_exists('views'.$path.'.php')) {
                ob_start();
                ob_flush();
                include 'views/'.$path.'.php';
                $content = ob_get_contents();
                ob_clean();
                $a = new self(array(
                    'path' => $path,
                    'title' => $title,
                    'caption' => $caption,
                    'meta_keywords' => $meta_keywords,
                    'meta_description' => $meta_description,
                    'content' => $content,
                    'code' => ''), true);
                if(EDIT_MODE) {
                    header('Location: '.$a->url());
                }
                return $a;
            }
            elseif(is_dir('views'.$path)) {
                $a = self::load($path.'/index', $read_only, true, false);
                if($a) {
                    return $a;
                }
            }
        }

        if($try_db) {
            $a = db()->query("SELECT * FROM article WHERE path='".es($path)."' LIMIT 1")->fetch_object();
            if($a) {
                return new self($a, $read_only);
            }
        }
    }

    public function article($a, $read_only = false) {
        $this->read_only = $read_only;

        if(is_array($a)) {
            $b = new dummy();
            foreach($a as $k=>$v) {
                $b->$k = $v;
            }
            $a = $b;
        }

        if(!is_array($a->path)) {
            $a->path = self::prepare_path($a->path);
        }
        if(!$a->path or @$a->path[0] == 'index.php') {
            $a->path = array('');
        }

        $this->path = $a->path;
        $this->title = $a->title;
        $this->caption = $a->caption;
        $this->meta_keywords = $a->meta_keywords;
        $this->meta_description = $a->meta_description;
        $this->content = $a->content;
        $this->code = $a->code;
    }

    public function toArray() {
        $a = array();
        foreach(self::$columns as $col) {
            $a[$col] = $this->$col;
            if($col == 'path') {
                $a[$col] = $this->path();
            }
        }
        return $a;
    }

    public function backup() {
        if($this->read_only) return;
        db()->query("INSERT INTO article_backup SET ".implode(', ', hashed_array_to_sql($this->toArray())));
    }
    public function save($old_version) {
        if($this->read_only) return;
        db()->query("REPLACE INTO article SET ".implode(', ', hashed_array_to_sql($this->toArray())));
        if(db()->affected_rows and $old_version and $old_version->path() != $this->path()) {
            db()->query("DELETE FROM article WHERE path='".es($old_version->path())."'");
        }
    }

    public function path() {
        if(count($this->path) == 1) return '/';
        return implode('/', $this->path);
    }
    public function url() {
        if(count($this->path) == 1) return '/';
        else return $this->path().'.html';
    }
}


function load_path($path) {
    $article = article::load($path);
    if(!$article) {
        if($path == '/core/error/404') {
            die('Internal Server Error');
        }
        if(EDIT_MODE) {
            $article = new article(array(
                'path' => $path,
                'title' => 'Neue Seite',
                'caption' => '',
                'meta_keywords' => '',
                'meta_description' => '',
                'content' => 'Neue Seite...',
                'code' => ''), false);
        }
        elseif(empty($_POST['save'])) {
            return load_path('/core/error/404');
        }
    }

    if(EDIT_MODE and !$article->read_only and !empty($_POST['save'])) {
        if($article) {
            $article->backup();
        }
        $_POST['content'] = preg_replace('~(<p>)?\s*&lt;\?php(.*?)\?&gt;\s*(</p>)?~si', '<?php\2?>', $_POST['content']);
        $_POST['content'] = preg_replace('~&lt;\?=(.*?)\?&gt;~si', '<?=\1?>', $_POST['content']);
        $new = new article($_POST, false);
        $new->save($article);
        header('Location: '.$new->url());
        die;
    }

    return $article;
}

$main = load_path($_SERVER['REQUEST_URI']);

include 'html/'.LANG.'.php';

?>
