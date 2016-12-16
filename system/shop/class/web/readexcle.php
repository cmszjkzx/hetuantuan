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
    for($i = 0; $i < count($order_list); $i++){
        if($order_id == $order_list[$i]['ordersn']){
            $order_express_name = "";
            $order_express_id = "";
            $title = $sheet->getCellByColumnAndRow(2, $row)->getValue();
            $option_name = $sheet->getCellByColumnAndRow(3, $row)->getValue();
            $express_name = $sheet->getCellByColumnAndRow(11, $row)->getValue();
            $express_id = $sheet->getCellByColumnAndRow(12, $row)->getValue();
            if($express_name instanceof PHPExcel_RichText)
                $express_name = $express_name->__toString();
            if(!empty($express_name) && !empty($express_id)){
                $express;
                for($express_row = 1; $express_row <= $express_allRow; $express_row++){
                    $express_string = $express_sheet->getCellByColumnAndRow(1, $express_row)->getValue();
                    if($express_string instanceof PHPExcel_RichText)
                        $express_string = $express_string->__toString();
                    if(stristr($express_string, $express_name)){
                        $express = $express_sheet->getCellByColumnAndRow(0, $express_row)->getValue();
                        break;
                    }
                }
                for($j = 0; $j < count($order_list[$i]['ordergoods']); $j++){
                    if(0 == strcmp($title, $order_list[$i]['ordergoods'][$j]['title']) && 0 == strcmp($option_name, $order_list[$i]['ordergoods'][$j]['optionname'])){
                        if(empty($order_list[$i]['ordergoods'][$j]['optionname']))
                            $tmp = 'inserttemp';
                        else
                            $tmp = $order_list[$i]['ordergoods'][$j]['optionname'];
                        $order_list[$i]['expresscom'] =  $order_list[$i]['expresscom'].$tmp."_".$express_name.";";
                        $order_list[$i]['expresssn'] = $order_list[$i]['expresssn'].$tmp."_".$express_id.";";
                        $order_list[$i]['express'] = $order_list[$i]['express'].$tmp."_".$express.";";
                    }
                }
                $order_update = array(
                    "status" => 3,
                    "expresscom" => $order_list[$i]['expresscom'],
                    "expresssn" => $order_list[$i]['expresssn'],
                    "express" => $order_list[$i]['express']
                );
                mysqld_update("shop_order", $order_update, array("id" => $order_list[$i]['id']));
            }
        }
    }
}
message("导入成功，已更新订单!",create_url('site',array('name' => 'shop','do' => 'order', 'status'=>99)),"success");
