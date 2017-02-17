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
            'price' => $_GP['price'],
            'praise' => $_GP['praise'],
            'description' => $_GP['description'],
            'express' => $_GP['express']
        );

        if(time()>$data['limittime']){
            message('设置错误，截止时间不能小于当前时间','refresh','error');
            return;
        }

        if (!empty($_FILES['thumb']['tmp_name'])) {
            $upload = group_upload($_FILES['thumb']);
            if (is_error($upload)) {
                message($upload['message'], '', 'error');
            }
            $data['thumb'] = $upload['path'];
        }
        if (!empty($_FILES['sucessthumb']['tmp_name'])) {
            $upload = group_upload($_FILES['sucessthumb']);
            if (is_error($upload)) {
                message($upload['message'], '', 'error');
            }
            $data['sucessthumb'] = $upload['path'];
        }
        if (!empty($_FILES['head_thumb']['tmp_name'])) {
            $upload = group_upload($_FILES['head_thumb']);
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
}else if ('notice' == $operation) {
    $list = mysqld_selectall("SELECT weixin_openid FROM ".table('group_user')." WHERE goodid = :goodid AND ispraise=1 ", array(':goodid' => intval($_GP['id'])));
    if(!empty($list)){
        foreach ($list as $item){
            $notice = array(
                "touser" => $item['weixin_openid'],
                "template_id" => "A-pOebjfRNtuzGSqEVnGwgtjk1Hqt3G9GOpavMVHzb0",
            );
            $first = array(
                "value" => "您团购的商品已上架！",
                "color" => "#173177"
            );
            $keyword1 = array(
                "value" => $_GP['goodname'],
                "color" => "#173177"
            );
            $keyword2 = array(
                "value" => "中国移动和团团",
                "color" => "#173177"
            );
            $remark = array(
                "value" => "请及时购买哟！",
                "color" => "#173177"
            );
            $noticeDat = array(
                "first" => $first,
                "keyword1" => $keyword1,
                "keyword2" => $keyword2,
                "remark" => $remark
            );
            $notice["data"] = $noticeDat;
            $dat = json_encode($notice);
            $dat = urldecode($dat);
            $token = get_weixin_token();
            $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token={$token}";
            $content = http_post($url, $dat);
        }
        mysqld_update("group", array("isnotice" => 1), array("id" => $_GP['id']));
    }
    header("location:".create_url('site', array('name' => 'group','do' => 'group','op'=>'display')));
}