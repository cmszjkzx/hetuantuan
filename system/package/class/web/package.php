<?php
$operation = !empty($_GP['op']) ? $_GP['op'] : 'display';
if($operation=='delete') {
	mysqld_update('package_bonus',array('deleted'=>1),array('bonus_id'=>intval($_GP['bonus_id'])));
	message("删除成功！","refresh","success");
}
if($operation=='addbonus') {
//    if($_GP['count'] > 1){
//        exit;
//    }
    $add_bonus_number=$_GP['count'];
    include page('package_bonus');
    exit;
}
if($operation=='post') {
    $bonus_id = $_GP['bonus_id'];
	$bonus = mysqld_select("SELECT * FROM " . table('package_bonus')." where bonus_id='".intval($_GP['bonus_id'])."' " );
  	if (checksubmit('submit')) {
  	    if(empty($_GP['package_id'])){
  	        message("请填写优惠礼包编号！");
        }
        if(empty($_GP['max_amount'])){
            message("请填写优惠礼包金额！");
        }
        if(empty($_GP['max_number'])){
            message("请填写优惠礼包最大人数！");
        }
        if(empty($_GP['add_bonus_number'])&&empty($_GP['bonus_id'])){
            message("请添加优惠券！");
        }
		if(empty($_GP['bonus_id'])) {
		    for($i=1; $i<=$_GP['add_bonus_number']; $i++) {
		        if(empty($_GP['bonus_name_'.$i])){
                    message("请填写优惠券名！");
                }
                if(empty($_GP['bonus_money_'.$i])){
                    message("请填写优惠券金额！");
                }
                if(empty($_GP['min_goods_amount_'.$i])){
                    message("请填写最小订单金额！");
                }
                if(empty($_GP['eable_days_'.$i])){
                    message("请填写优惠券可用天数！");
                }
                $data=array('package_id'=>$_GP['package_id'],
                    'max_amount'=>$_GP['max_amount'],
                    'max_number'=>$_GP['max_number'],
                    'bonus_name'=>$_GP['bonus_name_'.$i],
                    'bonus_send_type'=>$_GP['bonus_send_type_'.$i],
                    'bonus_money'=>$_GP['bonus_money_'.$i],
                    'min_goods_amount'=>$_GP['min_goods_amount_'.$i],
                    'eable_days'=>$_GP['eable_days_'.$i],
                    'min_send_amount'=>$_GP['min_send_amount_'.$i]);

                mysqld_insert('package_bonus',$data);
            }
			message("添加成功",create_url('site', array('name' => 'package','do' => 'package','op'=>'display')),"success");
		} else {
		    if($_GP['bonus_send_type_']!=2)
                $_GP['min_send_amount_'] = 0;
			$data=array('package_id'=>$_GP['package_id'],
				'max_amount'=>$_GP['max_amount'],
                'max_number'=>$_GP['max_number'],
                'bonus_name'=>$_GP['bonus_name_'.$_GP['add_bonus_number']],
                'bonus_send_type'=>$_GP['bonus_send_type_'.$_GP['add_bonus_number']],
                'bonus_money'=>$_GP['bonus_money_'.$_GP['add_bonus_number']],
				'min_goods_amount'=>$_GP['min_goods_amount_'.$_GP['add_bonus_number']],
                'eable_days'=>$_GP['eable_days_'.$_GP['add_bonus_number']],
                'min_send_amount'=>$_GP['min_send_amount_'.$_GP['add_bonus_number']]);
			mysqld_update('package_bonus',$data,array('bonus_id'=>$_GP['bonus_id']));
			message("修改成功",create_url('site', array('name' => 'package','do' => 'package','op'=>'display')),"success");
		}
	}
	include page('package');
 	exit;
}
$bonus_list = mysqld_selectall("SELECT *,0 sendcount,0 usercount FROM " . table('package_bonus')." where deleted=0" );
foreach($bonus_list as $index=>$bonus)
{
  	$bonus_list[$index]['sendcount']= mysqld_selectcolumn("SELECT count(*) FROM " . table('package_bonus_user')." where deleted=0 and package_bonus_id=:package_bonus_id",array(":package_bonus_id"=>$bonus['bonus_id']));
  	$bonus_list[$index]['usercount']= mysqld_selectcolumn("SELECT count(*) FROM " . table('package_bonus_user')." where deleted=0 and isuse=1 and package_bonus_id=:package_bonus_id",array(":package_bonus_id"=>$bonus['bonus_id']));
}
include page('package_list');