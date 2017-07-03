<?php

define('TERM_RED', "\033[91m");
define('TERM_BLUE', "\033[36m");
define('TERM_YELLOW', "\033[93m");
define('TERM_GREEN', "\033[92m");
define('TERM_VIOLET', "\033[95m");
define('TERM_GRAY', "\033[30m");

define('TERM_COLOR_CLOSE', "\033[0m");

function is_urn($purn){
    if (is_object($purn)) $purn = (string) $purn;
    $urna = explode('-', $purn);
    if (count($urna) < 2 || count($urna) > 4 || $urna[0] != 'urn') return false;
    return true;
}

function _option($nskeypath)
{
    $v = Config::value($nskeypath);
    if (get_class($v) == 'String')
        return $v->string;
    else
        return $v;
}

function _str($keypath, $lang=null)
{
    //if (!$lang) throw new Exception("_str({$keypath}, lang) lang not provided");
    if (!$lang) $lang = SystemLocale::$REQUEST_LANG;
    return Config::get('strings/'.$lang, $keypath);
}

function moneyFormatSimple($p)
{
    $p = (float) $p;
    $ip = (int) $p;
    if ($p == $ip)
        return $ip;
    else
        return money_format('%!n', $p);
}

function callerFirstParam($callers = null)
{
	$stack = 0;
	if (!$callers)
	{
		$callers = debug_backtrace();
		$stack++;
	}
	$lines = file($callers[$stack]['file']);
	$lineum = $callers[$stack]['line'] - 1;
	$bs = explode('(', $lines[$lineum]);
	$ps = explode(',', $bs[1]);
	if (count($ps) == 2)
	{
		return $ps[0];
	}
	else
	{
		$ps1 = explode(')', $ps[0]);
		return $ps1[0];
	}
}

function txt2boolean($txt)
{
	if ($txt == 'yes') return true;
	if ($txt == 'true') return true;
	if ($txt == '1') return true;
	return false;
}

function isURN($urn)
{
	if ($urn instanceof URN) return true;
	if (substr($urn,0,3) == 'urn') return 0;
	return false;
}
function isUUID($uuid)
{
	if ($uuid instanceof UUID) return true;
	return false;
}

function wrapon($d, $w1, $w2)                            
{
	if ($d) return $w1.$d.$w2;
}

function ajaxRequest($url, $post=null)
{
    $d = httpRequest($url, $post);
    return json_decode($d['data'], false); // return Object
}

/**
 TODO conf timeout, Cookie per session not global!
 */
function httpRequest($url, $post=null)
{
    $httpMethod = 'GET';
    //$cookie_jar = tempnam('/tmp','cookie');
    $url = (string) $url;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, 1); // To get only the headers use CURLOPT_NOBODY
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");
    curl_setopt($ch, CURLOPT_COOKIEJAR, '/tmp/cookie.php.dat');
    curl_setopt($ch, CURLOPT_COOKIEFILE, '/tmp/cookie.php.dat');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // http://unitstep.net/blog/2009/05/05/using-curl-in-php-to-access-https-ssltls-protected-sites/
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    if (count($post))
    {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        $httpMethod = 'POST';
    }
    $output = curl_exec($ch);
    if (curl_errno($ch))
    {
        $error = curl_error($ch).'('.curl_errno($ch).')';
        curl_close($ch);
        throw new Exception($error);
    }
    // {httpcode: 200, url: '/login', effectiveurl: '/account', 'totaltime': 2, data: '<html>', 'headers': [k:v,..], redirectcount: 1, receivedbytes: 1000, 'method': post, 'contenttype': 'html'}
    $meta = array();
    $meta['effectiveurl'] = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    $meta['httpcode'] = (integer) curl_getinfo($ch, CURLINFO_HTTP_CODE); // last
    $meta['totaltime'] = (float) curl_getinfo($ch, CURLINFO_TOTAL_TIME);
    $meta['redirectcount'] = (integer) curl_getinfo($ch, CURLINFO_REDIRECT_COUNT);
    $meta['receivedbytes'] = (integer) curl_getinfo($ch, CURLINFO_SIZE_DOWNLOAD);
    $meta['contenttype'] = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
    $headersBytes = (integer) curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $meta['url'] = $url;
    $meta['method'] = $httpMethod;
    $header = substr($output, 0, $headersBytes);
    $body = substr($output, $headersBytes);
    $headersarray = explode("\r\n", $header);
    $headersclean = array();
    foreach ($headersarray as $headervalue)
    {
        $hstruct = explode(':', $headervalue); //$headerkey
        if ($hstruct[0] && $hstruct[1])
            $headersclean[$hstruct[0]] = $hstruct[1];
    }
    $meta['headers'] = $headersclean;
    $meta['data'] = $body;
    //unset($body);
    curl_close($ch);
    if ($meta['httpcode'] == 200)
    {
        // $meta['contenttype'] == 'text/html'
        $aa = explode(';', $meta['contenttype']);
        if (count($aa) == 1)
        {
            if ($meta['contenttype'] == 'text/html') $ishtml = true;
        }
        else { // 2 or more
            if ($aa[0] == 'text/html') $ishtml = true;
            $aa[1] = trim($aa[1]);
            //println($aa[1],1,TERM_RED);
            $enc = explode('=', $aa[1]);
            //println($enc);
            if ($enc[0] == 'charset')
            {
                //println($enc[1],1,TERM_YELLOW);
                if ($enc[1] == 'windows-1251')
                {
                    $meta['data'] = mb_convert_encoding($meta['data'], "utf-8", "windows-1251");
                }
            }
        }

        if ($ishtml) {
            $d = new DOMDocument;
            $d->loadHTML($body);
            $meta['html'] = $d;
        }
    }
    return $meta;
}


