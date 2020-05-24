<?php
/**
* Шаблон шапки (header.php)
* @package WordPress
* @subpackage your-clean-template
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
		<!--[if lte IE 8]><script src="<?php get_template_directory_uri()  ?>/portfolio/assets/js/ie/html5shiv.js"></script><![endif]-->
		
		<!--[if lte IE 8]><link rel="stylesheet" href="<?php get_template_directory_uri()  ?>/portfolio/assets/css/ie8.css" /><![endif]-->
		<!--[if lte IE 9]><link rel="stylesheet" href="<?php get_template_directory_uri()  ?>/portfolio/assets/css/ie9.css" /><![endif]-->
		<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
		<script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
		<![endif]-->
		<?php wp_head(); // необходимо для работы плагинов и функционала ?>
	</head>
	