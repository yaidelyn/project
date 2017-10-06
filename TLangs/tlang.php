<?php
session_start();
class tlang{
	public function __construct($default_lang = ""){
        if (!isset($_SESSION["current_lang"])){
            $lng = $this->get_language();
            if ($default_lang == "")
                $default_lang = $lng;
            $_SESSION["current_lang"] = $default_lang;
            //$this->readXMLLang($default_lang);
        }
    }
	
	public function get_language($feature = ""){
		$a_languages = $this->languages();
		$index = $complete = '';
		$user_languages = array();
		if(isset($_SERVER["HTTP_ACCEPT_LANGUAGE"])){
			$languages = strtolower($_SERVER["HTTP_ACCEPT_LANGUAGE"]);
			$languages = str_replace(' ', '', $languages);
			$languages = explode(",", $languages);
			foreach($languages as $language_list){
				$temp_array = array();
				$temp_array[0] = substr($language_list, 0, strcspn( $language_list, ';'));
				$temp_array[1] = substr($language_list, 0, 2);
				$user_languages[] = $temp_array;
			}
			for($i = 0; $i < count($user_languages); $i++){
				foreach ($a_languages as $index => $complete){
					if ($index == $user_languages[$i][0]){
						$user_languages[$i][2] = $complete;
						$user_languages[$i][3] = substr($complete, 0, strcspn($complete, ' ('));
					}
				}
			}
		}
		else
			$user_languages[0] = array( '','','','' );
		if ($feature == 'data')
			return $user_languages;
		else 
			return (!empty($user_languages)) ? $user_languages[0][1] : "it";
	}
 
	private function languages(){
		$a_languages = array(
		'af' => 'Afrikaans',
		'sq' => 'Albanian',
		'ar-dz' => 'Arabic (Algeria)',
		'ar-bh' => 'Arabic (Bahrain)',
		'ar-eg' => 'Arabic (Egypt)',
		'ar-iq' => 'Arabic (Iraq)',
		'ar-jo' => 'Arabic (Jordan)',
		'ar-kw' => 'Arabic (Kuwait)',
		'ar-lb' => 'Arabic (Lebanon)',
		'ar-ly' => 'Arabic (libya)',
		'ar-ma' => 'Arabic (Morocco)',
		'ar-om' => 'Arabic (Oman)',
		'ar-qa' => 'Arabic (Qatar)',
		'ar-sa' => 'Arabic (Saudi Arabia)',
		'ar-sy' => 'Arabic (Syria)',
		'ar-tn' => 'Arabic (Tunisia)',
		'ar-ae' => 'Arabic (U.A.E.)',
		'ar-ye' => 'Arabic (Yemen)',
		'ar' => 'Arabic',
		'hy' => 'Armenian',
		'as' => 'Assamese',
		'az' => 'Azeri',
		'eu' => 'Basque',
		'be' => 'Belarusian',
		'bn' => 'Bengali',
		'bg' => 'Bulgarian',
		'ca' => 'Catalan',
		'zh-cn' => 'Chinese (China)',
		'zh-hk' => 'Chinese (Hong Kong SAR)',
		'zh-mo' => 'Chinese (Macau SAR)',
		'zh-sg' => 'Chinese (Singapore)',
		'zh-tw' => 'Chinese (Taiwan)',
		'zh' => 'Chinese',
		'hr' => 'Croatian',
		'cs' => 'Czech',
		'da' => 'Danish',
		'div' => 'Divehi',
		'nl-be' => 'Dutch (Belgium)',
		'nl' => 'Dutch (Netherlands)',
		'en-au' => 'English (Australia)',
		'en-bz' => 'English (Belize)',
		'en-ca' => 'English (Canada)',
		'en-ie' => 'English (Ireland)',
		'en-jm' => 'English (Jamaica)',
		'en-nz' => 'English (New Zealand)',
		'en-ph' => 'English (Philippines)',
		'en-za' => 'English (South Africa)',
		'en-tt' => 'English (Trinidad)',
		'en-gb' => 'English (United Kingdom)',
		'en-us' => 'English (United States)',
		'en-zw' => 'English (Zimbabwe)',
		'en' => 'English',
		'us' => 'English (United States)',
		'et' => 'Estonian',
		'fo' => 'Faeroese',
		'fa' => 'Farsi',
		'fi' => 'Finnish',
		'fr-be' => 'French (Belgium)',
		'fr-ca' => 'French (Canada)',
		'fr-lu' => 'French (Luxembourg)',
		'fr-mc' => 'French (Monaco)',
		'fr-ch' => 'French (Switzerland)',
		'fr' => 'French (France)',
		'mk' => 'FYRO Macedonian',
		'gd' => 'Gaelic',
		'ka' => 'Georgian',
		'de-at' => 'German (Austria)',
		'de-li' => 'German (Liechtenstein)',
		'de-lu' => 'German (Luxembourg)',
		'de-ch' => 'German (Switzerland)',
		'de' => 'German (Germany)',
		'el' => 'Greek',
		'gu' => 'Gujarati',
		'he' => 'Hebrew',
		'hi' => 'Hindi',
		'hu' => 'Hungarian',
		'is' => 'Icelandic',
		'id' => 'Indonesian',
		'it-ch' => 'Italian (Switzerland)',
		'it' => 'Italian (Italy)',
		'ja' => 'Japanese',
		'kn' => 'Kannada',
		'kk' => 'Kazakh',
		'kok' => 'Konkani',
		'ko' => 'Korean',
		'kz' => 'Kyrgyz',
		'lv' => 'Latvian',
		'lt' => 'Lithuanian',
		'ms' => 'Malay',
		'ml' => 'Malayalam',
		'mt' => 'Maltese',
		'mr' => 'Marathi',
		'mn' => 'Mongolian (Cyrillic)',
		'ne' => 'Nepali (India)',
		'nb-no' => 'Norwegian (Bokmal)',
		'nn-no' => 'Norwegian (Nynorsk)',
		'no' => 'Norwegian (Bokmal)',
		'or' => 'Oriya',
		'pl' => 'Polish',
		'pt-br' => 'Portuguese (Brazil)',
		'pt' => 'Portuguese (Portugal)',
		'pa' => 'Punjabi',
		'rm' => 'Rhaeto-Romanic',
		'ro-md' => 'Romanian (Moldova)',
		'ro' => 'Romanian',
		'ru-md' => 'Russian (Moldova)',
		'ru' => 'Russian',
		'sa' => 'Sanskrit',
		'sr' => 'Serbian',
		'sk' => 'Slovak',
		'ls' => 'Slovenian',
		'sb' => 'Sorbian',
		'es-ar' => 'Spanish (Argentina)',
		'es-bo' => 'Spanish (Bolivia)',
		'es-cl' => 'Spanish (Chile)',
		'es-co' => 'Spanish (Colombia)',
		'es-cr' => 'Spanish (Costa Rica)',
		'es-do' => 'Spanish (Dominican Republic)',
		'es-ec' => 'Spanish (Ecuador)',
		'es-sv' => 'Spanish (El Salvador)',
		'es-gt' => 'Spanish (Guatemala)',
		'es-hn' => 'Spanish (Honduras)',
		'es-mx' => 'Spanish (Mexico)',
		'es-ni' => 'Spanish (Nicaragua)',
		'es-pa' => 'Spanish (Panama)',
		'es-py' => 'Spanish (Paraguay)',
		'es-pe' => 'Spanish (Peru)',
		'es-pr' => 'Spanish (Puerto Rico)',
		'es-us' => 'Spanish (United States)',
		'es-uy' => 'Spanish (Uruguay)',
		'es-ve' => 'Spanish (Venezuela)',
		'es' => 'Spanish (Traditional Sort)',
		'sx' => 'Sutu',
		'sw' => 'Swahili',
		'sv-fi' => 'Swedish (Finland)',
		'sv' => 'Swedish',
		'syr' => 'Syriac',
		'ta' => 'Tamil',
		'tt' => 'Tatar',
		'te' => 'Telugu',
		'th' => 'Thai',
		'ts' => 'Tsonga',
		'tn' => 'Tswana',
		'tr' => 'Turkish',
		'uk' => 'Ukrainian',
		'ur' => 'Urdu',
		'uz' => 'Uzbek',
		'vi' => 'Vietnamese',
		'xh' => 'Xhosa',
		'yi' => 'Yiddish',
		'zu' => 'Zulu' );
		return $a_languages;
	}

