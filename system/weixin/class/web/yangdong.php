<?php
/**
 * Created by PhpStorm.
 * User: yanru02
 * Date: 2016/12/12
 * Time: 11:26
 */
?>
<?php
$access_token = get_weixin_token();

if('submit' == $_GP['op']) {
    if(empty($_GP['url'])){
        message("请输入正确!");
    }else{
        $data = $_GP['data'];
        $data = htmlspecialchars_decode($data);
        $data = strtr($data, array(' '=>''));
//        $data=str_replace("\\\"","\"",$data);
//        $data = json_encode($data, true);
        $url = $_GP['url'];
        $url = substr($url, 0, -12);
        $url = $url.$access_token;
        $return_json= http_post($url, $data);
//        if(strstr($return_json,"errcode")){
//            return $this->get_result($return_json);
//        }else{
//            die(json_encode(array('result' => 1, 'return' => $return_json)));
//        }
        die(json_encode($return_json));
    }
}
include page('yangdong');
