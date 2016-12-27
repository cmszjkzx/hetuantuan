<?php
/**
 * Created by PhpStorm.
 * User: yanru02
 * Date: 2016/12/2
 * Time: 14:37
 */
?>
<?php
error_reporting(E_ALL);
date_default_timezone_set('Asia/ShangHai');

require_once WEB_ROOT.'/includes/lib/phpexcel/PHPExcel.php';
require_once WEB_ROOT.'/includes/lib/phpexcel/PHPExcel/IOFactory.php';

//$file_path = WEB_ROOT.'/attachment/'.$data['expressorder'];
$file_path = WEB_ROOT.'/attachment/'.$upload['path'];
if(!file_exists($file_path)){
    message('没有找到上传文件！');
}
$extension = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
if ($extension =='xlsx') {
    $reader = new PHPExcel_Reader_Excel2007();
    $PHPExcel = $reader ->load($file_path);
} else if ($extension =='xls') {
    $reader = new PHPExcel_Reader_Excel5();
    $PHPExcel = $reader ->load($file_path);
} else if ($extension=='csv') {
    $reader = new PHPExcel_Reader_CSV();
    //默认输入字符集
    $reader->setInputEncoding('UTF-8');
    //默认的分隔符
    $reader->setDelimiter(',');
    //载入文件
    $PHPExcel = $reader->load($file_path);
}

//$reader = PHPExcel_IOFactory::createReader("Excel5");
//$PHPExcel = $reader->load($file_path);

$sheet = $PHPExcel->getSheet(0);//读取第一个工作表
$allRow = $sheet->getHighestRow();//取得总行数
$allColum = $sheet->getHighestColumn();//取得总列数
$allColum = PHPExcel_Cell::columnIndexFromString($allColum);//字母列转换为数字列 如:AA变为27

//读取常用的物流公司表，用于订单物流跟踪------
$express_file_path = WEB_ROOT."/express/wuliu.xlsx";
if(!file_exists($express_file_path)){
    message('请准备常用物流公司文件!');
}
$express_extension = strtolower(pathinfo($express_file_path, PATHINFO_EXTENSION));
if ($express_extension =='xlsx') {
    $express_reader = new PHPExcel_Reader_Excel2007();
    $express_PHPExcel = $express_reader ->load($express_file_path);
} else if ($express_extension =='xls') {
    $express_reader = new PHPExcel_Reader_Excel5();
    $express_PHPExcel = $express_reader ->load($express_file_path);
} else if ($express_extension=='csv') {
    $express_reader = new PHPExcel_Reader_CSV();
    //默认输入字符集
    $express_reader->setInputEncoding('UTF-8');
    //默认的分隔符
    $express_reader->setDelimiter(',');
    //载入文件
    $express_PHPExcel = $express_reader->load($express_file_path);
}
$express_sheet = $express_PHPExcel->getSheet(0);//读取第一个工作表
$express_allRow = $express_sheet->getHighestRow();//取得总行数
$express_allColum = $express_sheet->getHighestColumn();//取得总列数
$express_allColum = PHPExcel_Cell::columnIndexFromString($express_allColum);//字母列转换为数字列 如:AA变为27
//------


/* 循环读取每个单元格的数据 */
for ($row = 2; $row <= $allRow; $row++){//行数是以第1行开始
    $order_id = $sheet->getCellByColumnAndRow(0, $row)->getValue();
    for($i = 0; $i < count($list); $i++){
        if($order_id == $list[$i]['ordersn']){
//            $order_express_name = "";
//            $order_express_id = "";
            $title = $sheet->getCellByColumnAndRow(2, $row)->getValue();
            $option_name = $sheet->getCellByColumnAndRow(3, $row)->getValue();
            $express_name = $sheet->getCellByColumnAndRow(12, $row)->getValue();
            $express_id = $sheet->getCellByColumnAndRow(13, $row)->getValue();
            if($express_name instanceof PHPExcel_RichText)
                $express_name = $express_name->__toString();
            if(!empty($express_name) && !empty($express_id)){
                for($express_row = 1; $express_row <= $express_allRow; $express_row++){
                    $express_string = $express_sheet->getCellByColumnAndRow(1, $express_row)->getValue();
                    if($express_string instanceof PHPExcel_RichText)
                        $express_string = $express_string->__toString();
                    if(stristr($express_string, $express_name)){
                        $express = $express_sheet->getCellByColumnAndRow(0, $express_row)->getValue();
                        break;
                    }
                }
                for($j = 0; $j < count($list[$i]['ordergoods']); $j++){
                    if(0 == strcmp($title, $list[$i]['ordergoods'][$j]['title']) && 0 == strcmp($option_name, $list[$i]['ordergoods'][$j]['optionname'])){
//                        if(empty($list[$i]['ordergoods'][$j]['optionname']))
//                            $tmp = 'inserttemp';
//                        else
//                            $tmp = $list[$i]['ordergoods'][$j]['optionname'];
                        $tmp = $list[$i]['ordergoods'][$j]['goodsid']."@".$list[$i]['ordergoods'][$j]['optionid'];
                        if(stristr($list[$i]['expresscom'], $tmp)==false){
                            $list[$i]['expresscom'] =  $list[$i]['expresscom'].$tmp."_".$express_name.";";
                            $list[$i]['expresssn'] = $list[$i]['expresssn'].$tmp."_".$express_id.";";
                            $list[$i]['express'] = $list[$i]['express'].$tmp."_".$express.";";
                            $order_update = array(
                                "status" => 3,
                                "expresscom" => $list[$i]['expresscom'],
                                "expresssn" => $list[$i]['expresssn'],
                                "express" => $list[$i]['express']
                            );
                            mysqld_update("shop_order", $order_update, array("id" => $list[$i]['id']));
                            $notice = array(
                                "touser" => $list[$i]['weixin_openid'],
                                "template_id" => "A-pOebjfRNtuzGSqEVnGwgtjk1Hqt3G9GOpavMVHzb0",
                            );
                            $first = array(
                                "value" => "您的商品已发货！",
                                "color" => "#173177"
                            );
                            $keyword1 = array(
                                "value" => $list[$i]['ordergoods'][$j]['title'],
                                "color" => "#173177"
                            );
                            $keyword2 = array(
                                "value" => $list[$i]['ordersn'],
                                "color" => "#173177"
                            );
                            $keyword3 = array(
                                "value" => "中国移动和团团",
                                "color" => "#173177"
                            );
                            $remark = array(
                                "value" => "满意您再来哟！",
                                "color" => "#173177"
                            );
                            $noticeDat = array(
                                "first" => $first,
                                "keyword1" => $keyword1,
                                "keyword2" => $keyword2,
                                "keyword3" => $keyword3,
                                "remark" => $remark
                            );
                            $notice["data"] = $noticeDat;
                            $dat = json_encode($notice);
                            $dat = urldecode($dat);
                            $token = get_weixin_token();
                            $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token={$token}";
                            $content = http_post($url, $dat);
                        }
                    }
                }
            }
        }
    }
}
message("导入成功，已更新订单!",create_url('site',array('name' => 'shop','do' => 'order', 'status'=>99)),"success");