/**
http://www.pagood.ru/seo/pravila-transliteracii-urlov-yandeks-translit-i-gugl-translit/
http://www.rezonans.ru/lab/tablica-translita.html
UKR http://dictumfactum.com.ua/ru/infopoint/61-translit
Ї	
I	
Yi - в начале слова, і - в других позициях	
Їжакевич - Yizhakevych;Кадіївка - Kadiivka

Й	
Y, i	
Y - в начале слова, і - в других позициях	
Йосипівка - Yosypivka;Стрий - Stryi
Є	
Ye, ie	
Ye - в начале слова, іе - в других позициях	
Єнакієве - Yenakiieve;Наєнко - Naienko
Г	
H, gh	
Н - в большинстве случаев,

gh - когда    встречается комбинация “зг”	
Гадяч - Hadiach;Згорани - Zghorany
Ю	
Yu, iu	
Yu - в начале слова, iu - в других позициях	
Юрій - Yurii;Крюківка - Krukivka

Я	
Ya, ia	
Ya - в начале слова, іа - в других позициях	
Яготин - Yahotyn;Iчня - Ichnia

‘ (апостроф)	
“	
(см. пример)	
Знам’янка - Znamianka
*/

// cyrillic
/**
TODO ukr spcific for letters in head of word
OPTION SAVE ' in ukr
*/
function translit($str, $lang='ru') 
{
	
	/**
	$lang = Text::langDetect($str);
	*/
	if (!$lang) $lang = 'ru';
	
	if ($lang == 'ru')
	{
		$tr = array(
        "А"=>"A","Б"=>"B","В"=>"V","Г"=>"G",
        "Д"=>"D","Е"=>"E","Ж"=>"J","З"=>"Z","И"=>"I",
        "Й"=>"Y","К"=>"K","Л"=>"L","М"=>"M","Н"=>"N",
        "О"=>"O","П"=>"P","Р"=>"R","С"=>"S","Т"=>"T",
        "У"=>"U","Ф"=>"F","Х"=>"H","Ц"=>"C","Ч"=>"Ch",
        "Ш"=>"Sh","Щ"=>"Sch","Ю"=>"U","Я"=>"Ya",
        
        "Ъ"=>"","Ы"=>"Y","Ь"=>"","Ё"=>"E","Э"=>"E", // RU SPECIFIC
        "Ґ"=>"G","І"=>"I","Ї"=>"I","Є"=>"E", // UKR in ru
        
        "а"=>"a","б"=>"b",
        "в"=>"v","г"=>"g","д"=>"d","е"=>"e","ж"=>"j",
        "з"=>"z","и"=>"i","й"=>"y","к"=>"k","л"=>"l",
        "м"=>"m","н"=>"n","о"=>"o","п"=>"p","р"=>"r",
        "с"=>"s","т"=>"t","у"=>"u","ф"=>"f","х"=>"h",
        "ц"=>"c","ч"=>"ch","ш"=>"sh","щ"=>"sch",
        "ю"=>"u","я"=>"ya",
        
        "ъ"=>"","ь"=>"","ы"=>"y","э"=>"e","ё"=>"e", // ru specific
        "є"=>"e","ї"=>"i","і"=>"i","ґ"=>"g", // ukr in ru
        
        " "=> "-", "."=> "", "/"=> "~", "*"=> "~", "№"=> "~"
        );
    }
    else if ($lang == 'ua')
	{
		$tr = array(
        "А"=>"A","Б"=>"B","В"=>"V","Г"=>"G",
        "Д"=>"D","Е"=>"E","Ж"=>"J","З"=>"Z","И"=>"Y",
        "Й"=>"Y","К"=>"K","Л"=>"L","М"=>"M","Н"=>"N",
        "О"=>"O","П"=>"P","Р"=>"R","С"=>"S","Т"=>"T",
        "У"=>"U","Ф"=>"F","Х"=>"H","Ц"=>"C","Ч"=>"Ch",
        "Ш"=>"Sh","Щ"=>"Sch","Ю"=>"U","Я"=>"Ya",
        
        "Ґ"=>"G","І"=>"I","Ї"=>"I","Є"=>"E", // UKR SPECIFIC
        "Ъ"=>"","Ы"=>"Y","Ь"=>"","Ё"=>"E","Э"=>"E", // RU in ua
        
        "а"=>"a","б"=>"b",
        "в"=>"v","г"=>"g","д"=>"d","е"=>"e","ж"=>"j",
        "з"=>"z","и"=>"u","й"=>"y","к"=>"k","л"=>"l",
        "м"=>"m","н"=>"n","о"=>"o","п"=>"p","р"=>"r",
        "с"=>"s","т"=>"t","у"=>"u","ф"=>"f","х"=>"h",
        "ц"=>"c","ч"=>"ch","ш"=>"sh","щ"=>"sch",
        "ь"=>"","ю"=>"u","я"=>"ya",
        
        "є"=>"e","ї"=>"i","і"=>"i","ґ"=>"g", // ukr specific
        "ъ"=>"","ь"=>"","ы"=>"y","э"=>"e","ё"=>"e", // ru in ua
        
        " "=> "-", "."=> "", "/"=> "~", "*"=> "~", "№"=> "~"
        );
    }
	else // en, fr etc
	{
		$tr = array(
        	" "=> "-", "."=> "", "/"=> "~", "*"=> "~", "№"=> "~", "é" => 'e', "à" => 'a', "ç" => 'c', "ï" => 'i', "î" => 'i', "ô" => 'o', "ù" => 'u', "ÿ" => 'y'
        );
	}
    //$str = mb_strtoupper($str, 'UTF-8');
    $str = strtr($str, $tr);
    $str = preg_replace('/[^A-Za-z0-9_\-~]/', '', $str);
    $str = str_replace('__','_',$str);
    $str = str_replace('__','_',$str);
    $str = str_replace('--','-',$str);
    $str = str_replace('--','-',$str);
    $str = str_replace('_-_','_',$str);
    return $str;
}


