<?php
$GLOBALS['tzs_tr_types'] = array(
	0 => 'не указан',
	1 => 'тент',
	2 => 'крытая',
	3 => 'изотерм',
	4 => 'цельномет.',
	5 => 'рефрижератор'
);

$GLOBALS['tzs_tr_types_search'] = array(
	0 => 'все',
	1 => 'тент',
	2 => 'крытая',
	3 => 'изотерм',
	4 => 'цельномет.',
	5 => 'рефрижератор'
);

$GLOBALS['tzs_tr2_types'] = array(
	0 => array('не указан', '', ''),
	1 => array('грузовик', get_site_url()."/wp-content/plugins/tzs/assets/images/grzv_bl.gif", get_site_url()."/wp-content/plugins/tzs/assets/images/grzv_gr.gif"),
	2 => array('полуприцеп', get_site_url()."/wp-content/plugins/tzs/assets/images/pol_bl.gif", get_site_url()."/wp-content/plugins/tzs/assets/images/pol_gr.gif"),
	3 => array('сцепка', get_site_url()."/wp-content/plugins/tzs/assets/images/scpk_bl.gif", get_site_url()."/wp-content/plugins/tzs/assets/images/scpk_gr.gif")
);

$GLOBALS['tzs_weight_enum'] = array(0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25);
$GLOBALS['tzs_volume_enum'] = array(0,1,2,3,4,5,10,15,20,25,30,40,50,60,70,80,90,100,110,120,130);

$GLOBALS['tzs_curr'] = array(
	1 => 'грн',
	2 => 'грн/км',
	3 => 'грн/тонну',
	0 => '',
	4 => 'EUR',
	5 => 'EUR/км',
	6 => 'EUR/тонну',
	7 => 'USD',
	8 => 'USD/км',
	9 => 'USD/тонну',
	10 => 'рос.руб',
	11 => 'рос.руб/км',
	12 => 'рос.руб/тонну',
	13 => 'бел.руб',
	14 => 'бел.руб/км',
	15 => 'бел.руб/тонну',
	16 => 'лит',
	17 => 'лит/км',
	18 => 'лит/тонну',
	19 => 'лат',
	20 => 'лат/км',
	21 => 'лат/тонну',
	22 => 'лей',
	23 => 'лей/км',
	24 => 'лей/тонну',
	25 => 'тнг',
	26 => 'тнг/км',
	27 => 'тнг/тонну',
	28 => 'тад.сом',
	29 => 'тад.сом/км',
	30 => 'тад.сом/тонну',
	31 => 'лари',
	32 => 'лари/км',
	33 => 'лари/тонну',
	34 => 'AZN',
	35 => 'AZN/км',
	36 => 'AZN/тонну',
	37 => 'AMD',
	38 => 'AMD/км',
	39 => 'AMD/тонну',
	40 => 'кыр.сом',
	41 => 'кыр.сом/км',
	42 => 'кыр.сом/тонну',
	43 => 'TMT',
	44 => 'TMT/км',
	45 => 'TMT/тонну',
	46 => 'сум',
	47 => 'сум/км',
	48 => 'сум/тонну',
	49 => 'PLN',
	50 => 'PLN/км',
	51 => 'PLN/тонну',
	52 => 'RON',
	53 => 'RON/км',
	54 => 'RON/тонну',
	55 => 'TRY',
	56 => 'TRY/км',
	57 => 'TRY/тонну'
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
	2 => 'безналичная без НДС',
	3 => 'безналичная, включая НДС',
);

$GLOBALS['tzs_au_unit'] = array(
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

$GLOBALS['tzs_au_contact_view_all'] = (get_post_meta($post_id='1', 'tzs-au-contact-view-all', true) == '1');

?>