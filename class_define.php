<?php
    // ぐるなびAPIで店を検索
    class get_shopinformation{
        //require("config.php");
        public $uri = "http://api.gnavi.co.jp/RestSearchAPI/20150630/";
        public $acckey = "5898ffa0cb2d11bb41251f4e3f8b8fd5";
        public $format = "json";
        public $s_show = 500;
        public $s_range = 3;
        public $input_mode = 2;
        public $s_mode = 2;
        
        public function get_parameter($s_lat, $s_lon){
            
            /*
            $data = array($this->uri => array("?format" => '"' . $this->format ."'", 
                                              "keyid" => "$this->acckey",
                                              "latitude" => "$s_lat",
                                              "longitude" => "$s_lon",
                                              "range" => $this->s_range,
                                              "hit_per_page" => $this->s_show,
                                              "input_coordinates_mode" => $this->input_mode,
                                              "coordinates_mode" => $this->s_mode));
            print http_build_query($data); 
            */              
                                      
            // APIに渡す引数を指定
            $url = sprintf("%s%s%s%s%s%s%s%s%s%s%s%s%s%s%s%s%s", $this->uri, "?format=", $this->format, "&keyid=", $this->acckey, "&latitude=",$s_lat,"&longitude=",$s_lon,"&range=",$this->s_range,"&hit_per_page=",$this->s_show,"&input_coordinates_mode=",$this->input_mode,"&coordinates_mode=",$this->s_mode);
            //print($url);

            // APIからデータを取得
            $json_data = file_get_contents($url, true);
            $d_data = json_decode($json_data);
            //var_dump($d_data);
            
            return $d_data;
        }
    }
?>