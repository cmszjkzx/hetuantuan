<?php
/**
 * Created by PhpStorm.
 * User: yanru02
 * Date: 2016/12/11
 * Time: 10:50
 */
?>
<?php
if(checksubmit()){
    if (!empty($_FILES['material']['tmp_name'])) {
        $upload = material_upload($_FILES['material']);
        if (is_error($upload)) {
            message($upload['message'], '', 'error');
        }
        $filename = "/attachment/".$upload['path'];
    }else{
        message("请上传素材!");
    }
    if(0 == $_GP['status']){
        if(empty($_GP['type'])){
            message("请选择上传的素材类型!");
        }else{
            $media = array('filename'=>$filename,
                'filelength'=>$_FILES['material']['size'],
                'content-type'=>$_FILES['material']['type']);
            $data = array('type'=>$_GP['type'],
                'media'=>$media);
        }
    }else if(1 == $_GP['status']){
        if(empty($_GP['title'])){
            message("请输入标题!");
        }
        if(empty($_GP['thumb_media_id'])){
            message("请输入图片素材ID!");
        }
        if(empty($_GP['author'])){
            message("请输入作者名称!");
        }
        if(!isset($_GP['show_cover_pic'])){
            message("请选择是否显示封面!");
        }
        if(empty($_GP['content'])){
            message("请输入图文具体内容!");
        }
        if(empty($_GP['content_source_url'])){
            message("请输入原文地址!");
        }
        $data['articles'] = array('title'=>$_GP['title'],
            'thumb_media_id'=>$_GP['thumb_media_id'],
            'author'=>$_GP['author'],
            'digest'=>$_GP['digest'],
            'show_cover_pic'=>$_GP['show_cover_pic'],
            'content'=>$_GP['content'],
            'content_source_url'=>$_GP['content_source_url']
        );
    }else{
        message("请选择是否为永久素材!");
    }
    $return= $this->uploadMaterial($data, $_GP['status']);
    if($return==true)
    {
        message('修改成功', 'refresh', 'success');
    }else
    {
        message($return);
    }
}

include page('weixin_upload');