function base64_encode_data_from_file($filename) {
	if (function_exists('finfo_file'))
		$filetype = finfo_file($filename);
	else	if (function_exists('mime_content_type'))
		$filetype = mime_content_type($filename);
	else
	{
		$i = getimagesize($filename);
		$filetype = $i['mime'];
	}
	$imgbinary = fread(fopen($filename, "r"), filesize($filename));
	return 'data:' . $filetype . ';base64,' . base64_encode($imgbinary);
}

function append_data_to_file($filename, $data)
{
	$file = fopen($filename, 'a+');
	if (flock($file, LOCK_EX)) 
	{
		fwrite($file, $data);
		flock($file, LOCK_UN);
	} 
	else
	{
	   throw new Exception("Couldn't get the WRITE EX lock for $filename file!");
	}
	fclose($file);
}

function save_data_as_file($filename, $data)
{
	$file = fopen($filename, 'w');
	if (flock($file, LOCK_EX)) 
	{
		fwrite($file, $data);
		flock($file, LOCK_UN);
	} 
	else
	{
	   throw new Exception("Couldn't get the WRITE EX lock for $filename file!");
	}
	fclose($file);
}

function base64_decode_file($base64)
{
	$comapos = strpos($base64,',');
	return base64_decode(substr($base64,++$comapos));
}

function domGetImageDimSize($domel)
{
	$eachside = $domel->getAttribute('eachside');
	$horizontal = $domel->getAttribute('horizontal');
	$vertical = $domel->getAttribute('vertical');
	$largestside = $domel->getAttribute('largestside');
	if ($eachside)
		$size = array('dim'=>'eachside','size'=>$eachside);
	elseif ($horizontal)
		$size = array('dim'=>'horizontal','size'=>$horizontal);
	elseif ($vertical)
		$size = array('dim'=>'vertical','size'=>$vertical);
	elseif ($largestside)
		$size = array('dim'=>'largestside','size'=>$largestside);
	return $size;
}

function get_time_difference( $start, $end )
{
    //$uts['start']      =    strtotime( $start );
    //$uts['end']        =    strtotime( $end );
    $uts['start']      =    $start;
    $uts['end']        =    $end;
    if( $uts['start']!==-1 && $uts['end']!==-1 )
    {
        if( $uts['end'] >= $uts['start'] )
        {
            $diff    =    $uts['end'] - $uts['start'];
            if( $days=intval((floor($diff/86400))) )
                $diff = $diff % 86400;
            if( $hours=intval((floor($diff/3600))) )
                $diff = $diff % 3600;
            if( $minutes=intval((floor($diff/60))) )
                $diff = $diff % 60;
            $diff    =    intval( $diff );            
            return( array('days'=>$days, 'hours'=>$hours, 'minutes'=>$minutes, 'seconds'=>$diff) );
        }
        else
        {
            trigger_error( "Ending date/time is earlier than the start date/time", E_USER_WARNING );
        }
    }
    else
    {
        trigger_error( "Invalid date/time data detected", E_USER_WARNING );
    }
    return( false );
}


function mysqldate2timestamp($mysqldate)
{
	$year = (integer) substr($mysqldate, 0, 4);
	$month = (integer) substr($mysqldate, 5, 2);
	$day = (integer) substr($mysqldate, 8, 2);
	$hour = (integer) substr($mysqldate, 11, 2);
	$min = (integer) substr($mysqldate, 14, 2);
	$sec = (integer) substr($mysqldate, 17, 2);
	return mktime($hour, $min, $sec, $month, $day, $year);
}





function detect_platform()
{
	if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
		return "WIN";
	} elseif (strtoupper(substr(PHP_OS, 0, 6)) === 'DARWIN') {
		return "OSX";
	} elseif (strtoupper(substr(PHP_OS, 0, 5)) === 'LINUX') {
		return "LINUX";
	} elseif (eregi('BSD', PHP_OS)) {
		return "BSD";
	} else {
		throw new Exception("Unknow system");
	}
}

function is_web_request()
{
	/**
	if(php_sapi_name() == 'cli'){
	 */
	return ( ($_SERVER['DOCUMENT_ROOT']) ? true : false );
}

/**
run script from text editor - vim or TextMate
 */
function in_console()
{
	//var_dump(is_web_request());
	return !is_web_request();
	// return ( (getenv('TM_DIRECTORY') || getenv('MYVIMRC')) ? false : true );
}

function under_win() 
{
	return ( detect_platform() == 'WIN' );
}

function printColor($s, $color)
{
	if (in_console() && !under_win()) echo $color;
	if ( is_web_request() && $color) {
		if ($color === TERM_RED)	$htmlcolor = 'red';
		if ($color === TERM_GREEN)	$htmlcolor = 'green';
		if ($color === TERM_YELLOW)	$htmlcolor = 'yellow';
		if ($color === TERM_BLUE)	$htmlcolor = 'lightblue';
		if ($color === TERM_VIOLET)	$htmlcolor = 'violet';
		if ($color === TERM_GRAY)	$htmlcolor = 'gray';
		print '<span style="color: '.$htmlcolor.'">';
	}
	echo $s;
	if (in_console() && !under_win()) echo TERM_COLOR_CLOSE;
	if ( is_web_request() && $color) echo '</span>';
}

function printml($s,$tag,$class)
{
	if ($class) $class = " class=\"$class\"";
	print "<{$tag}{$class}>$s</$tag>";
}

