<?php

$cfg = globaSetting();
$category = mysqld_selectall("SELECT * FROM " . table('shop_category') . " where deleted=0 ORDER BY parentid ASC, displayorder DESC", array(), 'id');
if (!empty($category)) {
    //变成树状结构，从父节点开始
    $children = '';
    foreach ($category as $cid => $cate) {
        if (!empty($cate['parentid'])) {
            $children[$cate['parentid']][$cate['id']] = array($cate['id'], $cate['name']);
        }
    }
}
$operation = !empty($_GP['op']) ? $_GP['op'] : 'display';
        
if ($operation == 'post') {
    $id = intval($_GP['id']);
    if (!empty($id)) {
        $item = mysqld_select("SELECT * FROM " . table('shop_goods') . " WHERE id = :id", array(':id' => $id));
        if (empty($item)) {
            message('抱歉，商品不存在或是已经删除！', '', 'error');
        }
        require(WEB_ROOT.'/system/common/extends/class/shop/class/web/goods_0.php');
           
        //查询该商品的样式
        $allspecs = mysqld_selectall("select * from " . table('shop_goods_spec')." where goodsid=:id order by displayorder asc",array(":id"=>$id));
        //遍历每个样式下的具体内容
        foreach ($allspecs as &$s) {
            $s['items'] = mysqld_selectall("select * from " . table('shop_goods_spec_item') . " where specid=:specid order by displayorder asc", array(":specid" => $s['id']));
        }
        unset($s);

        $piclist = mysqld_selectall("SELECT * FROM " . table('shop_goods_piclist') . " where goodid=$id ORDER BY id ASC");
       	
        //处理规格项
        $html = "";
        $options = mysqld_selectall("select * from " . table('shop_goods_option') . " where goodsid=:id order by id asc", array(':id' => $id));

        //排序好的specs
        $specs = array();
        //找出数据库存储的排列顺序
        if (count($options) > 0) {
            $specitemids = explode("_", $options[0]['specs'] );
            foreach($specitemids as $itemid){
                foreach($allspecs as $ss){
                    $items=  $ss['items'];
                    foreach($items as $it){
                        if($it['id']==$itemid){
                            $specs[] = $ss;
                            break;
                        }
                    }
                }
            }
                    
            $html = '<table  class="spectable" style="border:1px solid #ccc;"><thead><tr>';

            $len = count($specs);
            $newlen = 1; //多少种组合
            $h = array(); //显示表格二维数组
            $rowspans = array(); //每个列的rowspan

            for ($i = 0; $i < $len; $i++) {
                //表头
                $html.="<th>" . $specs[$i]['title'] . "</th>";
                //计算多种组合
                $itemlen = count($specs[$i]['items']);
                if ($itemlen <= 0) {
                    $itemlen = 1;
                }
                $newlen*=$itemlen;
                //初始化 二维数组
                $h = array();
                for ($j = 0; $j < $newlen; $j++) {
                    $h[$i][$j] = array();
                }
                //计算rowspan
                $l = count($specs[$i]['items']);
                $rowspans[$i] = 1;
                for ($j = $i + 1; $j < $len; $j++) {
                    $rowspans[$i]*= count($specs[$j]['items']);
                }
            }

            $html .= '<th>库存：<input type="text" class="span1 option_stock_all"  VALUE=""/><span class="add-on">&nbsp;<a href="javascript:;" class="icon-hand-down" title="批量设置" onclick="setCol(\'option_stock\');"></a></span></th>';
            $html.= '<th>销售价格：<input type="text" class="span1 option_marketprice_all"  VALUE=""/><span class="add-on">&nbsp;<a href="javascript:;" class="icon-hand-down" title="批量设置" onclick="setCol(\'option_marketprice\');"></a></span></th>';
            $html.='<th>市场价格：<input type="text" class="span1 option_productprice_all"  VALUE=""/><span class="add-on">&nbsp;<a href="javascript:;" class="icon-hand-down" title="批量设置" onclick="setCol(\'option_productprice\');"></a></span></th>';
            $html.='<th>重量(克)：<input type="text" class="span1 option_weight_all"  VALUE=""/><span class="add-on">&nbsp;<a href="javascript:;" class="icon-hand-down" title="批量设置" onclick="setCol(\'option_weight\');"></a></span></th>';
            $html.='</tr>';
            for($m=0;$m<$len;$m++){
                $k = 0;$kid = 0;$n=0;
                for($j=0;$j<$newlen;$j++){
                    $rowspan = $rowspans[$m]; //9
                    if( $j % $rowspan==0){
                        $h[$m][$j]=array("html"=> "<td rowspan='".$rowspan."'>".$specs[$m]['items'][$kid]['title']."</td>","id"=>$specs[$m]['items'][$kid]['id']);
                        // $k++; if($k>count($specs[$m]['items'])-1) { $k=0; }
                    }
                    else{
                        $h[$m][$j]=array("html"=> "","id"=>$specs[$m]['items'][$kid]['id']);
                    }
                    $n++;
                    if($n==$rowspan){
                        $kid++; if($kid>count($specs[$m]['items'])-1) { $kid=0; }
                        $n=0;
                    }
                }
            }
         
            $hh = "";
            for ($i = 0; $i < $newlen; $i++) {
                $hh.="<tr>";
                $ids = array();
                for ($j = 0; $j < $len; $j++) {
                    $hh.=$h[$j][$i]['html'];
                    $ids[] = $h[$j][$i]['id'];
                }
                $ids = implode("_", $ids);

                $val = array("id" => "","title"=>"", "stock" => "", "costprice" => "", "productprice" => "", "marketprice" => "", "weight" => "");
                if(!empty($options))
                {
                    foreach ($options as $o) {
                        if ($ids === $o['specs']) {
                            $val = array("id" => $o['id'],
                                "title"=>$o['title'],
                                "stock" => $o['stock'],
                                "productprice" => $o['productprice'],
                                "marketprice" => $o['marketprice'],
                                "weight" => $o['weight']);
                            break;
                        }
                    }
												}
                $hh .= '<td>';
                $hh .= '<input name="option_stock_' . $ids . '[]"  type="text" class="span1 option_stock option_stock_' . $ids . '" value="' . $val['stock'] . '"/></td>';
                $hh .= '<input name="option_id_' . $ids . '[]"  type="hidden" class="span1 option_id option_id_' . $ids . '" value="' . $val['id'] . '"/>';
                $hh .= '<input name="option_ids[]"  type="hidden" class="span1 option_ids option_ids_' . $ids . '" value="' . $ids . '"/>';
                $hh .= '<input name="option_title_' . $ids . '[]"  type="hidden" class="span1 option_title option_title_' . $ids . '" value="' . $val['title'] . '"/>';
                $hh .= '</td>';
                $hh .= '<td><input name="option_marketprice_' . $ids . '[]" type="text" class="span1 option_marketprice option_marketprice_' . $ids . '" value="' . $val['marketprice'] . '"/></td>';
                $hh .= '<td><input name="option_productprice_' . $ids . '[]" type="text" class="span1 option_productprice option_productprice_' . $ids . '" " value="' . $val['productprice'] . '"/></td>';
                $hh .= '<td><input name="option_weight_' . $ids . '[]" type="text" class="span1 option_weight option_weight_' . $ids . '" " value="' . $val['weight'] . '"/></td>';
                $hh .="</tr>";
            }
            $html.=$hh;
            $html.="</table>";
        }
    }
    if (empty($category)) {
        message('抱歉，请您先添加商品分类！', web_url('category', array('op' => 'post')), 'error');
    }
    if (checksubmit('submit')) {
            
        if (empty($_GP['goodsname'])) {
            message('请输入商品名称！');
        }
        if (empty($_GP['pcate'])) {
            message('请选择商品分类！');
        }
        if (empty($_GP['total'])) {
            message('请输入商品库存！');
        }
        //2016-10-26-yanru-begin
        if (empty($_GP['kinds'])) {
            message('请选择商品类别！');
        }
//        //2016-11-10-yanru
//        if (empty($_GP['videopath'])) {
//            message('请上传商品视频！');
//        }
//        //end
        $data = array(
            'pcate' => intval($_GP['pcate']),
            'ccate' => intval($_GP['ccate']),
            'type' => 0,
            'status' => intval($_GP['status']),
            'displayorder' => intval($_GP['displayorder']),
            'title' => $_GP['goodsname'],
            'description' => $_GP['description'],
            'content' => htmlspecialchars_decode($_GP['content']),
            'express' => $_GP['express'],
            'service' => $_GP['service'],
            'goodssn' => $_GP['goodssn'],
            'productsn' => $_GP['productsn'],
            'marketprice' => $_GP['marketprice'],
            'weight' => $_GP['weight'],
            'productprice' => $_GP['productprice'],
            'total' => intval($_GP['total']),
            'totalcnf' => intval($_GP['totalcnf']),
            'credit' => intval($_GP['credit']),
            'createtime' => TIMESTAMP,
            'isnew' => intval($_GP['isnew']),
            'isfirst' => intval($_GP['isfirst']),
            'ishot' => intval($_GP['ishot']),
            'isjingping' => intval($_GP['isjingping']),
            'issendfree' => intval($_GP['issendfree']),
            'type' => intval($_GP['type']),
            'ishot' => intval($_GP['ishot']),
            'isdiscount' => intval($_GP['isdiscount']),
            'isrecommand' => intval($_GP['isrecommand']),
            'istime' => intval($_GP['istime']),
            'timestart' => strtotime($_GP['timestart']),
            'hasoption' => intval($_GP['hasoption']),
            'timeend' => strtotime($_GP['timeend']),
            'isverify' => intval($_GP['isverify']),
            'kinds' => intval($_GP['kinds']),
            'videopath' => $_GP['videopath']
        );
        require(WEB_ROOT.'/system/common/extends/class/shop/class/web/goods_1.php');
                    
        if (!empty($_FILES['thumb']['tmp_name'])) {
            $upload = file_upload($_FILES['thumb']);
            if (is_error($upload)) {
                message($upload['message'], '', 'error');
            }
            $data['thumb'] = $upload['path'];
        }

        //2016-11-16-yanru-begin-video
        if (!empty($_FILES['videopath']['tmp_name'])) {
            $upload = video_upload($_FILES['videopath']);
            if (is_error($upload)) {
                message($upload['message'], '', 'error');
            }
            $data['videopath'] = $upload['path'];
        }
        //end

        if (empty($id)) {
            $data['sales']=0;
            mysqld_insert('shop_goods', $data);
            $id = mysqld_insertid();
        } else {
            unset($data['createtime']);
            unset($data['sales']);
            mysqld_update('shop_goods', $data, array('id' => $id));
        }
                
        require(WEB_ROOT.'/system/common/extends/class/shop/class/web/goods_2.php');
           
        $hsdata=array();
        if (!empty($_GP['attachment-new'])) {
            foreach ($_GP['attachment-new'] as $index => $row) {
                if (empty($row)) {
                    continue;
                }
                $hsdata[$index] = array(
                    'attachment' => $_GP['attachment-new'][$index],
                );
            }
            $cur_index = $index + 1;
        }
        if (!empty($_GP['attachment'])) {
            foreach ($_GP['attachment'] as $index => $row) {
                if (empty($row)) {
                    continue;
                }
                $hsdata[$cur_index + $index] = array(
                    'attachment' => $_GP['attachment'][$index]
                );
            }
        }
        mysqld_delete('shop_goods_piclist', array('goodid' => $id));
        foreach ($hsdata as $row) {
            $data = array(
                'goodid' => $id,
                'picurl' =>$row['attachment']
            );
            mysqld_insert('shop_goods_piclist', $data);
        }
                
        //处理商品规格
        $files = $_FILES;
        $spec_ids = $_POST['spec_id'];
        $spec_titles = $_POST['spec_title'];

        $specids = array();
        $len = count($spec_ids);
        $specids = array();
        $spec_items = array();
        for ($k = 0; $k < $len; $k++) {
            $spec_id = "";
            $get_spec_id = $spec_ids[$k];
            $a = array(
                "goodsid" => $id,
                "displayorder" => $k,
                "title" => $spec_titles[$get_spec_id]
            );
            $tspec = mysqld_select("SELECT id FROM " . table('shop_goods_spec') . " WHERE id = :id", array(':id' => $get_spec_id));
              
            if (is_numeric($get_spec_id)&&!empty($get_spec_id)&&!empty($tspec['id'])) {
                mysqld_update("shop_goods_spec", $a, array("id" => $get_spec_id));
                $spec_id = $get_spec_id;
            } else {
                mysqld_insert("shop_goods_spec", $a);
                $spec_id = mysqld_insertid();
            }
            //子项
            $spec_item_ids = $_POST["spec_item_id_".$get_spec_id];
            $spec_item_titles = $_POST["spec_item_title_".$get_spec_id];
            $spec_item_shows = $_POST["spec_item_show_".$get_spec_id];
                    
            $spec_item_oldthumbs = $_POST["spec_item_oldthumb_".$get_spec_id];
            $itemlen = count($spec_item_ids);
            $itemids = array();
                    
            for ($n = 0; $n < $itemlen; $n++) {
                $item_id = "";
                $get_item_id = $spec_item_ids[$n];
                $d = array(
                    "specid" => $spec_id,
                    "displayorder" => $n,
                    "title" => $spec_item_titles[$n],
                    "show" => $spec_item_shows[$n]
                );
                $f = "spec_item_thumb_" . $get_item_id;
                $old = $_GP["spec_item_oldthumb_".$get_item_id];
                	
                if (!empty($files[$f]['tmp_name'])) {
                    $upload = file_upload($files[$f]);
                    if (is_error($upload)) {
                        message($upload['message'], '', 'error');
                    }
                    $d['thumb'] = $upload['path'];
                } else if (!empty($old)) {
                    $d['thumb'] = $old;
                }
                $tspecitems = mysqld_select("SELECT id FROM " . table('shop_goods_spec_item') . " WHERE id = :id", array(':id' => $get_item_id));
              
                if (is_numeric($get_item_id)&&!empty($get_item_id)&&!empty($tspecitems['id'])) {
                    mysqld_update("shop_goods_spec_item", $d, array("id" => $get_item_id));
                    $item_id = $get_item_id;
                } else {
                    mysqld_insert("shop_goods_spec_item", $d);
                    $item_id = mysqld_insertid();
                }
                $itemids[] = $item_id;

                //临时记录，用于保存规格项
                $d['get_id'] = $get_item_id;
                $d['id']= $item_id;
                $spec_items[] = $d;
            }
            //删除其他的
            if(count($itemids)>0){
                mysqld_query("delete from " . table('shop_goods_spec_item') . " where 1=1 and specid=$spec_id and id not in (" . implode(",", $itemids) . ")");
            }
            else{
                mysqld_query("delete from " . table('shop_goods_spec_item') . " where 1=1 and specid=$spec_id");
            }
                    
            //更新规格项id
            mysqld_update("shop_goods_spec", array("content" => serialize($itemids)), array("id" => $spec_id));

            $specids[] = $spec_id;
        }

        //删除其他的
        if( count($specids)>0){
            mysqld_query("delete from " . table('shop_goods_spec') . " where 1=1 and goodsid=$id and id not in (" . implode(",", $specids) . ")");
        }
        else{
            mysqld_query("delete from " . table('shop_goods_spec') . " where 1=1 and goodsid=$id");
        }

        //保存规格
           
        $option_idss = $_POST['option_ids'];
        $option_productprices = $_POST['option_productprice'];
        $option_marketprices = $_POST['option_marketprice'];
        $option_costprices = $_POST['option_costprice'];
        $option_stocks = $_POST['option_stock'];
        $option_weights = $_POST['option_weight'];
        $len = count($option_idss);
        $optionids = array();
        for ($k = 0; $k < $len; $k++) {
            $option_id = "";
            $get_option_id = $_GP['option_id_' . $ids][0];
             
            $ids = $option_idss[$k]; $idsarr = explode("_",$ids);
            $newids = array();
            foreach($idsarr as $key=>$ida){
                foreach($spec_items as $it){
                    if($it['get_id']==$ida){
                        $newids[] = $it['id'];
                        break;
                    }
                }
            }
            $newids = implode("_",$newids);
                     
            $a = array(
                "title" => $_GP['option_title_' . $ids][0],
                "productprice" => $_GP['option_productprice_' . $ids][0],
                "costprice" => $_GP['option_costprice_' . $ids][0],
                "marketprice" => $_GP['option_marketprice_' . $ids][0],
                "stock" => $_GP['option_stock_' . $ids][0],
                "weight" => $_GP['option_weight_' . $ids][0],
                "goodsid" => $id,
                "specs" => $newids
            );
                   
            $totalstocks+=$a['stock'];

            if (empty($get_option_id)) {
                mysqld_insert("shop_goods_option", $a);
                $option_id = mysqld_insertid();
            } else {
                mysqld_update("shop_goods_option", $a, array('id' => $get_option_id));
                $option_id = $get_option_id;
            }
            $optionids[] = $option_id;
        }
        if (count($optionids) > 0) {
            mysqld_query("delete from " . table('shop_goods_option') . " where goodsid=$id and id not in ( " . implode(',', $optionids) . ")");
        }
        else{
            mysqld_query("delete from " . table('shop_goods_option') . " where goodsid=$id");
        }
        //总库存
        if ($totalstocks > 0) {
            mysqld_update("shop_goods", array("total" => $totalstocks), array("id" => $id));
        }
        message('商品操作成功！', web_url('goods', array('op' => 'post', 'id' => $id)), 'success');
    }
    require(WEB_ROOT.'/system/common/extends/class/shop/class/web/goods_3.php');
    require(WEB_ROOT.'/system/shop/class/web/kinds.php');
    include page('goods');
} elseif ($operation == 'display') {
    $pindex = max(1, intval($_GP['page']));
    $psize = 10;
    $condition = '';
    if (!empty($_GP['keyword'])) {
        $condition .= " AND title LIKE '%{$_GP['keyword']}%'";
    }
					
    if (!empty($_GP['cate_2'])) {
        $cid = intval($_GP['cate_2']);
        $condition .= " AND ccate = '{$cid}'";
    } elseif (!empty($_GP['cate_1'])) {
        $cid = intval($_GP['cate_1']);
        $condition .= " AND pcate = '{$cid}'";
    }

    if (isset($_GP['status'])) {
        $condition .= " AND status = '" . intval($_GP['status']) . "'";
    }

    $list = mysqld_selectall("SELECT * FROM " . table('shop_goods') . " WHERE  deleted=0 $condition ORDER BY status DESC, displayorder DESC, id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
    $total = mysqld_selectcolumn('SELECT COUNT(*) FROM ' . table('shop_goods') . " WHERE deleted=0 $condition");
    $pager = pagination($total, $pindex, $psize);
    include page('goods_list');
} elseif ($operation == 'delete') {
    $id = intval($_GP['id']);
    $row = mysqld_select("SELECT id, thumb FROM " . table('shop_goods') . " WHERE id = :id", array(':id' => $id));
    if (empty($row)) {
        message('抱歉，商品不存在或是已经被删除！');
    }
    //修改成不直接删除，而设置deleted=1
    mysqld_update("shop_goods", array("deleted" => 1), array('id' => $id));

    message('删除成功！', 'refresh', 'success');
}