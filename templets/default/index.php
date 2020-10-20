<?php
/**
 * 이 파일은 iModule IE버전확인 플러그인의 일부입니다. (https://www.imodules.io)
 *
 * 브라우저 업데이트 안내 페이지 기본 템플릿
 * 
 * @file /plugins/iechecker/templets/default/index.php
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
 * 플러그인 설정에서 최소허용버전을 확인한다.
 */
$minimum = $me->getConfig('minimum');

/**
 * 기본메세지
 */
$message = str_replace(array('{VERSION}','{MINIMUM}'),array($version,$minimum),$Templet->getConfig('message'));

// @todo IE6에서도 잘 보이도록 안내페이지 디자인 필요
?>
<html>
	<body>
		<?php echo $message; ?>
	</body>
</html>