	public function changeLang($new_lang){
		//print_r($_SESSION); die;
		//echo $new_lang."-->".$_SESSION["current_lang"]; die;
        if ($_SESSION["current_lang"]!= $new_lang){
            $_SESSION["current_lang"] = $new_lang;
            $this->readXMLLang($new_lang);
        }
		else{
			$langs = (isset($_SESSION["tlang"])) ? $_SESSION["tlang"] : array();
			$array = array(); $x = 0;
			foreach($langs as $x => $v){
				$array[$x]["key"] = $v["key"];
				$array[$x]["value"] = $v["value"];
				$x++;
			}
			echo json_encode(array('result'=>$array));
		}
    }

    public function current_Lang(){
		$array = $this->readXMLLang($_SESSION["current_lang"], 1);
		echo json_encode(array('data'=>array('d'=>$_SESSION["current_lang"], 'result'=>$array)));
    }
	
	public function readXMLLang($lang, $mode = 0){
		$array = array(); $x = 0;
        if ($lang != "it" && $lang != "en")
            $lang = $_SESSION["current_lang"] = "it";
		$path = (file_exists("XML/tlang-$lang.xml")) ? "XML/tlang-$lang.xml" : "XML/tlang-it.xml";
        $xml = simplexml_load_file($path);
		foreach($xml->children() as $child){
			foreach($child as $key => $value){
				$array[$x]["key"] = $_SESSION["tlang"][$x]["key"] = $key;
				$val = (string) $value;
				if ($lang == "it"){
					$val = str_replace("Ãš", "|", $val);
					if (strpbrk($val, "à") !== false)
						$val = utf8_decode($val);
					$array[$x]["value"] = $_SESSION["tlang"][$x]["value"] = str_replace("|", "è", $val);
				}
				else
					$array[$x]["value"] = $_SESSION["tlang"][$x]["value"] = $val;
				$x++;
			}
		}
		if ($mode)
			return $array;
		else
			echo json_encode(array('result'=>$array));
    }
	
	function xml_attribute($object, $attribute){
		if(isset($object[$attribute]))
			return (string) $object[$attribute];
	}
	
	public function get(){
		if(!empty($_POST)){
			if (isset($_POST['fn'])){
				switch($_POST['fn']){
					case "getLang":
						$this->current_Lang();
					break;
					case "changeLang":
						$this->changeLang($_POST['lang']);
					break;
				}
			}
		}
    }
}

$tlang = new tlang();
$tlang->get($_POST);
?>