function printhref($href, $text, $id=null, $class=null)
{
	if ($class) $class = " class=\"$class\" ";
	if ($id) $id = " id=\"$id\" ";
	print "<a href=\"{$href}\"{$id}{$class}>$text</a>";
}

function dprintln($s, $level=0, $color=false)
{
	if (TEST_ENV === true)
		println($s, $level, $color);
}
function dprintlnd($s, $level=0, $color=false)
{
	if (TEST_ENV === true)
		printlnd($s, $level, $color);
}

function printlnd($s, $level=0, $color=false)
{
	if (is_object($s)) print '@' . get_class($s)." ";
	if (is_string($s) && !is_numeric($s)) print '@string ';
	if (is_string($s) && is_numeric($s)) print '@numeric_string ';
	if (is_int($s)) print '@int ';
	if (is_array($s)) print '@array ';
	println($s, $level, $color);
}

function println($s, $level=0, $color=false)
{
	if ( is_web_request() ) print "\n<pre>\n";
	if ($level > 0)
		for ($i=0;$i<$level-1;$i++) print "\t";
	
	if ($color !== false && in_console() && !under_win()) echo $color;
	if ( is_web_request() && $color) {
		if ($color === TERM_RED)	$htmlcolor = 'red';
		if ($color === TERM_GREEN)	$htmlcolor = 'green';
		if ($color === TERM_YELLOW)	$htmlcolor = 'yellow';
		if ($color === TERM_BLUE)	$htmlcolor = 'lightblue';
		if ($color === TERM_VIOLET)	$htmlcolor = 'violet';
		if ($color === TERM_GRAY)	$htmlcolor = 'gray';
		print '<span style="color: '.$htmlcolor.'">';
	}

	if (is_int($s))
		print $s;
	elseif (is_array($s))
		print json_encode($s);
    elseif (is_object($s) && get_class($s) == 'Message')
        print Utils::array_to_colored_json($s->get());
	elseif ($s === true)
		print '(TRUE)';
	else {
		if ($s === '') print "(EMPTY STRING)";
		else if ($s === false) print "(FALSE)";
		else if ($s === null) print "(NULL)";
		else print ltrim(rtrim($s));
	}

	if ($color !== false && in_console() && !under_win()) echo TERM_COLOR_CLOSE;
	if ( is_web_request() && $color) echo '</span>';
	if ( is_web_request() ) print "</pre>";
	print "\n";
}

function printLine()
{
	print "\n";
	if ( is_web_request() ) print "<hr>";
	else for ($i=0;$i<72;$i++) print "-";
	print "\n";
}

function printH($h)
{
	if ( is_web_request() ) print "<hr>";
	else for ($i=0;$i<72;$i++) print "-";
	print "\n";
	if ( is_web_request() ) print "<strong>";
	elseif (in_console() && !under_win()) echo "\033[93m";
	print strtoupper($h);
	if ( is_web_request() ) print "</strong>";
	elseif (in_console() && !under_win()) echo "\033[0m";
	print "\n";
	if ( is_web_request() ) print "<hr>";
	else for ($i=0;$i<72;$i++) print "-";
	print "\n";
}


function uuid($prefix = '')
{
	$chars = md5(uniqid(mt_rand(), true));
	$uuid  = substr($chars,0,8) . '-';
	$uuid .= substr($chars,8,4) . '-';
	$uuid .= substr($chars,12,4) . '-';
	$uuid .= substr($chars,16,4) . '-';
	$uuid .= substr($chars,20,12);
	return $prefix . $uuid;
}

function balanced_uuid()
{
	$chars = md5(uniqid(mt_rand(), true));
	$uuid  = substr($chars,0,1) . '/';
	$uuid .= substr($chars,1,1) . '/';
	$uuid .= substr($chars,2,1);
	return $uuid;
}

function short_uuid()
{
	$chars = md5(uniqid(mt_rand(), true));
	$uuid  = substr($chars,0,8);
	return $uuid;
}

function shortcode()
{
	//$chars = md5(uniqid(mt_rand(), true));
	//$uuid  = substr($chars,0,3) . rand(100,999);
	$uuid  = rand(100,999) . rand(100,999) . rand(100,999);
	return strtoupper($uuid);
}

function login_hash()
{
	$chars = md5(uniqid(mt_rand(), true));
	return substr($chars,0,32);
}

function is_json($s)
{
	if (is_string($s))
	{
		$s = trim($s);
		if (substr($s,0,1) == '{' || substr($s,0,1) == '[')
			return true;
	}
	return false;
}

function is_xml($s)
{
	if (is_string($s))
	{
		$s = trim($s);
		if (substr($s,0,1) == '<')
			return true;
	}
	return false;
}

function getRequestParameter($key, $default = null)
{
	if(isset($_REQUEST[$key])) return $_REQUEST[$key];
	else return $default;
}


// for array_filter
function morethenone($v)	{ return($v > 1); }
function morethenten($v)	{ return($v > 10); }

function cp1251toUT8($s)	{ return iconv('cp1251', 'UTF-8//IGNORE', $s);	}
function UTF8to1251($s)	{ return iconv('UTF-8', 'cp1251//IGNORE', $s);	}



function get_option($name, $field='name')
{
	$lang = SystemLocale::$REQUEST_LANG;
	$m = new Message();
	$m->action = 'load';
	$m->urn = "urn-options";
	$m->lang = $lang;
	$m->$field = $name;
	$o = $m->deliver();
	if ($o->count() != 1)
		return null;
	else
		return $o->current()->gtext;
}

function get_fragment($name)
{
	$lang = SystemLocale::$REQUEST_LANG;
	$m = new Message();
	$m->action = 'load';
	$m->urn = "urn-fragment";
	$m->lang = $lang;
	$m->name = $name;
	$o = $m->deliver();
	if ($o->count() != 1)
		return null;
	else
		return $o->current()->fullhtml;
}


