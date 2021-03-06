<?php
error_reporting(E_ALL);
ini_set('display_errors', FALSE);
ini_set('display_startup_errors', FALSE);
if (PHP_SAPI == 'cli')
    die('This example should only be run from a Web Browser');

require_once WEB_ROOT.'/includes/lib/phpexcel/PHPExcel.php';

// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

// Set document properties
//$objPHPExcel->getProperties()->setCreator("hetuantuan")
//    ->setLastModifiedBy("hetuantuan")
//    ->setTitle("Office 2007 XLSX Test Document")
//    ->setSubject("Office 2007 XLSX Test Document")
//    ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
//    ->setKeywords("office 2007 openxml php")
//    ->setCategory("report file");
$objPHPExcel->getProperties()->setCreator("hetuantuan")
    ->setLastModifiedBy("hetuantuan")
    ->setTitle("Office 2003 XLS Test Document")
    ->setSubject("Office 2003 XLS Test Document")
    ->setDescription("Test document for Office 2003 XLS, generated using PHP classes.")
    ->setKeywords("office 2003 openxml php")
    ->setCategory("report file");

if($report=='orderreport')
{
    // Add some data
    $objPHPExcel->setActiveSheetIndex(0)
//        ->setCellValue('A1', '订单号')
//        ->setCellValue('B1', '下单时间')
//        ->setCellValue('C1', '订单总额')
//        ->setCellValue('D1', '运费')
//        ->setCellValue('E1', '付款方式')
//        ->setCellValue('F1', '配送方式')
//        ->setCellValue('G1', '收货人')
//        ->setCellValue('H1', '收货地址')
//        ->setCellValue('I1', '收货电话')
//        ->setCellValue('J1', '分类名称')
//        ->setCellValue('K1', '产品名称')
//        ->setCellValue('L1', '产品编号')
//        ->setCellValue('M1', '规格')
//        ->setCellValue('N1', '商品单价')
//        ->setCellValue('O1', '购买数量')
//        ->setCellValue('P1', '商品总价');
        ->setCellValue('A1', '订单序号')
        ->setCellValue('B1', '品牌')
        ->setCellValue('C1', '产品名称')
        ->setCellValue('D1', '规格型号')
        ->setCellValue('E1', '数量')
        ->setCellValue('F1', '成本价')
        ->setCellValue('G1', '成本总价')
        //->setCellValue('H1', '含税总价')//不需要
        //->setCellValue('I1', '商品进价')//不需要
        ->setCellValue('H1', '收货人')
        ->setCellValue('I1', '电话')
        ->setCellValue('J1', '收货地址')
        ->setCellValue('K1', '物流公司')
        ->setCellValue('L1', '物流单号')
        ->setCellValue('M1', '运费')//新增
        ->setCellValue('N1', '签收情况')
        ->setCellValue('O1', '订单完成状况')
        ->setCellValue('P1', '备注');
    //$objPHPExcel->getActiveSheet()->getStyle('A')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
    $objPHPExcel->getActiveSheet()->getStyle('K')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
    $objPHPExcel->getActiveSheet()->getStyle('L')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
    $i=2;
    $index=0;
    $countmoney=0;

    foreach($list as $item){

        $countmoney=$countmoney+$item['price'];
        $priceother='';

        foreach($item['ordergoods'] as $itemgoods){

            $index++;
            if(!empty($item['dispatchprice'])&&$item['dispatchprice']>0)
            {
                $priceother=$item['dispatchprice'];
            }else
            {
                $priceother="0";
            }
            $itemgoods['goodstotal'] = intval($itemgoods['price'])*intval($itemgoods['total']);
            $objPHPExcel->setActiveSheetIndex(0)
//                ->setCellValue('A'.$i, $item['ordersn'])
//                ->setCellValue('B'.$i, date('Y-m-d  H:i:s',$item['createtime']))
//                ->setCellValue('C'.$i, $item['price'])
//                ->setCellValue('D'.$i, $priceother)
//                ->setCellValue('E'.$i, $item['paytypename'])
//                ->setCellValue('F'.$i,$dispatchdata[$item['dispatch']]['dispatchname'])
//                ->setCellValue('G'.$i, $item['address_realname'])
//                ->setCellValue('H'.$i, $item['address_province'].$item['address_city'].$item['address_area'].$item['address_address'])
//                ->setCellValue('I'.$i, $item['address_mobile'])
//                ->setCellValue('J'.$i, $itemgoods['categoryname'])
//                ->setCellValue('K'.$i, $itemgoods['title'])
//                ->setCellValue('L'.$i, $itemgoods['goodssn'])
//                ->setCellValue('M'.$i, $itemgoods['optionname'])
//                ->setCellValue('N'.$i, $itemgoods['price'])
//                ->setCellValue('O'.$i, $itemgoods['total'])
//                ->setCellValue('P'.$i,$itemgoods['goodstotal']);
                ->setCellValueExplicit('A'.$i, $item['ordersn'],PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValue('B'.$i, $itemgoods['band'])
                ->setCellValue('C'.$i, $itemgoods['title'])
                ->setCellValue('D'.$i, $itemgoods['optionname'])
                ->setCellValue('E'.$i, $itemgoods['total'])
                ->setCellValue('F'.$i, $itemgoods['productprice'])
                ->setCellValue('G'.$i, round($itemgoods['productprice']*$itemgoods['total'],2))
                //->setCellValue('H'.$i, $itemgoods['goodstotal'])
                //->setCellValue('I'.$i, $itemgoods['productprice'])
                ->setCellValue('H'.$i, $item['address_realname'])
                ->setCellValue('I'.$i, $item['address_mobile'])
                ->setCellValue('J'.$i, $item['address_province'].$item['address_city'].$item['address_area'].$item['address_address'])
                ->setCellValue('K'.$i, $itemgoods['expresscom'])
                ->setCellValueExplicit('L'.$i, $itemgoods['expresssn'],PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValue('M'.$i, $itemgoods['dispatchprice'])//新增
                ->setCellValue('N'.$i, '')
                ->setCellValue('O'.$i, '')
                ->setCellValue('P'.$i, $item['remark']);
            $i++;
        }
    }

    $objPHPExcel->getActiveSheet()->getStyle('A1:P1')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(5);
    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(80);
    $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(35);
    //$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(35);
    $objPHPExcel->getActiveSheet()->setTitle('订单统计');
}

$objPHPExcel->setActiveSheetIndex(0);

//header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
//header('Content-Disposition: attachment;filename="report_'.time().'.xlsx"');
//header('Cache-Control: max-age=0');
//$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="report_'.time().'.xls"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

$objWriter->save('php://output');
exit;