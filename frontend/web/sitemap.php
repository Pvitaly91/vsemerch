<?php 
$parmasPath = str_replace("//", "/", $_SERVER['DOCUMENT_ROOT']."/common/config/main-local.php");
if(!file_exists($parmasPath)){
    echo "confifg not found";
    exit;
}
 
$params = require_once($parmasPath);
$db = $params["components"]["db"];
$parts = explode(";",$db['dsn']);
$server = str_replace("mysql:host=", "", $parts['0']);
$dbName = str_replace("dbname=", "", $parts['1']);
// Везде выводим ошибки для отладки
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Настройки базы
define('DB_HOSTNAME', $server);
define('DB_DATABASE', $dbName);
define('DB_USERNAME', $db["username"]);
define('DB_PASSWORD', $db["password"]);


function my_debag($array, $die = true, $vars = false) {
	$array = $vars ? var_dump($array, true) : print_r($array, true);
    $out = '<pre>'.htmlspecialchars($array).'</pre><style>PRE{-moz-border-radius:10px;-webkit-border-radius:10px;-khtml-border-radius:10px;border-radius:10px;padding:15px 20px;font-size:14px;background-color:#272822;color:#FFFFFF;font-family:"Consolas","Monospace","Menlo Regular";min-width:60vw;max-width:100vw;-moz-box-shadow:0 0 15px 5px rgba(0,0,0,0.5);-webkit-box-shadow:0 0 15px 5px rgba(0,0,0,0.5);box-shadow:0 0 15px 5px rgba(0,0,0,0.5);border:2px solid #BDBDBD}</style>';
    if ($die) {
    	die($out);
    }
    else {
    	echo $out;
    }
}

function execute($sth, $array) {
	$sth->execute($array);
	if ($sth->errorCode() != '00000') {
		$info = $sth->errorInfo();
		echo '<h2 style="color:red;"><b>Ошибка запроса в базу:</b></h2>';
		my_debag($info);
	}
}
function ee($text, $color = 'black') {
	global $start;
	echo '<b style="color:'.$color.';">'.$text.'</b> -- '.((int)microtime(true) - $start).' сек<br>';
}
$start = (int)microtime(true);
try {
	$DB = new PDO('mysql:dbname='.DB_DATABASE.';host='.DB_HOSTNAME, DB_USERNAME, DB_PASSWORD);
}
catch (PDOException $e) {
	die($e->getMessage());
}
ee('Подключились к базе', 'green');









$domain = 'https://agcity.com.ua/';

// Начало карты
$sitemap_uk = '<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
	<url>
		<loc>'.$domain.'</loc>
		<priority>0.7</priority>
	</url>';
$sitemap_ru = '<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
	<url>
		<loc>'.$domain.'ru</loc>
		<priority>0.7</priority>
	</url>';
$sitemap_en = '<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
	<url>
		<loc>'.$domain.'en</loc>
		<priority>0.7</priority>
	</url>';


function addSitemap($url, $priority = '0.5', $date = false) {
global $sitemap_uk, $sitemap_ru, $sitemap_en, $domain;

	if (stripos($url, 'http') === false) {
		$lastmod = '';
		if ($date) {
			$lastmod = '
		<lastmod>'.$date.'</lastmod>';
		}
		$sitemap_uk .= '
	<url>
		<loc>'.$domain.$url.'</loc>'.$lastmod.'
		<priority>'.$priority.'</priority>
	</url>';
		$sitemap_ru .= '
	<url>
		<loc>'.$domain.'ru/'.$url.'</loc>'.$lastmod.'
		<priority>'.$priority.'</priority>
	</url>';
		$sitemap_en .= '
	<url>
		<loc>'.$domain.'en/'.$url.'</loc>'.$lastmod.'
		<priority>'.$priority.'</priority>
	</url>';
	}
}


// $rules = [
//     '<language:(ru|uk|en)>'=>'site/index',
//     '<language:(ru|uk|en)>/text/<slug:\w+>'=>'text/index',
//     '<language:(ru|uk|en)>/call/success'=>'call/success',
//     '<language:(ru|uk|en)>/mail/success'=>'mail/success',
//     '<language:(ru|uk|en)>/news'=>'news/index',
//     '<language:(ru|uk|en)>/news/<translit:[\w\-\ ]+>-<id:\d+>'=>'news/show',
//     '<language:(ru|uk|en)>/products/<translit:[\w\-\ ]+>-<id:\d+>'=>'products/show',
//     '<language:(ru|uk|en)>/products'=>'products/index',
//     '<language:(ru|uk|en)>/disease/<translit:[\w\-\ ]+>'=>'disease/show',
//     '<language:(ru|uk|en)>/disease'=>'disease/index',
//     '<language:(ru|uk|en)>/partners'=>'partners/index',
//     '<language:(ru|uk|en)>/contacts'=>'contacts/index',
//     '<language:(ru|uk|en)>/production'=>'production/index',
//     '<language:(ru|uk|en)>/production/<slug:\w+>'=>'production/show',
//     '<language:(ru|uk|en)>/catalog/<translit:[\w\-\ ]+>'=>'catalog/show',
//     '<language:(ru|uk|en)>/catalog'=>'catalog/index',
//     '<language:(ru|uk|en)>/web/<translit:[\w\-\ ]+>'=>'web/show',
//     '<language:(ru|uk|en)>/web'=>'web/index',
//     '<language:(ru|uk|en)>/web/portfolio/<translit:[\w\-\ ]+>-<id:\d+>'=>'web-products/show',
//     '<language:(ru|uk|en)>/design'=>'design/index',
//     '<language:(ru|uk|en)>/design/<slug:[\w\-\ ]+>'=>'design/show',

