<?php
/**
 * 이 파일은 iModule IE버전확인플러그인 일부입니다. (https://www.iplugin.kr)
 *
 * IE버전을 확인하여 특정 IE버전 이하일 경우 홈페이지 접속을 차단하고 브라우져 업데이트 안내메세지를 출력한다.
 * 
 * @file /plugins/iechecker/PluginIechecker.class.php
 * @author Arzz (arzz@arzz.com)
 * @license MIT License
 * @version 3.0.0
 * @modified 2018. 6. 21.
 */
class PluginIechecker {
	/**
	 * iModule core 와 Plugin core 클래스
	 */
	private $IM;
	private $Plugin;
	
	/**
	 * DB 관련 변수정의
	 *
	 * @private object $DB DB접속객체
	 * @private string[] $table DB 테이블 별칭 및 원 테이블명을 정의하기 위한 변수
	 */
	private $DB;
	private $table;
	
	/**
	 * 언어셋을 정의한다.
	 * 
	 * @private object $lang 현재 사이트주소에서 설정된 언어셋
	 * @private object $oLang package.json 에 의해 정의된 기본 언어셋
	 */
	private $lang = null;
	private $oLang = null;
	
	/**
	 * class 선언
	 *
	 * @param iModule $IM iModule core class
	 * @param Plugin $Plugin Plugin core class
	 * @see /classes/iModule.class.php
	 * @see /classes/Plugin.class.php
	 */
	function __construct($IM,$Plugin) {
		$this->IM = $IM;
		$this->Plugin = $Plugin;
		
		/**
		 * 플러그인에서 사용하는 DB 테이블 별칭 정의
		 * @see 플러그인폴더의 package.json 의 databases 참고
		 */
		$this->table = new stdClass();
		$this->table->log = 'iechecker_log_table';
	}
	
	/**
	 * 플러그인 코어 클래스를 반환한다.
	 * 현재 플러그인의 각종 설정값이나 플러그인의 package.json 설정값을 플러그인 코어 클래스를 통해 확인할 수 있다.
	 *
	 * @return Plugin $Plugin
	 */
	function getPlugin() {
		return $this->Plugin;
	}
	
	/**
	 * 플러그인 설치시 정의된 DB코드를 사용하여 플러그인에서 사용할 전용 DB클래스를 반환한다.
	 *
	 * @return DB $DB
	 */
	function db() {
		if ($this->DB == null || $this->DB->ping() === false) $this->DB = $this->IM->db($this->getPlugin()->getInstalled()->database);
		return $this->DB;
	}
	
	/**
	 * 플러그인에서 사용중인 DB테이블 별칭을 이용하여 실제 DB테이블 명을 반환한다.
	 *
	 * @param string $table DB테이블 별칭
	 * @return string $table 실제 DB테이블 명
	 */
	function getTable($table) {
		return empty($this->table->$table) == true ? null : $this->table->$table;
	}
	
	/**
	 * [사이트관리자] 플러그인 설정패널을 구성한다.
	 *
	 * @return string $panel 설정패널 HTML
	 */
	function getConfigPanel() {
		/**
		 * 설정패널 PHP에서 iModule 코어클래스와 플러그인코어클래스에 접근하기 위한 변수 선언
		 */
		$IM = $this->IM;
		$Plugin = $this->getPlugin();
		
		ob_start();
		INCLUDE $this->getPlugin()->getPath().'/admin/configs.php';
		$panel = ob_get_contents();
		ob_end_clean();
		
		return $panel;
	}
	
	/**
	 * [사이트관리자] 플러그인 관리자패널 구성한다.
	 *
	 * @return string $panel 관리자패널 HTML
	 */
	function getAdminPanel() {
		/**
		 * 설정패널 PHP에서 iModule 코어클래스와 플러그인코어클래스에 접근하기 위한 변수 선언
		 */
		$IM = $this->IM;
		$Plugin = $this;
		
		ob_start();
		INCLUDE $this->getPlugin()->getPath().'/admin/index.php';
		$panel = ob_get_contents();
		ob_end_clean();
		
		return $panel;
	}
	
