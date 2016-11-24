<?php
/*************************
 * Proje: Empatisoft @ 2016
 * Developer: Onur KAYA
 * Telefon: 0537 493 10 20
 * E-posta: empatisoft@gmail.com
 * Web: www.empatisoft.com
 * PHP Sürümü: 7.0.9
 * MySQL Sürümü: 5.0.12
 * Oluşturma Tarihi: 24.11.2016 14:12
 */
class GoogleCookieParser {
    public $utm_type = null;
    public $utm_userID = null;
    public $utm_source = null;
    public $utm_medium = null;
    public $utm_campaign = null;
    public $utm_term = null;
    public $utm_content = null;

    public function parse_google_cookie($__utmz){
        $data = array();
        foreach((array)preg_split('~([.|])(?=ut)~', $__utmz) as $pair){
            if (isset($pair))
            {
                //list($key, $value) = explode('=', $pair);
                list($key,$value) = (strstr($pair, '=') ? explode('=', $pair) : array($pair, ''));
                $data[$key] = $value;
            }

        }
        return $data;
    }

    public function parse_google_cookie_ga($str){
        $data = array();
        foreach((array)preg_split('~([.|])~', $str) as $pair){
            if (isset($pair))
            {
                //list($key, $value) = explode('=', $pair);
                list($key) = (strstr($pair, '=') ? explode('=', $pair) : array($pair, ''));
                $data[] = $key;
            }

        }
        return $data;
    }
    public function getUserSource()
    {
        /**
         * Universal Analytics: GA1.A.B.C
         * GA1: Sürüm numarası
         * A: Alan adı seviyesi
         * B: Rastgele oluşuturlmuş olan ve tekil (unique) kimlik bilgisi
         * C: Zaman damasıdır. Ziyaretçinin ilk geliş tarihini ifade eder.
         * B.C: Ziyaretçi Kimliği (clientID)
         * */

        /**
         * Klasik Analytics: A.B.C.D.utmcsr=XXXXXXXXX|utmccn=XXXXXXXXX|utmcmd=XXXXXXXXX
         * A: Ziyaretçi ile site arasındaki anahtar
         * B: Zaman damasıdır. Ziyaretçinin ilk geliş tarihini ifade eder.
         * C: Ziyaretçinin oturum sayısı. Bu örnekte 19. kez geldiği görülüyor.
         * D: Kampanya sayısını ifade eder. Bu örnekte ziyaretçi 5 farklı kampanyamız üzerinden gelmiştir.
         * utmcsr: Ziyaretçinin geldiği kaynağı gösterir. (utm_source değerini taşır)
         * utmccn: Kampanya adını gösterir. (utm_campaign değerini taşır)
         * utmcmd: Aracı gösterir. (utm_medium değerini taşır) Organik, Paid vs.
         * utmctr: Anahtar kelime (utm_term değerini taşır)
         * utmcct: Kampanya içeriğini ifade eder. (utm_content değerini taşır)
         *
         */

        $__utmz = get_cookie("__utmz");
        $_ga = get_cookie("_ga");

        // Google Universal Analytics
        if(!is_null($_ga))
        {
            $this->utm_type = "_ga";

            $data = $this->parse_google_cookie_ga($_ga);
            $this->utm_userID = $data[2] . '.' . $data[3];
        }
        // Google Classic Analytics
        else if (!is_null($__utmz))
        {
            $this->utm_type = "__utmz";

            $data = $this->parse_google_cookie($__utmz);
            $this->utm_source = isset($data["utmcsr"]) ? $data["utmcsr"] : null;;
            $this->utm_campaign = isset($data["utmccn"]) ? $data["utmccn"] : null;
            $this->utm_content = isset($data["utmcct"]) ? $data["utmcct"] : null;
            $this->utm_term = isset($data["utmctr"]) ? $data["utmctr"] : null;;
            $this->utm_medium = isset($data["utmcmd"]) ? $data["utmcmd"] : null;;
        }
    }
}