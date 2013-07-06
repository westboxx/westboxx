<?php

class keywords {
    public $data = array();
    public static $badwords = array();

    public function feed($data) {
        $new = '';
        $data = str_replace("\r", " ", $data);
        $data = str_replace("\n", " ", $data);
        $data = preg_replace('~<script\s.*?</script>~i', ' ', $data);
        $data = preg_replace('~<style\s.*?</style>~i', ' ', $data);
        if(preg_match_all('~<[^>]*\stitle=["\']([^"\']*)["\'][^>]*>~i', $data, $out)) {
            for($i = 0, $num = count($out[1]); $i < $num; $i++) {
                $data .= ' '.$out[1][$i];
            }
        }
        $data = preg_replace('~<[^>]*>~', ' ', $data);
        $data = html_entity_decode($data);
        $data = preg_replace('~[^\s\wöäüÖÄÜ]+~i', ' ', $data);
        $data = array_filter(explode(' ', $data));
        foreach($data as $word) {
            if(mb_strlen($word) <=3 ) {
                continue;
            }
            $w = mb_strtolower($word);
            if(in_array($w, self::$badwords)) {
                continue;
            }
            if(!isset($this->data[$w])) {
                $this->data[$w] = array(
                    'name' => $word,
                    'count' => 1);
            }
            else {
                $this->data[$w]['count']++;
            }
        }
    }
    public function feed_article($a) {
        $this->feed($a->caption);
        $this->feed($a->code);
        $this->feed($a->content);
    }
    public function render() {
        return implode(', ', array_map(function($a) {
            return '<span style="font-size:'.(75 + $a['count']*7).'%" title="'.$a['count'].'">'.htmlspecialchars($a['name']).'</span>';
        }, $this->get()));
    }

    public static function render_article($a) {
        $s = new self();
        $s->feed_article($a);
        return $s->render();
    }


    public function get() {
        ksort($this->data);
        uasort($this->data, function($a, $b) {
            return $b['count'] - $a['count'];
        });
        return array_filter($this->data, function($a) { return $a['count'] > 1; });
    }
}

keywords::$badwords = array_merge(keywords::$badwords, explode(',', mb_strtolower('aber,als,am,an,auch,auf,aus,bei,bin,bis,ist,da,dadurch,daher,darum,das,daß,dass,dein,deine,dem,den,der,des,dessen,deshalb,die,dies,dieser,dieses,doch,dort,du,durch,ein,eine,einem,einen,einer,eines,er,es,euer,eure,für,hatte,hatten,hattest,hattet,hier,hinter,ich,ihr,ihre,im,in,ist,ja,jede,jedem,jeden,jeder,jedes,jener,jenes,jetzt,kann,kannst,können,könnt,machen,mein,meine,mit,muß,mußt,musst,müssen,müßt,nach,nachdem,nein,ncht,nun,oder,seid,sein,seine,sich,sie,sind,soll,sollen,sollst,sollt,sonst,soweit,sowie,und,unserunsere,unter,vom,von,vor,wann,warum,was,weiter,weitere,wenn,wer,werde,werden,werdet,weshalb,wie,wieder,wieso,wir,wird,wirst,wo,woher,wohin,zu,zum,zur,über')));
keywords::$badwords = array_merge(keywords::$badwords, explode(',', mb_strtolower('ihnen,unsere,seit,per,uns,alle,ihm,ihren,unseren,unserer,westboxx,ihrer,damit,unser')));
keywords::$badwords = array_merge(keywords::$badwords, explode(',', mb_strtolower('nicht,erteilt,mehr,zugänglich,streng,löschen,mwst,_post,innerhalb,zurückrufen,berechnet,zählen,anderem,ohne,gelten,keine,anderes,ausgeschlossen,ausschliesslich,geben,zukommen,lesen,senden,kleine,erfahrene,garantieren,grosse,lassen,besten,bitten,nichts,dritte,angemessene,ausdrücklich,vereinbart,beglaubigte,beschäftigen,sondern,gute,muss,verstehen,bitte')));

?>
