<?php
/**
 * Created by PhpStorm.
 * User: yanru02
 * Date: 2016/10/26
 * Time: 12:11
 */
if('kinds' == $_GP['do']) {
    $operation = !empty($_GP['op']) ? $_GP['op'] : 'display';
    //$state = $_GP['st'];
    if ($operation == 'del') {
        mysqld_delete('goods_kinds', array("kinds_level" => intval($_GP['kinds_level'])));
        message("删除成功！", "refresh", "success");
    }
    if ($operation == 'detail') {
        if($_GP['st']=='changekind') {
            $condition = "kinds_level = " . intval($_GP['kinds_level']);
            $kinds = mysqld_select("SELECT * FROM " . table('goods_kinds') . " WHERE $condition");
            if (!empty($kinds['kinds_thumb'])) {
                $thumb_path = explode("_", $kinds['kinds_thumb']);
                $kinds['kinds_thumb_before'] = $thumb_path[0];
                $kinds['kinds_thumb_after'] = $thumb_path[1];
            }
        }
        if (checksubmit('submit')) {
            if(($_GP['st']=='newkind') || ($_GP['st']=='changekind' && $_GP['id']!=$_GP['kinds_level'])){
                $haskind = mysqld_select("SELECT * FROM " . table('goods_kinds') . " WHERE kinds_level =:kinds_level ", array(":kinds_level"=>intval($_GP['kinds_level'])));
                if(!empty($haskind)){
                    message("商品类别编号不能重复!");
                }
            }
            if (empty($_GP['kinds_level'])) {
                message("商品类别编号不能为空!");
            } else if (empty($_GP['kinds_name'])) {
                message("商品类别名称不能为空!");
            } else if (empty($haskind) && $_GP['st']=='newkind') {
                $data = array('kinds_level' => intval($_GP['kinds_level']),
                    'kinds_name' => $_GP['kinds_name']);
                mysqld_insert('goods_kinds', $data);
            } else {
//                if($_GP['kinds_level'] == $kinds['kinds_level']){
//                    message("商品编号已经存在！");
//                }
                $thumbPath = '';
                if (!empty($_FILES['kinds_thumb_before']['tmp_name'])) {
                    $upload = file_upload($_FILES['kinds_thumb_before']);
                    if (is_error($upload)) {
                        message($upload['message'], '', 'error');
                    }
                    $thumbPath = $thumbPath.$upload['path'];
                }
                if (!empty($_FILES['kinds_thumb_after']['tmp_name'])) {
                    $upload = file_upload($_FILES['kinds_thumb_after']);
                    if (is_error($upload)) {
                        message($upload['message'], '', 'error');
                    }
                    $thumbPath = $thumbPath."_".$upload['path'];
                }
                $data = array('kinds_level' => $_GP['kinds_level'],
                    'kinds_name' => $_GP['kinds_name'],
                    'kinds_thumb' => $thumbPath);
                mysqld_update('goods_kinds', $data, array('kinds_level' => $_GP['id']));
            }
            message('操作成功！', web_url('kinds'), 'success');
        }
        include page('kinds');
        exit;
    }
    $kindslist = mysqld_selectall('SELECT * FROM ' . table('goods_kinds') . " ORDER BY kinds_level");
    include page('kinds_list');
}else if ('goods' == $_GP['do']){
    $kindslist = mysqld_selectall('SELECT * FROM ' . table('goods_kinds') . " ORDER BY kinds_level");
}