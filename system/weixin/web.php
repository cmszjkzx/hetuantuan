<?php
defined('SYSTEM_IN') or exit('Access Denied');
define('subscribe_key', '系统_关注事件');
define('default_key', '系统_默认回复');
class weixinAddons  extends BjSystemModule {

		public function do_rule()
	{
		$this->__web(__FUNCTION__);
	}
	
	public function do_menumodify() {
		$this->__web(__FUNCTION__);
	}
	
	public function do_designer()
	{
		$this->__web(__FUNCTION__);
	}
	
	public function do_remove()
	{
		$this->__web(__FUNCTION__);
	}
	
	public function do_refresh()
	{
		message('', 'refresh');
	}
	
	public function do_setting()
	{
		$this->__web(__FUNCTION__);
	}
	
	public function do_guanzhu()
	{
		$this->__web(__FUNCTION__);
	}
	//2016-12-11-yanru-begin
    public function do_weixin_upload()
    {
        $this->__web(__FUNCTION__);
    }

    public function uploadMaterial($data, $status){
		    //http://www.cnblogs.com/binblogs/p/5207207.html
        if(0 == $status){
            $uDat = $data['media'];
            $access_token = get_weixin_token();
            $url = "https://api.weixin.qq.com/cgi-bin/media/upload?access_token={$access_token}&type={$data['type']}";
            $upload_curl = curl_init();
            $timeout = 5;
            $real_path = "{$_SERVER['DOCUMENT_ROOT']}"."/hetuantuan"."{$uDat['filename']}";
            //$real_path=str_replace("/", "//", $real_path);
            $upload_data = array("media" => "@{$real_path}", 'form-data' => $uDat);
            curl_setopt($upload_curl, CURLOPT_URL, $url);
            curl_setopt($upload_curl, CURLOPT_POST, true);
            curl_setopt($upload_curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($upload_curl, CURLOPT_CONNECTTIMEOUT, $timeout);
            curl_setopt ( $upload_curl, CURLOPT_SAFE_UPLOAD, false);
            if (stripos ( $url, "https://" ) !== false) {
                curl_setopt ($upload_curl, CURLOPT_SSL_VERIFYPEER, false );
                curl_setopt ($upload_curl, CURLOPT_SSL_VERIFYHOST, false);
            }
            curl_setopt($upload_curl, CURLOPT_POSTFIELDS, $upload_data);
            $result = curl_exec($upload_curl);
            curl_close($upload_curl);
            //$content = http_post($url, $uDat);
            return $this->menuResponseParse($result);
        }else if(1 == $status){
            $data = json_encode($data);
            $data = str_replace("\\", "", $data);
            $token = get_weixin_token();
            $url = "https://api.weixin.qq.com/cgi-bin/material/add_news?access_token={$token}";
            $content = http_post($url, $data);
            return $this->menuResponseParse($content);
        }
    }
    //end
	
	
		private function menuResponseParse($content) {
		if(empty($content)) {
			return message( "接口调用失败，请重试！公众平台返回错误信息: {$content}" );
		}
		$dat = $content;
		$result = @json_decode($dat, true);
		//2016-12-12-yanru
//		if(is_array($result) && $result['errcode'] == '0') {
//			return true;
//		}
            //end
            if(is_array($result) && $result['errcode'] == '0' || empty($result['errcode'])) {
                return true;
		}else {
			if(is_array($result)) {
				return  message("微信公众平台返回错误. 错误代码: {$result['errcode']} 错误信息: {$result['errmsg']} 错误描述: " . $this->error_code($result['errcode']));
			} else {
				return  message('微信公众平台未知错误');
			}
		}
	}
	
	
	private function error_code($code) {
		$errors = array(
			'-1' => '系统繁忙',
			'0' => '请求成功',
			'40001' => '获取access_token时AppSecret错误，或者access_token无效',
			'40002' => '不合法的凭证类型',
			'40003' => '不合法的OpenID',
			'40004' => '不合法的媒体文件类型',
			'40005' => '不合法的文件类型',
			'40006' => '不合法的文件大小',
			'40007' => '不合法的媒体文件id',
			'40008' => '不合法的消息类型',
			'40009' => '不合法的图片文件大小',
			'40010' => '不合法的语音文件大小',
			'40011' => '不合法的视频文件大小',
			'40012' => '不合法的缩略图文件大小',
			'40013' => '不合法的APPID',
			'40014' => '不合法的access_token',
			'40015' => '不合法的菜单类型',
			'40016' => '不合法的按钮个数',
			'40017' => '不合法的按钮个数',
			'40018' => '不合法的按钮名字长度',
			'40019' => '不合法的按钮KEY长度',
			'40020' => '不合法的按钮URL长度',
			'40021' => '不合法的菜单版本号',
			'40022' => '不合法的子菜单级数',
			'40023' => '不合法的子菜单按钮个数',
			'40024' => '不合法的子菜单按钮类型',
			'40025' => '不合法的子菜单按钮名字长度',
			'40026' => '不合法的子菜单按钮KEY长度',
			'40027' => '不合法的子菜单按钮URL长度',
			'40028' => '不合法的自定义菜单使用用户',
			'40029' => '不合法的oauth_code',
			'40030' => '不合法的refresh_token',
			'40031' => '不合法的openid列表',
			'40032' => '不合法的openid列表长度',
			'40033' => '不合法的请求字符，不能包含\uxxxx格式的字符',
			'40035' => '不合法的参数',
			'40038' => '不合法的请求格式',
			'40039' => '不合法的URL长度',
			'40050' => '不合法的分组id',
			'40051' => '分组名字不合法',
			'41001' => '缺少access_token参数',
			'41002' => '缺少appid参数',
			'41003' => '缺少refresh_token参数',
			'41004' => '缺少secret参数',
			'41005' => '缺少多媒体文件数据',
			'41006' => '缺少medSYSTEM_id参数',
			'41007' => '缺少子菜单数据',
			'41008' => '缺少oauth code',
			'41009' => '缺少openid',
			'42001' => 'access_token超时',
			'42002' => 'refresh_token超时',
			'42003' => 'oauth_code超时',
			'43001' => '需要GET请求',
			'43002' => '需要POST请求',
			'43003' => '需要HTTPS请求',
			'43004' => '需要接收者关注',
			'43005' => '需要好友关系',
			'44001' => '多媒体文件为空',
			'44002' => 'POST的数据包为空',
			'44003' => '图文消息内容为空',
			'44004' => '文本消息内容为空',
			'45001' => '多媒体文件大小超过限制',
			'45002' => '消息内容超过限制',
			'45003' => '标题字段超过限制',
			'45004' => '描述字段超过限制',
			'45005' => '链接字段超过限制',
			'45006' => '图片链接字段超过限制',
			'45007' => '语音播放时间超过限制',
			'45008' => '图文消息超过限制',
			'45009' => '接口调用超过限制',
			'45010' => '创建菜单个数超过限制',
			'45015' => '回复时间超过限制',
			'45016' => '系统分组，不允许修改',
			'45017' => '分组名字过长',
			'45018' => '分组数量超过上限',
			'46001' => '不存在媒体数据',
			'46002' => '不存在的菜单版本',
			'46003' => '不存在的菜单数据',
			'46004' => '不存在的用户',
			'47001' => '解析JSON/XML内容错误',
			'48001' => 'api功能未授权',
			'50001' => '用户未授权该api',
		);
		$code = strval($code);
	if($code == '40001') {
					$rec = array();
					$rec['access_token'] = '';
         refreshSetting($rec);
			
			return '微信公众平台授权异常, 系统已修复这个错误, 请刷新页面重试.';
		}
		if($errors[$code]) {
			return $errors[$code];
		} else {
			return '未知错误';
		}
	}
	
	


		public function menuCreate($domenu) {
		if($domenu==']')
		{
			return $this->menuDelete();
		}
		$mDat = $domenu;
		$mDat = htmlspecialchars_decode($mDat);
		$mDat=str_replace("\\\"","\"",$mDat);
		$menu = json_decode($mDat, true);
				if(empty($menu)) {
		message('自定义菜单结构错误！:<br/><textarea style="width:300px;height:100px">'.$mDat.'</textarea>');
	}
		foreach($menu as &$m) {
			$m['name'] = preg_replace_callback('/\:\:([0-9a-zA-Z_-]+)\:\:/', create_function('$matches', 'return utf8_bytes(hexdec($matches[1]));'), $m['name']);
			$m['name'] = urlencode($m['name']);
			if(isset($m['url']) && !empty($m['url'])){
				$m['url'] = $this->smartUrlEncode($m['url']);
			}
			if(is_array($m['sub_button'])) {
				foreach($m['sub_button'] as &$s) {
					$s['name'] = preg_replace_callback('/\:\:([0-9a-zA-Z_-]+)\:\:/', create_function('$matches', 'return utf8_bytes(hexdec($matches[1]));'), $s['name']);
					$s['name'] = urlencode($s['name']);
					if(!empty($s['url'])){
						$s['url'] = $this->smartUrlEncode($s['url']);
					}
				}
			}
		}
			
			if(!is_array($menu)) {
		message('操作非法，自定义菜单结构错误！');
	}
			$menus = array();
		$menus['button'] = $menu;
	 	$dat = json_encode($menus);
		$dat = urldecode($dat);
		$token = get_weixin_token();
		$url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token={$token}";
		$content = http_post($url, $dat);
		return $this->menuResponseParse($content);
	}
function smartUrlEncode($url){
	if (strpos($url, '=') === false) {
		return $url;
	} else {
		$urls = parse_url($url);
		parse_str($urls['query'], $queries);
		$params=array();
		if (!empty($queries)) {
			foreach ($queries as $variable => $value) {
				$params[$variable] = urlencode($value);
			}
		}
		$queryString = http_build_query($params, '', '&');
		return $urls['scheme'] . '://' . $urls['host'] . $urls['path'] . '?' . $queryString . '#' . $urls['fragment'];
	}
}
	public function menuDelete() {
		$token = get_weixin_token();
		$url = "https://api.weixin.qq.com/cgi-bin/menu/delete?access_token={$token}";
		$content = http_get($url);
		return $this->menuResponseParse($content);
	}

	public function menuModify($menu) {
		return $this->menuCreate($menu);
	}

	public function menuQuery() {
		$token = get_weixin_token();
		$url = "https://api.weixin.qq.com/cgi-bin/menu/get?access_token={$token}";
		$content = http_get($url);
		if(empty($content)) {
			return message( "接口调用失败，请重试！微信公众平台返回错误信息: {$content}" );
		}
		$dat = $content;
		$result = @json_decode($dat, true);
		if(is_array($result) && !empty($result['menu'])) {
			return $result;
		} else {
			if($result['errcode']!='46003')
			{
				get_weixin_token(true);
			if(is_array($result)) {
				return message( "微信公众平台返回接口错误。错误信息: {$result['errmsg']} 错误描述: " . $this->error_code($result['errcode']));
			} else {
				return message( '微信公众平台未知错误');
			}
		}
		}
	}
	function error($code, $msg = '') {
	return array(
		'errno' => $code,
		'message' => $msg,
	);
	}
}


