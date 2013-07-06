<?php

class prices {
    public static $use_currencies = array();
    public static $currencies, $currencies_current;
    public static $currency, $tax;

    /*
     * cronjob
     */
    public static function update() {
        $xml = simplexml_load_file('http://www.ecb.int/stats/eurofxref/eurofxref-daily.xml');
        db()->query("CREATE TABLE IF NOT EXISTS currency ( currency CHAR(3) PRIMARY KEY NOT NULL, rate FLOAT NOT NULL )");
        foreach($xml->Cube->Cube->Cube as $c) {
            db()->query("INSERT INTO currency SET currency='EUR', rate='1' ON DUPLICATE KEY UPDATE rate='1'");
            if(in_array($c['currency'], self::$use_currencies)) {
                db()->query("INSERT INTO currency SET currency='".$c['currency']."', rate='".$c['rate']."' ON DUPLICATE KEY UPDATE rate='".$c['rate']."'");
            }
        }
    }

    private static function find_current_currency() {
        switch(@$_SERVER['HTTP_X_GEOIP_COUNTRY_CODE']) {
        default:
            return 'EUR';
        case 'US':
            return 'USD';
        case 'CH':
            return 'CHF';
        }
    }

    /*
     * module initialisation
     */
    public static function init() {
        self::$tax = (@$_POST['tax'] ? $_POST['tax'] : (@$_SESSION['tax'] ? $_SESSION['tax'] : '0'));
        if(@$_SESSION['tax'] != self::$tax) {
            $_SESSION['tax'] = self::$tax;
        }

        self::$currency = (@$_POST['currency'] ? $_POST['currency'] : (@$_SESSION['currency'] ? $_SESSION['currency'] : self::find_current_currency()));
        if(!in_array(self::$currency, self::$use_currencies)) {
            self::$currency = self::find_current_currency();
        }
        if(@$_SESSION['currency'] != self::$currency) {
            $_SESSION['currency'] = self::$currency;
        }

        if(@$_POST['tax'] or @$_POST['currency']) {
            header('Location: '.$_SERVER['REQUEST_URI']);
            die;
        }

        self::$currencies = array();
        $aa = db()->query("SELECT * FROM currency");
        while($a = $aa->fetch_assoc()) {
            if(in_array($a['currency'], self::$use_currencies)) {
                self::$currencies[$a['currency']] = $a['rate'];
            }
        }

        self::$currencies_current = array();
        foreach(self::$currencies as $k=>$v) {
            self::$currencies_current[$k] = round((1/self::$currencies[self::$currency])*$v, 3);
        }

        ?><script type="text/javascript">
        $(function() {
            $('form select').change(function() {this.form.submit();});
            $('form select[name="currency"] option[value="<?=self::$currency?>"]').attr('selected', true);
            $('form select[name="tax"] option[value="<?=self::$tax?>"]').attr('selected', true);
        });
        </script><?php
    }
    public static function calc($price) {
        $price = $price*self::$currencies[self::$currency];
        $price += $price*(self::$tax/100);
        return self::nice($price);
    }
    public static function nice($price) {
        if($price > 20) $round = 1;
        elseif($price > 10) $round = 0.5;
        elseif($price > 5) $round = 0.25;
        elseif($price > 2) $round = 0.10;
        elseif($price > 0.80) $round = 0.05;
        else $round = 0;
        $price = round($price, 2, PHP_ROUND_HALF_DOWN);
        if($round) {
            $price = floor($price/$round)*$round;
        }
        $price = number_format($price, 2, ',', '.');
        return $price;
    }
}

prices::$use_currencies[] = 'EUR';
prices::$use_currencies[] = 'CHF';
prices::$use_currencies[] = 'USD';

?>
