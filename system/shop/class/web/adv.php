<?php
	
$operation = !empty($_GP['op']) ? $_GP['op'] : 'display';
if ($operation == 'display') {
    $list = mysqld_selectall("SELECT * FROM " . table('shop_adv') . "  ORDER BY displayorder DESC");
    include page('adv_list');
} elseif ($operation == 'post') {
    $id = intval($_GP['id']);
    if (checksubmit('submit')) {
        $data = array(
            'link' => $_GP['link'],
            'enabled' => intval($_GP['enabled']),
            'displayorder' => intval($_GP['displayorder'])
        );
        if (!empty($_FILES['thumb']['tmp_name'])) {
            $upload = file_upload($_FILES['thumb']);
            if (is_error($upload)) {
                message($upload['message'], '', 'error');
            }
            $data['thumb'] = $upload['path'];
        }

        $pos_key = array_keys($_POST);
        foreach ($pos_key as $pKey){
            if(strpos($pKey, "select_bonus") !== false){
                $data['hasbonus'] = $data['hasbonus']."-".$_POST[$pKey];
            }
        }
        if (!empty($id)) {
            mysqld_update('shop_adv', $data, array('id' => $id));
        } else {
            mysqld_insert('shop_adv', $data);
        }
        message('更新幻灯片成功！', web_url('adv', array('op' => 'display')), 'success');
    }
    $adv = mysqld_select("select * from " . table('shop_adv') . " where id=:id  limit 1", array(":id" => $id));
    //2016-11-29-yanru-begin
    $adv_bonus = array();
    if(!empty($adv['hasbonus'])){
        $adv_bonus = explode('-',$adv['hasbonus']);
        array_shift($adv_bonus);
    }
    $bonuslist = mysqld_selectall("select * from " . table('bonus_type') . " where send_type=0 and deleted=0 and use_start_date<=:use_start_date and use_end_date>=:use_end_date" ,array(":use_start_date"=>time(),":use_end_date"=>time()));
    //end
    include page('adv');
} elseif ($operation == 'delete') {
    $id = intval($_GP['id']);
    $adv = mysqld_select("SELECT id  FROM " . table('shop_adv') . " WHERE id = '$id' ");
    if (empty($adv)) {
        message('抱歉，幻灯片不存在或是已经被删除！', web_url('adv', array('op' => 'display')), 'error');
    }
    mysqld_delete('shop_adv', array('id' => $id));
    message('幻灯片删除成功！', web_url('adv', array('op' => 'display')), 'success');
} else {
    message('请求方式不存在');
}