function page_by_uri($uri)
{
	$m = new Message('{"action": "load"}');
	$m->urn = 'urn-page';
	$m->lang = SystemLocale::$REQUEST_LANG;
	$m->uri = $uri;
	return $m->deliver()->current();
}


function category_by_uri($uri)
{
	try
	{
		$m = new Message();
		$m->action = 'load';
		$m->urn = "urn-category";
		$m->uri = $uri;
		$m->last = 1;
		$m->lang = SystemLocale::$REQUEST_LANG;
		$r = $m->deliver();
		if (count($r)) return $r->current();
	}
	catch (Exception $e) 
	{
		//print "123 $e";
	}
	return null;
}
function category_by_urn($urn)
{
	$m = new Message('{"action": "load"}');
	$m->urn = $urn;
	$m->lang = SystemLocale::$REQUEST_LANG;
	return $m->deliver()->current();
}

function page_by_urn($urn)
{
	$m = new Message('{"action": "load"}');
	$m->lang = SystemLocale::$REQUEST_LANG;
	$m->urn = $urn;
	return $m->deliver()->current();
}

function urn($urn_string)
{
	if (is_object($urn_string))
		return $urn_string;
	else if (is_string($urn_string))
	{
		if (substr($urn_string,0,3)=='urn')
			return new URN($urn_string);
		else
			return null;
	}
	else
		return null;
}

function tofloat($num) {
    $dotPos = strrpos($num, '.');
    $commaPos = strrpos($num, ',');
    $sep = (($dotPos > $commaPos) && $dotPos) ? $dotPos :
        ((($commaPos > $dotPos) && $commaPos) ? $commaPos : false);

    if (!$sep) {
        return floatval(preg_replace("/[^0-9]/", "", $num));
    }

    return floatval(
        preg_replace("/[^0-9]/", "", substr($num, 0, $sep)) . '.' .
        preg_replace("/[^0-9]/", "", substr($num, $sep+1, strlen($num)))
    );
}
/*
$num = '1.999,369€';
var_dump(tofloat($num)); // float(1999.369)
$otherNum = '126,564,789.33 m²';
var_dump(tofloat($otherNum)); // float(126564789.33)
*/

function load_widget($widget_name, $options)
{
	$widget_file = BASE_DIR."/widgets/{$widget_name}.php";
	if (!file_exists($widget_file))
		return "ERROR: {$widget_name} not exists <br>\n";
	ob_start();
	include_once $widget_file;	
	$widget_function_name = "widget_{$widget_name}";
	if (function_exists($widget_function_name))
	{
		$html = call_user_func($widget_function_name, $options);
		echo $html; 
	}
	//else Log::error("name: {$widget_name}, FN not exists: widget_{$widget_name}", 'widget');
	$outbuffer = ob_get_clean();
	ob_end_clean();
	//Log::info(substr($outbuffer,0,100),'widgets');
	return $outbuffer;	
}

function is_64bit()
{
	$int = "9223372036854775807";
	$int = intval($int);
	if ($int == 9223372036854775807)
		return true;
	elseif ($int == 2147483647)
		return false;
	else 
		return null;
}


function isCli()
{
	if(php_sapi_name() == 'cli' && empty($_SERVER['REMOTE_ADDR']))
		return true;
	else
		return false;
}

function ConditionalHttpGet($last_modified, $etag) 
{
	//var_dump($last_modified);
	//var_dump($etag);
    $if_modified_since = isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ? stripslashes($_SERVER['HTTP_IF_MODIFIED_SINCE']) : false;
    $if_none_match = isset($_SERVER['HTTP_IF_NONE_MATCH']) ? stripslashes($_SERVER['HTTP_IF_NONE_MATCH']) : false;
    if (!$if_modified_since && !$if_none_match) 
	{
		//echo '!$if_modified_since && !$if_none_match IN REQUEST';
        return true;
    }
    // хотя бы один из заголовков передан на проверку
    if ($if_none_match && $if_none_match != $etag) {
        //echo "// etag есть, но не совпадает";
    		return true; // etag есть, но не совпадает
    }
    if ($if_modified_since && $if_modified_since != $last_modified) {
    		//echo "if-modified-since есть, но не совпадает";
        return true; // if-modified-since есть, но не совпадает
    }
    // контент не изменился
    return false;
}

function http_modified_date($ts)
{
	return date('D, d M Y H:i:s O', $ts);		
}








/**
All Elements	*	//*
All P Elements	p	//p
All Child Elements	p > *	//p/*
Element By ID	#foo		//*[@id='foo']
Element By Class	.foo		//*[contains(@class,'foo')] 1
Element With Attribute	*[title]		//*[@title]
First Child of All P	p > *:first-child	//p/*[0]
All P with an A child	Not possible	//p[a]
Next Element	p + *	//p/following-sibling::*[0]
*/

function domInnerHTML($element) 
{ 
    $innerHTML = ""; 
    $children = $element->childNodes; 
    foreach ($children as $child) 
    { 
        $tmp_dom = new DOMDocument("1.0","UTF-8"); 
        $tmp_dom->appendChild($tmp_dom->importNode($child, true)); 
        $innerHTML .= trim($tmp_dom->saveHTML()); // saveXML! 
    } 
    return $innerHTML; 
} 

function node2doc($n)
{
	$t = new DOMDocument("1.0","UTF-8"); 
	$t->appendChild($t->importNode($n, true));
	return $t;
	//$originDocNode->appendChild($originDoc->importNode($tmpDoc->documentElement, true));
}

