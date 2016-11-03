<?php
/**
 * Created by PhpStorm.
 * User: yanru02
 * Date: 2016/10/26
 * Time: 12:11
 */
if('kinds' == $_GP['do']) {
    $operation = !empty($_GP['op']) ? $_GP['op'] : 'display';
    $state = $_GP['st'];
    if ($operation == 'del') {
        mysqld_delete('goods_kinds', array("kinds_level" => intval($_GP['kinds_level'])));
        message("删除成功！", "refresh", "success");
    }
    if ($operation == 'detail') {
        $condition = "kinds_level = " . intval($_GP['kinds_level']);
        $kinds = mysqld_select("SELECT * FROM " . table('goods_kinds') . " WHERE $condition");

        if (checksubmit('submit')) {
            if (empty($_GP['kinds_level'])) {
                message("商品类别编号不能为空!");
            } else if (empty($_GP['kinds_name'])) {
                message("商品类别名称不能为空!");
            } else if (empty($kinds)) {
                $data = array('kinds_level' => intval($_GP['kinds_level']),
                    'kinds_name' => $_GP['kinds_name']);
                mysqld_insert('goods_kinds', $data);
            } else {
                $data = array('kinds_name' => $_GP['kinds_name']);
                mysqld_update('goods_kinds', $data, array('kinds_level' => $kinds['kinds_level']));
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