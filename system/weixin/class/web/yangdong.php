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
    if(empty($_GP['url'])||empty($_GP['data'])){
        message("请输入正确!");
    }else{
        $url = $_GP['url'];
        $data = $_GP['data'];
        $return_json= http_post($url, $data);
        if(strstr($return_json,"errcode")){
            return $this->menuResponseParse($return_json);
        }else{
            die(json_encode(array('result' => 1, 'return' => $return_json)));
        }
    }
}
include page('yangdong');
