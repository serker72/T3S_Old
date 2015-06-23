<!DOCTYPE html>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<title><?php wp_title( '|', true, 'right' ); ?></title>
	<link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&amp;subset=latin,cyrillic" rel="stylesheet" type="text/css">
        <link href="/wp-content/themes/twentytwelve/css/bootstrap.min.css" rel="stylesheet">
        <!--link href="/wp-content/themes/twentytwelve/css/dcslick.css" rel="stylesheet" type="text/css"/-->
	<?php wp_head(); ?>
	<!--script src="http://code.jquery.com/ui/1.11.0/jquery-ui.js"></script>
	<script src="https://jquery-ui.googlecode.com/svn-history/r3982/trunk/ui/i18n/jquery.ui.datepicker-ru.js"></script-->
	<script src="/wp-content/themes/twentytwelve/js/jquery-ui.min.js"></script>
	<script src="/wp-content/themes/twentytwelve/js/jquery.ui.datepicker-ru.js"></script>
	<link rel="stylesheet" href="/ui/jquery-ui.css">
	<link rel="stylesheet" type="text/css" href="../info/tooltip.css"/>
        <script src="/wp-content/themes/twentytwelve/js/bootstrap.min.js"></script>
        <!--script src="/wp-content/themes/twentytwelve/js/jquery.slick.js" type="text/javascript"></script-->
	<script>
		var ajax_url = '<?php echo admin_url('admin-ajax.php'); ?>';
	</script>
</head>
<body <?php body_class(); ?>>
<header id="masthead" class="site-header" role="banner">
	<div id="header">
		<?php if (!is_front_page()) {?>
			<div id="logo_head" style="float: left;">
                		<a href="/"><img src="/wp-content/themes/twentytwelve/images/logo_footer_3.png"></a>    
            		</div>
		<?php } ?>
		<div id="time">
			<script type="text/javascript">
				var l = new Date();
				document.write (l.toLocaleString());
			</script> 
		</div>
		<div id="lang">
			<a href="#"><!-- Русский --></a>
		</div>
		<?php if (get_current_user_id() == 0) {?>
		<div id="login">
			<a href="/account/login">Войти в систему</a>
		</div>
		<div id="registr">
			<a href="/account/registration/">Регистрация</a>
		</div>
		<?php } else {?>
		<div id="profile">
			<a href="/account/profile">Личный кабинет</a>
		</div>
		<?php }?>
		<div id="chat">
			<p>ОН-ЛАЙН</p>
			<p>помощник</p>
		</div>

<?php if (is_front_page()) {?>
<div style="clear: both;"></div>
	 <div id="sliders">
                <a href="/"><img src="/wp-content/themes/twentytwelve/images/Head-pic.png"></a>    
            </div>
<?php } ?>


        <?php if (!dynamic_sidebar("Тендеры и Товары") ) : ?>
        <?php endif; ?>
		<?php //if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar("Слайдер") ) : ?>
		<?php //endif; ?>
           
	</div>
</header>

<div id="page" class="hfeed site">
	<div id="main" class="wrapper">