/**
TODO <p class='title'>Title <span class='time'>WIPED!</span></p>
TODO join('. ', arrayofvals) make html logic
*/
function recursiveKeyDataFragmentRenderer(DOMDocument $f, $d, $k = null, $pk = null)
{
	$debug = false;
	if ($debug) print "<blockquote>";
	if ($debug) print "<h3>recursiveKeyDataFragmentRenderer $k</h3>";
	if ($k && is_array($d)) $datalocal = $d[$k];
	else if ($k && is_object($d)) $datalocal = $d->$k;
	else $datalocal = $d;
	if (is_array($datalocal))
	{
		if ($debug) print "<font color='red'>is_array(datalocal) $k</font>\n";
		$els = getListContainer($f, $k);
		if ($els->length == 1)
		{
			$group = getListGroupper($f, $k);
			if ($group->length)
			{
				$groupped = true;
				$groupsProcessed = 0;
				$g = $group->item(0);
				$gp = $g->parentNode;
				$groupBy = $g->getAttribute('data-groupby');
				$skipFirstGroup = ($g->getAttribute('data-skipfirstgroupheader') == 'yes') ? true : false;
				$gp->removeChild($g);
			}
			$e = $els->item(0);
			if (!$k) $e->removeAttribute('data-list');
			if ($debug) echo "[A:". $e->nodeName. ' @ ' . $e->getAttribute('class')  . "]\n";
			$pn = $e->parentNode;
			// REMOVE LIST-ORIGINAL-CONTAINER FROM MAIN DOC
			$pn->removeChild($e);
			if ($debug) echo '<font color=silver size=-1>'.htmlspecialchars($f->saveXML($f->documentElement)).'</font>';
			// EACH LIST DATA
			foreach ($datalocal as $v)
			{
				if ($groupped)
				{
					$groupCriteriaValue = $v->$groupBy;
					if ($groupCriteriaValue != $prevGroupCriteriaValue)
					{
						$groupsProcessed++;
						if (!($groupsProcessed == 1 && $skipFirstGroup))
						{
							$fg = node2doc($g);
							queryReplaceKeyInFragment($fg, 'grouptitle', $groupCriteriaValue); // add data-prepend=text data-append=text
							$gp->appendChild($f->importNode($fg->documentElement, true));
						}
					}
					$prevGroupCriteriaValue = $groupCriteriaValue;
				}
				// CREATE TEMP DOC LIST ELEMENT AS CLONE OF LIST CONTAINER ORIGINAL
				$t = new DOMDocument("1.0","UTF-8");
				$t->formatOutput = true;
				$t->appendChild($t->importNode($e, true));
				$t->firstChild->setAttribute('data-item',++$k);
				if ($debug) echo '<font color=green>'.htmlspecialchars($t->saveXML($t->documentElement)).'</font>';
				// DATA TO TEMP DOC LIST ELEMENT
				recursiveKeyDataFragmentRenderer($t, $v);
				// APPEND LIST ELEMENT TO MAIN DOC
				if ($debug) print "<font color=red>APPEND TO MAIN</font>";
				if ($debug) echo '<font color=olive>'.htmlspecialchars($t->saveXML($t->documentElement)).'</font>';
				//if ($groupped) $pn->appendChild($g);
				$pn->appendChild($f->importNode($t->documentElement, true));
				if ($debug) echo '<font color=silver>'.htmlspecialchars($f->saveXML($f->documentElement)).'</font>';
			}
		}
		else
		{
			if ($debug) print debugDom($f);
			if ($els->length > 1)
			{
				if ($debug)  
				{
					foreach ($els as $e) echo "[ER:". $e->nodeName. ' @ ' . $e->getAttribute('class') . "]\n";
					throw new Exception("[data-list=$k] in DOM found more then one");
				}
			}
			else
			{
				if ($debug) throw new Exception("[data-list=$k] in DOM not found");
			}
		}
	} // DEEP: {}
	else if (is_object($datalocal) && $k) { // на первом проходе данные являются объектом, а нам нужны вложенные объекты
		// FOCUS ON PART OF DOM NAMED AS OBJECT KEY ENTRY (.., image: {..}, ..) <news><_image_><img>
		$els = getElementsByAny($f, $k);
		if ($debug) print "<font color='orange'>DEEP is_object(datalocal) && k $k elsCount: {$els->length}</font>\n";
		if ($els->length)
		{
			$e = $els->item(0);
			if ($debug) echo "[++". $e->nodeName. ' @ ' . $e->getAttribute('class') . "]\n";
			$cwn = node2doc($e); // newDomDocumentFromNode or node2doc
		}
		else
		{
			$cwn = $f;
		}
		foreach ($datalocal as $kk => $v) 
		{
			if ($debug) print("<i>D:$kk</i>, \n");
			recursiveKeyDataFragmentRenderer($cwn, $datalocal, $kk, $k);
			if ($els->length)
			{
				if ($debug) echo '<font color=brown>'.htmlspecialchars($cwn->saveXML($cwn->documentElement)).'</font>';
				if ($debug) echo '<font color=gold>'.htmlspecialchars($f->saveXML($e)).'</font>';
				//$e->parentNode->replaceChild($f->importNode($cwn->documentElement, true), $e);
			}
		}
		if ($els->length) $e->parentNode->replaceChild($f->importNode($cwn->documentElement, true), $e);
		//else $e->parentNode->replaceChild($f->importNode($cwn->documentElement, true), $e);
	}
	else if (is_object($datalocal) && !$k) {
		if ($debug) print("ROOT OBJECT is_object(\$datalocal) && \$k\n");
		//var_dump($datalocal);
		foreach ($datalocal as $kr => $v)
		{
			if ($debug) print("<i>r:$kr</i>, \n");
			recursiveKeyDataFragmentRenderer($f, $datalocal, $kr, $k);
		}
	}
	else // final object for keys replace {title: etc}
	{
		if ($debug) print "FINAL KEY $k of $pk <br>\n";
		//var_dump($k, $f, $datalocal, $pk);
		if ($datalocal)
			$replacedCount = queryReplaceKeyInFragment($f, $k, $datalocal, $pk);
		else
			queryRemoveElementByKeyInFragment($f, $k);
	}
	if ($debug) print "</blockquote>";
}

