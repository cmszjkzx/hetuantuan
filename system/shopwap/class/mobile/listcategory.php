<?php
$settings=globaSetting();
if(empty($settings['shop_category_style']))
{
    /*
     * 2016-10-27-yanru
     */
    $category = mysqld_selectall("SELECT *,'' as list FROM " . table('shop_category') . " WHERE isrecommand=1 and enabled=1 and deleted=0 ORDER BY parentid ASC, displayorder DESC", array(), 'id');
    //$category = mysqld_selectall("SELECT * FROM " . table('shop_category') . " WHERE deleted=0 and enabled=1 ORDER BY parentid ASC, displayorder DESC");
    foreach ($category as $index => $row) {
        if (!empty($row['parentid'])) {
            $children[$row['parentid']][$row['id']] = $row;
            $children_category[$row['parentid']][$row['id']] = $row;
            unset($category[$index]);
        }
    }
    /*
     * begin
     */
    $recommandcategory = array();
    foreach ($category as &$c) {
        if ($c['isrecommand'] == 1) {
            $c['list'] = mysqld_selectall("SELECT * FROM " . table('shop_goods') . " WHERE  isrecommand=1 and deleted=0 AND status = 1  and pcate='{$c['id']}'  ORDER BY displayorder DESC, sales");
            $recommandcategory[] = $c;
        }
        if (!empty($children_category[$c['id']])) {
            foreach ($children_category[$c['id']] as &$child) {
                if ($child['isrecommand'] == 1) {
                    $child['list'] = mysqld_selectall("SELECT * FROM " . table('shop_goods') . " WHERE  isrecommand=1 and deleted=0 AND status = 1  and pcate='{$c['id']}' and ccate='{$child['id']}'  ORDER BY displayorder DESC, sales DESC ");
                    $recommandcategory[] = $child;
                }
            }
            unset($child);
        }
    }
    include themePage('list_category');
}else {
    $categoryparent = mysqld_selectall("SELECT * FROM " . table('shop_category') . " WHERE deleted=0 and enabled=1 and parentid=0 ORDER BY parentid ASC, displayorder DESC");
    $ppcateid=$_GP['ppcateid'];
    if(empty($ppcateid))
    {
        if(!empty($categoryparent)&&is_array($categoryparent)&&count($categoryparent)>0)
        {
            $ppcateid=$categoryparent[0]['id'];
        }
    }
    if(!empty($ppcateid))
    {
        $categorypitem = mysqld_select("SELECT * FROM " . table('shop_category') . " WHERE deleted=0 and enabled=1 and id=:id ORDER BY parentid ASC, displayorder DESC",   array(":id"=> intval($ppcateid)));
        $categoryson = mysqld_selectall("SELECT * FROM " . table('shop_category') . " WHERE deleted=0 and enabled=1 and parentid=:parentid ORDER BY parentid ASC, displayorder DESC",   array(":parentid"=> intval($ppcateid)));
    }
    
    include themePage('list_category_pic');
}