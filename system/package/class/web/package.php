<?php
$operation = !empty($_GP['op']) ? $_GP['op'] : 'display';
if($operation=='delete') {
	mysqld_update('package_bonus',array('deleted'=>1),array('bonus_id'=>intval($_GP['id'])));
	message("删除成功！","refresh","success");
}
if($operation=='addbonus') {
    $add_bonus_number = $_GP['count'];
    include page('package_bonus');
    exit;
}
if($operation=='post') {
	$bonus = mysqld_select("SELECT * FROM " . table('package_bonus')." where bonus_id='".intval($_GP['bonus_id'])."' " );
  	if (checksubmit('submit')) {
		if(empty($_GP['bonus_id'])) {
		    for($i=1; $i<=$_GP['add_bonus_number']; $i++) {

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
                'bonus_name'=>$_GP['bonus_name_'],
                'bonus_send_type'=>$_GP['bonus_send_type_'],
                'bonus_money'=>$_GP['bonus_money_'],
				'min_goods_amount'=>$_GP['min_goods_amount_'],
                'eable_days'=>$_GP['eable_days_'],
                'min_send_amount'=>$_GP['min_send_amount_']);
			mysqld_update('package_bonus',$data,array('bonus_id'=>$_GP['bonus_id']));
			message("修改成功","refresh","success");
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