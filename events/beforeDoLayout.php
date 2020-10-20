<?php
/**
 * 이 파일은 iModule IE버전확인 플러그인의 일부입니다. (https://www.imodules.io)
 *
 * 특정 IE 버전이하일 경우 사이트 접근을 차단하고 브라우저 업데이트 안내페이지를 출력합니다.
 * 
 * @file /plugins/iechecker/events/beforeDoLayout.php
 * @author Arzz (arzz@arzz.com)
 * @license MIT License
 * @version 3.0.0
 * @modified 2018. 3. 26.
 */
if (defined('__IM__') == false) exit;

/**
 * 브라우저가 IE이라면 IE버전을 확인한다.
 */
$isIE = strpos($_SERVER['HTTP_USER_AGENT'],'Trident') !== false || strpos($_SERVER['HTTP_USER_AGENT'],'MSIE') !== false;
$version = null;
if (preg_match('/MSIE ([0-9]+)\./',$_SERVER['HTTP_USER_AGENT'],$match) == true) {
	$version = intval($match[1]);
}
$version = $isIE === true && $version == null ? 11 : $version;

/**
 * 브라우저가 IE인 경우, 최소허용버전을 확인한 뒤 최소허용버전보다 낮은 버전일 경우 안내메세지를 출력한다.
 */
if ($isIE == true) {
	/**
	 * 플러그인 설정에서 최소허용버전을 확인한다.
	 */
	$minimum = $me->getConfig('minimum');
	if ($minimum > $version) {
		/**
		 * 템플릿을 가져온다.
		 */
		$templet = $me->getConfig('templet');
		$templet_configs = $me->getConfig('templet_configs');
		
		$templet = $me->getTemplet($templet,$templet_configs);
		echo $templet->getContext('index');
		exit;
	}
}
?>