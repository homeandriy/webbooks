<?php
/**
* Шаблон шапки (header.php)
* @package WordPress
* @subpackage webbooks
*/
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<meta name="msvalidate.01" content="D2011B41C4E32109AA355E08C8E7D298" />
		<meta name="google-site-verification" content="moLqYp4Ozcbrt4kxF5K-KDMMd7dh-iJdeyjAj49HnQQ" />
		<meta name='wmail-verification' content='fb72a559db8e3a6009afff9c71ca8e95' />
		<meta name="msvalidate.01" content="D2011B41C4E32109AA355E08C8E7D298" />
		<link rel="alternate" hreflang="ru-RU" href="http://webbooks.com.ua/" />
		<meta http-equiv="content-language" content="ru"> 
		<title><?php get_bloginfo('name' ); ?></title>
		<meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
		<?php wp_head(); // необходимо для работы плагинов и функционала ?>
	</head>
