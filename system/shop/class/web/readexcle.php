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

$file_path = WEB_ROOT."/attachment/".$data['expressorder'];
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
/* 循环读取每个单元格的数据 */
for ($row = 2; $row <= $allRow; $row++){//行数是以第1行开始
    $order_id = $sheet->getCellByColumnAndRow(0, $row)->getValue();
    foreach ($order_list as $order){
        $order_express_name = "";
        $order_express_id = "";
        if($order_id == $order['ordersn']){
            $title = $sheet->getCellByColumnAndRow(2, $row)->getValue();
            $option_name = $sheet->getCellByColumnAndRow(3, $row)->getValue();
            $express_name = $sheet->getCellByColumnAndRow(11, $row)->getValue();
            $express_id = $sheet->getCellByColumnAndRow(12, $row)->getValue();
            foreach ($order['ordergoods'] as $goods){
                if(0 == strcmp($title, $goods['title']) && 0 == strcmp($option_name, $goods['optionname'])){
                    $order['expresscom'] = $order['expresscom']."_".$express_name;
                    $order['expresssn'] = $order['expresssn']."_".$express_id;
                }
            }
        }
    }
}
$count = 0;
exit;
