<?php
    // 選択した駅の駅コードを取得
    $get_sName = $_POST["s1"];

    // 駅データのAPIに駅コードで検索をかける
    $s_url = "http://www.ekidata.jp/api/s/" . $get_sName . ".xml";

    // 選択した駅の情報を取得
    $s_json_data = file_get_contents($s_url, true);
    //var_dump($s_json_data);

    // XML形式で取得したので、読み取れる形に変換
    $s_data = new SimpleXMLElement($s_json_data);
    //var_dump($s_data);

    // ライン名、駅名、緯度経度の取得
    $l_name = $s_data->station[0]->line_name;
    $st_name = $s_data->station[0]->station_name;
    $s_lon = $s_data->station[0]->lon;
    $s_lat = $s_data->station[0]->lat;

?>

<html lang="ja">
    <head>
        <style>
            #container{
                margin-top: 20px;
            }
            #h_label{
                margin-left: 20px;
            }
            #s_info{
                margin-top: 20px;
                margin-bottom: 20px;
                margin-left: 20px;
            }
        </style>
        
        <script src="http://maps.google.com/maps/api/js?sensor=false"></script>
        <script>
            var map = null;
            var marker = null;
            window.addEventListener("load", function(){
                // ライン名、駅名の情報取得
                var line_name = "<?php print $l_name; ?>";
                var station_name = "<?php print $st_name; ?>";

                // ライン名、駅名の情報をHTML上に表示
                document.getElementById("line_name").innerHTML = line_name;
                document.getElementById("station_name").innerHTML = station_name;

                // 選択した駅の緯度経度の情報取得
                var lat = <?php print $s_lat ?>;
                var lon = <?php print $s_lon ?>;

                // 選択した駅周辺情報をマップに表示
                var map = new google.maps.Map(
                    document.getElementById("myGoogleMap"),{
                        zoom: 16,
                        center: new google.maps.LatLng(lat, lon),
                        mapTypeId: google.maps.MapTypeId.ROADMAP
                    }
                );

                if(!navigator.geolocation){
                    return;
                }

                // クラスからぐるなびの情報を取得する。
                <?php 
                    require("./class_define.php");
                    $get_shop_param = new get_shopinformation();
                    $result = $get_shop_param->get_parameter($s_lat, $s_lon);
                ?>
                
                // クラスから取得した値をjson encodeする。
                var shop_data = <?php print json_encode($result, true); ?>;              

                if(shop_data.total_hit_count > 500){
                    var shop_count = 500;
                }
                else{
                    var shop_count = shop_data.total_hit_count;
                }
                
                
                // 選択した駅周辺のお店をMakerにて表示
                for(var i=0; i < shop_count; i++){                
                //for(var i=0; i < shop_data.total_hit_count; i++){
                    var sp_name = shop_data.rest[i].name;
                    var sp_lat = shop_data.rest[i].latitude;
                    var sp_lon = shop_data.rest[i].longitude;
                    var sp_url = shop_data.rest[i].url;
                    var sp_image_url = shop_data.rest[i].image_url.shop_image1;
                    var sp_address = shop_data.rest[i].address;

                    var currentPosition = new google.maps.LatLng(sp_lat, sp_lon);

                    // 新規にマーカーを表示する
                    marker = new google.maps.Marker({
                        position: currentPosition,
                        title: sp_name,
                        map: map
                    });

                    // クリックしたら指定したurlに遷移するイベント
                    google.maps.event.addListener(marker, 'click', (function(url){
                            return function(){location.href = url;};
                    })(sp_url));
                }

            });
        </script>

    </head>
    <body>
        <div id="container">
            <h3 id="h_label">選択した駅周辺のお店を検索します</h3>
            <table id="s_info">
                <tr>
                    <td width="100">
                        ライン名：
                    </td>
                    <td>
                        <div id="line_name"></div>
                    </td>
                </tr>
                <tr>
                    <td width="100">
                        駅名：
                    </td>
                    <td>
                        <div id="station_name"></div>
                    </td>
                </tr>
            </table>
                                    
            <div id="myGoogleMap" style="width:100%; height:90%; border: 1px solid black;">
            </div>
        </div>
    </body>
</head>