	/**
	 * 언어셋파일에 정의된 코드를 이용하여 사이트에 설정된 언어별로 텍스트를 반환한다.
	 * 코드에 해당하는 문자열이 없을 경우 1차적으로 package.json 에 정의된 기본언어셋의 텍스트를 반환하고, 기본언어셋 텍스트도 없을 경우에는 코드를 그대로 반환한다.
	 *
	 * @param string $code 언어코드
	 * @param string $replacement 일치하는 언어코드가 없을 경우 반환될 메세지 (기본값 : null, $code 반환)
	 * @return string $language 실제 언어셋 텍스트
	 */
	function getText($code,$replacement=null) {
		if ($this->lang == null) {
			if (is_file($this->getPlugin()->getPath().'/languages/'.$this->IM->language.'.json') == true) {
				$this->lang = json_decode(file_get_contents($this->getPlugin()->getPath().'/languages/'.$this->IM->language.'.json'));
				if ($this->IM->language != $this->getPlugin()->getPackage()->language && is_file($this->getPlugin()->getPath().'/languages/'.$this->getPlugin()->getPackage()->language.'.json') == true) {
					$this->oLang = json_decode(file_get_contents($this->getPlugin()->getPath().'/languages/'.$this->getPlugin()->getPackage()->language.'.json'));
				}
			} elseif (is_file($this->getPlugin()->getPath().'/languages/'.$this->getPlugin()->getPackage()->language.'.json') == true) {
				$this->lang = json_decode(file_get_contents($this->getPlugin()->getPath().'/languages/'.$this->getPlugin()->getPackage()->language.'.json'));
				$this->oLang = null;
			}
		}
		
		$returnString = null;
		$temp = explode('/',$code);
		
		$string = $this->lang;
		for ($i=0, $loop=count($temp);$i<$loop;$i++) {
			if (isset($string->{$temp[$i]}) == true) {
				$string = $string->{$temp[$i]};
			} else {
				$string = null;
				break;
			}
		}
		
		if ($string != null) {
			$returnString = $string;
		} elseif ($this->oLang != null) {
			if ($string == null && $this->oLang != null) {
				$string = $this->oLang;
				for ($i=0, $loop=count($temp);$i<$loop;$i++) {
					if (isset($string->{$temp[$i]}) == true) {
						$string = $string->{$temp[$i]};
					} else {
						$string = null;
						break;
					}
				}
			}
			
			if ($string != null) $returnString = $string;
		}
		
		$this->IM->fireEvent('afterGetText',$this->getPlugin()->getName(),$code,$returnString);
		
		/**
		 * 언어셋 텍스트가 없는경우 iModule 코어에서 불러온다.
		 */
		if ($returnString != null) return $returnString;
		elseif (in_array(reset($temp),array('text','button','action')) == true) return $this->IM->getText($code,$replacement);
		else return $replacement == null ? $code : $replacement;
	}
	
	/**
	 * 상황에 맞게 에러코드를 반환한다.
	 *
	 * @param string $code 에러코드
	 * @param object $value(옵션) 에러와 관련된 데이터
	 * @param boolean $isRawData(옵션) RAW 데이터 반환여부
	 * @return string $message 에러 메세지
	 */
	function getErrorText($code,$value=null,$isRawData=false) {
		$message = $this->getText('error/'.$code,$code);
		if ($message == $code) return $this->IM->getErrorText($code,$value,null,$isRawData);
		
		$description = null;
		switch ($code) {
			default :
				if (is_object($value) == false && $value) $description = $value;
		}
		
		$error = new stdClass();
		$error->message = $message;
		$error->description = $description;
		$error->type = 'BACK';
		
		if ($isRawData === true) return $error;
		else return $this->IM->getErrorText($error);
	}
	
	/**
	 * 템플릿 정보를 가져온다.
	 *
	 * @return string $package 템플릿 정보
	 */
	function getTemplet() {
		return $this->getPlugin()->getTemplet($this->getPlugin()->getConfig('templet'));
	}
}
?>