//     ''=>'site/index',
//     'gallery/<id:\d+>'=>'gallery/show',
//     'about'=>'text/about',
//     'contact'=>'text/contact',
//     'text/<slug:\w+>'=>'text/index',
//     'news'=>'news/index',
//     'news/<translit:[\w\-\ ]+>-<id:\d+>'=>'news/show',
//     'products/<translit:[\w\-\ ]+>-<id:\d+>'=>'products/show',
//     'products'=>'products/index',
//     'disease/<translit:[\w\-\ ]+>'=>'disease/show',
//     'disease'=>'disease/index',
//     'partners'=>'partners/index',
//     'contacts'=>'contacts/index',
//     'production'=>'production/index',
//     'production/<slug:\w+>'=>'production/show',
//     'catalog/<translit:[\w\-\ ]+>'=>'catalog/show',
//     'catalog'=>'catalog/index',
//     'web/<translit:[\w\-\ ]+>'=>'web/show',
//     'web'=>'web/index',
//     'web/portfolio/<translit:[\w\-\ ]+>-<id:\d+>'=>'web-products/show',
//     'design'=>'design/index',
//     'design/<slug:[\w\-\ ]+>'=>'design/show',
// ];


addSitemap('news', '0.7');
addSitemap('products', '0.7');
addSitemap('partners', '0.3');
addSitemap('contacts', '0.3');
addSitemap('design', '0.3');
addSitemap('portfolio', '0.3');
addSitemap('production', '0.3');
addSitemap('catalog', '0.7');
addSitemap('web', '0.7');



$sth = $DB->prepare("SELECT `slug` FROM `text`"); $sth->execute();
$page = $sth->fetchAll(PDO::FETCH_ASSOC);
foreach ($page as $row) {
	if (($row['slug'] == 'news') or
		($row['slug'] == 'products') or
		($row['slug'] == 'partners') or
		($row['slug'] == 'contacts') or
		($row['slug'] == 'design') or
		($row['slug'] == 'portfolio') or
		($row['slug'] == 'production') or
		($row['slug'] == 'catalog') or
		($row['slug'] == 'web')) {
		continue;
	}
	addSitemap('text/'.$row['slug'], '0.3');
}
$disabledPartnersCatsSlug = ["eney"];
$disabledIdsids = [];
foreach($disabledPartnersCatsSlug as $slug){
    
    $disCats = $DB->prepare("SELECT id FROM `shop_category` WHERE `slug` = '$slug' AND `partner` = '$slug' ");
    $disCats->execute();
    $page = $disCats->fetch(PDO::FETCH_ASSOC);
    $disabledIdsids[] = $page["id"];
     $disCats = $DB->prepare("SELECT id,slug FROM `shop_category` WHERE `parent_id` = '".$page["id"]."' AND `partner` = '$slug' ");
    $disCats->execute();
    $page = $disCats->fetchAll(PDO::FETCH_ASSOC);
    
    foreach($page as $cat){
        $disabledIdsids[] = $cat["id"];
    }
   
}

$sth = $DB->prepare("SELECT `translit`, `id`, `date` FROM `news`"); $sth->execute();
$page = $sth->fetchAll(PDO::FETCH_ASSOC);
foreach ($page as $row) {
	addSitemap('news/'.$row['translit'].'-'.$row['id'], '0.3', $row['date']);
}

$sth = $DB->prepare("SELECT `translit` FROM `catalog`"); $sth->execute();
$page = $sth->fetchAll(PDO::FETCH_ASSOC);
foreach ($page as $row) {
	addSitemap('catalog/'.$row['translit'], '0.3');
}

$sth = $DB->prepare("SELECT `id`, `slug` FROM `shop_category` WHERE `active`=1 AND id NOT IN (".implode(",",$disabledIdsids).")"); $sth->execute();
$page = $sth->fetchAll(PDO::FETCH_ASSOC);
foreach ($page as $row) {
	addSitemap('shop/catalog/'.$row['slug'].'-'.$row['id'], '0.5');
}

$sth = $DB->prepare("SELECT `id`, `slug` FROM `shop_product` WHERE `not_active`=0  AND category_id NOT IN (".implode(",",$disabledIdsids).")"); $sth->execute();
$page = $sth->fetchAll(PDO::FETCH_ASSOC);
foreach ($page as $row) {
	addSitemap('shop/product/'.$row['slug'].'-'.$row['id'], '0.5');
}

$sth = $DB->prepare("SELECT `translit` FROM `disease`"); $sth->execute();
$page = $sth->fetchAll(PDO::FETCH_ASSOC);
foreach ($page as $row) {
	addSitemap('disease/'.$row['translit'], '0.1');
}

$sth = $DB->prepare("SELECT `translit`, `id` FROM `products`"); $sth->execute();
$page = $sth->fetchAll(PDO::FETCH_ASSOC);
foreach ($page as $row) {
	addSitemap('products/'.$row['translit'].'-'.$row['id'], '0.1');
}

$sth = $DB->prepare("SELECT `translit` FROM `web_catalog`"); $sth->execute();
$page = $sth->fetchAll(PDO::FETCH_ASSOC);
foreach ($page as $row) {
	addSitemap('web/'.$row['translit'].'-'.$row['id'], '0.1');
}

// Конец карты
$sitemap_uk .= '
</urlset>';
$sitemap_ru .= '
</urlset>';
$sitemap_en .= '
</urlset>';

file_put_contents(__DIR__.'/sitemap-uk.xml', $sitemap_uk);
file_put_contents(__DIR__.'/sitemap-ru.xml', $sitemap_ru);
//file_put_contents(__DIR__.'/sitemap-en.xml', $sitemap_en);