<?php
$GLOBALS['tzs_tr_types'] = array(
	0 => 'не указан',
	1 => 'борт открытый',
	2 => 'борт тентованый',
	3 => 'сцепка',
	4 => 'полуприцеп',
	5 => 'рефрижератор',
	6 => 'автобус',
	7 => 'контейнеровоз',
	8 => 'панелевоз',
	9 => 'самосвал',
	10 => 'борт+манипулятор',
	11 => 'лесовоз',
	12 => 'трубовоз',
	13 => 'автокран',
	14 => 'зерновоз',
	15 => 'цементовоз',
	16 => 'цистерна',
	17 => 'автобетоно смеситель',
	18 => 'скотовоз'
);

$GLOBALS['tzs_tr_types_search'] = array(
	0 => 'все',
	1 => 'борт открытый',
	2 => 'борт тентованый',
	3 => 'сцепка',
	4 => 'полуприцеп',
	5 => 'рефрижератор',
	6 => 'автобус',
	7 => 'контейнеровоз',
	8 => 'панелевоз',
	9 => 'самосвал',
	10 => 'борт+манипулятор',
	11 => 'лесовоз',
	12 => 'трубовоз',
	13 => 'автокран',
	14 => 'зерновоз',
	15 => 'цементовоз',
	16 => 'цистерна',
	17 => 'автобетоно смеситель',
	18 => 'скотовоз'
);

$GLOBALS['tzs_sh_types'] = array(
	0 => 'не указан',
	1 => 'стройматериалы',
	2 => 'инструмент',
	3 => 'отделочные материалы',
	4 => 'продукты',
	5 => 'с/х продукция',
	6 => 'сырьё',
	7 => 'оборудование',
	8 => 'бытовая техника',
	9 => 'товары народного потребления',
	10 => 'тара',
	11 => 'стекло',
	12 => 'опасный груз',
	13 => 'негабаритный груз',
	14 => 'спецгруз',
	15 => 'транспортные средства',
	16 => 'замороженная продукция',
	17 => 'люди',
	18 => 'другое'
);

$GLOBALS['tzs_sh_types_search'] = array(
	0 => 'все',
	1 => 'стройматериалы',
	2 => 'инструмент',
	3 => 'отделочные материалы',
	4 => 'продукты',
	5 => 'с/х продукция',
	6 => 'сырьё',
	7 => 'оборудование',
	8 => 'бытовая техника',
	9 => 'товары народного потребления',
	10 => 'тара',
	11 => 'стекло',
	12 => 'опасный груз',
	13 => 'негабаритный груз',
	14 => 'спецгруз',
	15 => 'транспортные средства',
	16 => 'замороженная продукция',
	17 => 'люди',
	18 => 'другое'
);

//	1 => array('грузовик', get_site_url()."/wp-content/plugins/tzs/assets/images/grzv_bl.gif", get_site_url()."/wp-content/plugins/tzs/assets/images/grzv_gr.gif"),
$GLOBALS['tzs_tr2_types'] = array(
	0 => array('не указан', ''),
	1 => array('борт открытый', get_site_url()."/wp-content/plugins/tzs/assets/images/bort-otkryt.png"),
	2 => array('борт тентованый', get_site_url()."/wp-content/plugins/tzs/assets/images/bort-tent.png"),
	3 => array('сцепка', get_site_url()."/wp-content/plugins/tzs/assets/images/scepka.png"),
	4 => array('полуприцеп', get_site_url()."/wp-content/plugins/tzs/assets/images/polu-pricep.png"),
	5 => array('рефрижератор', get_site_url()."/wp-content/plugins/tzs/assets/images/refrigerator.png"),
	6 => array('автобус', get_site_url()."/wp-content/plugins/tzs/assets/images/bus.png"),
	7 => array('контейнеровоз', get_site_url()."/wp-content/plugins/tzs/assets/images/conteinerovoz.png"),
	8 => array('панелевоз', get_site_url()."/wp-content/plugins/tzs/assets/images/panelevoz.png"),
	9 => array('самосвал', get_site_url()."/wp-content/plugins/tzs/assets/images/samosval.png"),
	10 => array('борт+манипулятор', get_site_url()."/wp-content/plugins/tzs/assets/images/bort-manipulator.png"),
	11 => array('лесовоз', get_site_url()."/wp-content/plugins/tzs/assets/images/lesovoz.png"),
	12 => array('трубовоз', get_site_url()."/wp-content/plugins/tzs/assets/images/trubovoz.png"),
	13 => array('автокран', get_site_url()."/wp-content/plugins/tzs/assets/images/avtokran.png"),
	14 => array('зерновоз', get_site_url()."/wp-content/plugins/tzs/assets/images/zernovoz.png"),
	15 => array('цементовоз', get_site_url()."/wp-content/plugins/tzs/assets/images/cementovoz.png"),
	16 => array('цистерна', get_site_url()."/wp-content/plugins/tzs/assets/images/cisterna.png"),
	17 => array('автобетоно смеситель', get_site_url()."/wp-content/plugins/tzs/assets/images/avtobeton.png"),
	18 => array('скотовоз ', get_site_url()."/wp-content/plugins/tzs/assets/images/skotovoz.png"),
);

$GLOBALS['tzs_weight_enum'] = array(0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25);
$GLOBALS['tzs_volume_enum'] = array(0,1,2,3,4,5,10,15,20,25,30,40,50,60,70,80,90,100,110,120,130);

$GLOBALS['tzs_curr'] = array(
	0 => '',
	1 => 'грн',
	2 => 'EUR',
	3 => 'USD',
	4 => 'рос.руб',
	5 => 'бел.руб',
	6 => 'лит',
	7 => 'лат',
	8 => 'лей',
	9 => 'тнг',
	10 => 'тад.сом',
	11 => 'лари',
	12 => 'AZN',
	13 => 'AMD',
	14 => 'кыр.сом',
	15 => 'TMT',
	16 => 'сум',
	17 => 'PLN',
	18 => 'RON',
	19 => 'TRY'
);

$GLOBALS['tzs_city_from_radius_value'] = array(
	10 => '10 км',
	20 => '20 км',
	30 => '30 км',
	40 => '40 км',
	50 => '50 км'
);

$GLOBALS['tzs_pr_curr'] = array(
	0 => '',
	1 => 'грн',
	2 => 'грн/м<sup>2</sup>',
	3 => 'грн/м<sup>3</sup>',
	4 => 'грн/метр пог.',
	5 => 'грн/кг',
	6 => 'грн/т',
	7 => 'грн/л',
	8 => 'грн/ч',
);

$GLOBALS['tzs_pr_payment'] = array(
	0 => 'любая',
	1 => 'наличная',
	2 => 'безналичная',
);

$GLOBALS['tzs_pr_nds'] = array(
	0 => '',
	1 => 'без НДС',
	2 => 'включая НДС',
);

$GLOBALS['tzs_pr_unit'] = array(
	0 => '',
	1 => 'шт',
	2 => 'м<sup>2</sup>',
	3 => 'м<sup>3</sup>',
	4 => 'метр пог.',
	5 => 'кг',
	6 => 'т',
	7 => 'л',
	8 => 'ч',
);

$GLOBALS['tzs_au_contact_view_all'] = (get_post_meta($post_id='19', 'tzs-au-contact-view-all', true) == '1');

?>