function queryRemoveElementByKeyInFragment($f, $k)
{
	$els = getElementsByAny($f, $k);
	foreach ($els as $e)
	{
		if ($e->hasAttribute($k))
			$e->removeAttribute($k);
		else	
			$e->parentNode->removeChild($e);
	}
	return $els->length;
}

// [data-selector="k"] > setAttribute | innerhtml
// [attr] > setAttribute
// .class > innerhtml
// #id > innerhtml
// + <tagname match?
function queryReplaceKeyInFragment($f, $k, $value, $pk)
{
	$debug = false;
	//if ($debug) print "<b>queryReplaceKeyInFragment $k of PK($pk) </b> value: "; //var_dump($f, $k, $value, $pk);
	//if ($debug) var_dump($k,$value);
	$els = getElementsByAny($f, $k); // getElementsByClassname($f, $k, $pk)
	foreach ($els as $e)
	{
		$writeTo = $e->getAttribute('data-write');
		//if ($debug) var_dump($e->hasAttribute($k));
		$writeAttrib = $k;
		if ($writeTo) $writeAttrib = $writeTo;
		if ($debug) echo "[". $e->nodeName. " @ $k " . $writeTo . ':' . $writeAttrib . "]\n";
		if ($e->hasAttribute($writeAttrib))
			$e->setAttribute($writeAttrib, $value);
		else	
			innerHTML($e, $value);
	}
	return $els->length;
}

function getElementById(DOMDocument $doc, $id)
{
    $xpath = new DOMXPath($doc);
    $node = $xpath->query("//*[@id='$id']");
    //         var_dump($loggeduserDom->item(0));
    if ($node->length);
    return $node->item(0);
}

/**
[data-selector=K]
class='K'
id=K
*/
function getElementsByAny(DOMDocument $doc, $k) 
{   
    $xpath = new DOMXPath($doc);
    $classname = $k;
    $containsClassXPath = "//*[contains( normalize-space( @class ), ' $classname ' ) or substring( normalize-space( @class ), 1, string-length( '$classname' ) + 1 ) = '$classname ' or substring( normalize-space( @class ), string-length( @class ) - string-length( '$classname' ) ) = ' $classname' or @class = '$classname']"; 
    $nodes = $xpath->query("//*[@data-selector='$k']|//*[@$k]|$containsClassXPath|//*[@id='$k']");
    return $nodes;
}
// xpath examples
//*[@id='foo']
//*[@title]
//div[@id='part2']/a[3]
//div[@id='part2']/a[3]/@href
//div[not(@id)]
//div[not(@id)] | //div[@id='part1']
//tr[2]/td[2]/p[2]
function getListContainer(DOMDocument $doc, $k=null)
{   
    $xpath = new DOMXPath($doc);
    if (!$k) $k = 'root';
    $q = "//*[@data-list='$k']";
    //if ($k) $q = "//*[@data-list='$k']";
    //else $q = "//*[@data-list]";
    $nodes = $xpath->query($q);
    return $nodes;
}
function getListGroupper(DOMDocument $doc, $k=null)
{   
    $xpath = new DOMXPath($doc);
    if (!$k) $k = 'root';
    $q = "//*[@data-grouplist='$k']";
    $nodes = $xpath->query($q);
    return $nodes;
}

/**
function getElementsByClassname( DOMDocument $doc, $classname, $pk = null ) 
{   
    $xpath = new DOMXPath( $doc );
    // XPath 2.0
    // $nodes = $xpath->query( "//*[count( index-of( tokenize( @class, '\s+' ), '$classname' ) ) = 1]" );
    // XPath 1.0
    if ($pk) $searchIn = "*[contains(@class,'$pk')]/";
    else $searchIn = '';
    $nodes = $xpath->query( "//$searchIn*[contains( normalize-space( @class ), ' $classname ' ) or substring( normalize-space( @class ), 1, string-length( '$classname' ) + 1 ) = '$classname ' or substring( normalize-space( @class ), string-length( @class ) - string-length( '$classname' ) ) = ' $classname' or @class = '$classname']" );
    return $nodes;
}
*/
function innerHTML($node, $html)
{
	$debug = false;
	// тк нет .innerHTML = html
	$f = $node->ownerDocument;
	$fr = $f->createDocumentFragment();
	$fr->appendXML($html);
	$node->nodeValue = '';
	$node->appendChild($fr);
	if ($debug) 
	{
		global $gi;
		$gi++;
		$node->setAttribute('P',$gi);
	}
}
function debugDom($d)
{
	return htmlspecialchars($d->saveXML($d->documentElement));
}

function debugDomElement($el)
{
    return htmlspecialchars($el->ownerDocument->saveXML($el));
}
function domGetNodeInnerHtml($node)
{
    $innerHTML= '';
    $children = $node->childNodes;
    foreach ($children as $child)
    {
        $innerHTML .= $child->ownerDocument->saveXML($child);
    }
    return $innerHTML;
}

function cerrarTag($tag, $xml){ 
        $indice = 0; 
        while ($indice< strlen($xml)){ 
            $pos = strpos($xml, "<$tag ", $indice); 
            if ($pos){ 
                $posCierre = strpos($xml, ">", $pos); 
                if ($xml[$posCierre-1] == "/"){ 
                    $xml = substr_replace($xml, "></$tag>", $posCierre-1, 2); 
                } 
                $indice = $posCierre; 
            } 
            else break; 
        } 
        return $xml; 
    } 
