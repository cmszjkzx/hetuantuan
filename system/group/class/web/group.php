<?php
$operation = !empty($_GP['op']) ? $_GP['op'] : 'display';
if('post' == $operation){
    $id = intval($_GP['id']);
    if (checksubmit('submit')) {
        if (empty($_GP['goodname'])) {
            message('请输入参团商品名称！');
        }
        if (1 == $_GP['isshow'] && 1 == $_GP['isgroup']) {
            message('请确认商品状态是否正确！');
        }
        if (1 == $_GP['isgroup'] && empty($_GP['goodsn'])) {
            message('请输入商品上架后的编号！');
        }
        $data = array(
            'goodname' => $_GP['goodname'],
            'isshow' => intval($_GP['isshow']),
            'limittime' => strtotime($_GP['limittime']),
            'isgroup' => intval($_GP['isgroup']),
            'goodsn' => $_GP['goodsn'],
            'praise' => $_GP['praise'],
            'description' => $_GP['description'],
            'express' => $_GP['express']
        );

        if(time()>$data['limittime']){
            message('设置错误，截止时间不能小于当前时间','refresh','error');
            return;
        }

        if (!empty($_FILES['thumb']['tmp_name'])) {
            $upload = file_upload($_FILES['thumb']);
            if (is_error($upload)) {
                message($upload['message'], '', 'error');
            }
            $data['thumb'] = $upload['path'];
        }
        if (!empty($_FILES['head_thumb']['tmp_name'])) {
            $upload = file_upload($_FILES['head_thumb']);
            if (is_error($upload)) {
                message($upload['message'], '', 'error');
            }
            $head_thumb_path = $upload['path'];
            $item = mysqld_select("SELECT * FROM " . table('shop_adv'). " WHERE link=:link",array(":link"=>'group'));
            if(empty($item))
                mysqld_insert('shop_adv', array('link' => 'group', 'thumb' => $head_thumb_path));
            else
                mysqld_update('shop_adv', array('thumb' => $head_thumb_path), array('link' => 'group'));
        }
        if (empty($id)) {
            mysqld_insert('group', $data);
            $id = mysqld_insertid();
        } else {
            mysqld_update('group', $data, array('id' => $id));
        }
        header("location:".create_url('site', array('name' => 'group','do' => 'group','op'=>'post','id'=>$id)));
        exit;
    }

    $head_thumb = mysqld_select("SELECT thumb FROM " . table('shop_adv'). " WHERE link=:link",array(":link"=>'group'));
    if (!empty($id)) {
        $item = mysqld_select("SELECT * FROM " . table('group') . " WHERE id = :id", array(':id' => $id));
        if (empty($item)) {
            message('抱歉，商品不存在或是已经删除！', '', 'error');
        }
    }
    include page('group_goods');
} else if ('display' == $operation) {
    $list = mysqld_selectall("SELECT * FROM " . table('group'));
    include page('group_goods_list');
} else if ('delete' == $operation) {
    mysqld_delete('group', array('id' => intval($_GP['id'])));
    include page('group_goods_list');
}