/**
saveHTML - no html5 tags (nav etc)
saveXML - problems with selfclosed <iframe * />
LIBXML_NOEMPTYTAG - imframe ok but </img>
*/
function renderGCtemplate($path, $ds)
{
	if ($ds instanceof DataSet || $ds instanceof DataRow || $ds instanceof Message) throw new Exception("DataSet, DataRow, Message as template data are not supported");
	$pxhtml = realpath($path.".xhtml");
	$phtml = realpath($path.".html");
	$useXMLtemplate = file_exists($pxhtml);
	$d = new DOMDocument;
	if ($useXMLtemplate)
		$d->loadXML(file_get_contents($pxhtml));
	else
		$d->loadHTML(file_get_contents($phtml));
	if (!$d) throw new Exception("loadXML error on $pxhtml $phtml");	
	recursiveKeyDataFragmentRenderer($d, $ds);
	//var_dump($path,$useXMLtemplate, $d->saveHTML());
	if ($useXMLtemplate)
		//$html = $d->saveHTML();
		$html = $d->saveXML($d->documentElement); // , LIBXML_NOEMPTYTAG
	else
		$html = $d->saveXML($d->documentElement->firstChild->firstChild); // , LIBXML_NOEMPTYTAG 
	$html = cerrarTag("iframe", $html); 
	return $html;	
}


function exportData()
{
    $filter = null;
    //$dir = BASE_DIR.'/importexport';
    $dir = FIXTURES_DIR;
    if (!file_exists($dir)) mkdir($dir, 0700);

    foreach (Entity::each_managed_entity($filter) as $m => $es)
    {
        //if ($m == 'User' || $m == 'Audio' || $m == 'Video' || $m == 'Attach') continue;

        foreach($es as $entity)
        {
            if ($entity->is_system())
            {
                //println("Skip system {$entity->name}",1,TERM_GRAY);
                //continue;
            }
            //if (!in_array($entity->name, array('someentity'))) continue; // export only one BIG table
            if (in_array($entity->name, array('online','visit'))) continue; // skip system tables full stat data
            //if (in_array($entity->name, array('user'))) continue; // skip user - proc in specialized exporter

            for ($cycle = 1; $cycle <= 30; $cycle++)
            {

                $m = new Message();
                $m->urn = (string)$entity;
                $m->action = "load";
                $m->page = $cycle;
                $m->last = 100;
                $m->offset = 100 * ($cycle - 1);

                try {
                    $data = $m->deliver();
                } catch (Exception $e) {
                    println($e->getMessage(), 1, TERM_RED);
                    continue;
                }
                $datacount = count($data);
                if (!$datacount) continue;
                println("$entity ($datacount)", 1, TERM_GREEN);
                if ($datacount > 10000) {
                    println("IT CAN TAKE TIME (> 10K rows)", 1, TERM_RED);
                }
                $i = 0;
                foreach ($data as $c) {
                    $i++;
                    //println($c);
                    $xml = $c->toXML();
                    $filename = $c->urn . '.xml';
                    //println(htmlentities($xml));
                    $fullDataDir = $dir . '/' . $entity->class . '/' . $entity->name;
                    //println($fullDataDir);
                    if (!file_exists($fullDataDir)) mkdir($fullDataDir, 0700, true);
                    save_data_as_file($fullDataDir . '/' . $filename, $xml);
                    if (($i % 2000) === 0) gc_collect_cycles();
                    //if (($i % 100) === 0) println($i);
                }
                gc_collect_cycles();
            }
        }
    }
}

function stringVariateByTemplate($content)
{
    preg_match_all('#{(.*)}#Ui',$content,$matches);
    for ($i=0; $i<sizeof($matches[1]); $i++)
    {
        $ns = explode("|",$matches[1][$i]);
        $c2 = sizeof($ns);
        $rand = rand(0,($c2-1));
        $content = str_replace("{".$matches[1][$i]."}",$ns[$rand],$content);
    }
    return $content;
}

function br2nl($string)
{
    return preg_replace('/\<br(\s*)?\/?\>/i', "\n", $string);
}

function check_json_decode_result($json=null)
{
    switch (json_last_error()) {
        case JSON_ERROR_NONE:
            return true;
            break;
        case JSON_ERROR_DEPTH:
            throw new Exception('JSONERROR1 Достигнута максимальная глубина стека '.$json);
            break;
        case JSON_ERROR_STATE_MISMATCH:
            throw new Exception('JSONERROR2 Некорректные разряды или не совпадение режимов '.$json);
            break;
        case JSON_ERROR_CTRL_CHAR:
            throw new Exception('JSONERROR3 Некорректный управляющий символ '.$json);
            break;
        case JSON_ERROR_SYNTAX:
            throw new Exception('JSONERROR4 Синтаксическая ошибка, не корректный JSON '.$json);
            break;
        case JSON_ERROR_UTF8:
            throw new Exception('JSONERROR5 Некорректные символы UTF-8, возможно неверная кодировка '.$json);
            break;
        default:
            throw new Exception('JSONERROR6 Неизвестная ошибка '.$json);
            break;
    }
}

function qr_detect_zbar($file)
{
    if (strtoupper(substr(PHP_OS, 0, 5)) !== 'LINUX') {
        dprintln("Not ready for ZBar not on Linux");
        return null;
    }
    ob_start();
    passthru("zbarimg $file 2>&1");
    $output = ob_get_contents();
    ob_end_clean();

    $search = '/QR-Code:(.*)/';
    $matches = array();
    $found = preg_match($search, $output, $matches, PREG_OFFSET_CAPTURE, 0);
    if ($found > 0) {
        $qr = $matches[1][0];
        return $qr;
    }
    else
    {
        return false;
    }
}

?>