<?php
/**
 * Основные параметры WordPress.
 *
 * Этот файл содержит следующие параметры: настройки MySQL, префикс таблиц,
 * секретные ключи, язык WordPress и ABSPATH. Дополнительную информацию можно найти
 * на странице {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Кодекса. Настройки MySQL можно узнать у хостинг-провайдера.
 *
 * Этот файл используется сценарием создания wp-config.php в процессе установки.
 * Необязательно использовать веб-интерфейс, можно скопировать этот файл
 * с именем "wp-config.php" и заполнить значения.
 *
 * @package WordPress
 */

// ** Параметры MySQL: Эту информацию можно получить у вашего хостинг-провайдера ** //
/** Имя базы данных для WordPress */
define( 'DB_NAME', 't3s' );

/** Имя пользователя MySQL */
define( 'DB_USER', 'root' );

/** Пароль к базе данных MySQL */
define( 'DB_PASSWORD', '' );

/** Имя сервера MySQL */
define( 'DB_HOST', 'localhost' );

/** Кодировка базы данных для создания таблиц. */
define('DB_CHARSET', 'utf8');

/** Схема сопоставления. Не меняйте, если не уверены. */
define('DB_COLLATE', '');

/**#@+
 * Уникальные ключи и соли для аутентификации.
 *
 * Смените значение каждой константы на уникальную фразу.
 * Можно сгенерировать их с помощью {@link https://api.wordpress.org/secret-key/1.1/salt/ сервиса ключей на WordPress.org}
 * Можно изменить их, чтобы сделать существующие файлы cookies недействительными. Пользователям потребуется снова авторизоваться.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '3UUs.@6)HqXL;}B6FmNaRu|~aT:-doW`c>Xp^qc[-JHSR%?_v*k#Nffc@soPeRA&');
define('SECURE_AUTH_KEY',  '=G/7c{r?f}wfSX6z~SgSt5|I_BF+]}@,K^bo6<>-#-vF)|jrS*9hc~ac,J=86xZA');
define('LOGGED_IN_KEY',    'zD+mE5$m+P8jnvl&j|bH- 8Wuud-PHz-g:eh2 )-/ #iA89hM{_=S| I80JyWAym');
define('NONCE_KEY',        'M`-??Y:Lw,h)qz~5K_4_:@+aE@+@nf*LF#h|rymvA:adr@*fY#( >/iY D].;MaT');
define('AUTH_SALT',        '@YcFQqk3h6[^O#Z3R&Z 9|j%yf4PUJs/h KjIAbfyET8Q`oSveh^8+7tiolj4+;E');
define('SECURE_AUTH_SALT', 'O@Gh30iOR#ML --A.{k>boYE:Hsvf.Hx(vMO?4)XEmElq.OqO:*VDB,J01W>nc[|');
define('LOGGED_IN_SALT',   '_E*+>z*w-hsmT#OCN98@IRA?Sul$IfckqLtt^s`aZI{y&=)%U))xB$eaRic;796J');
define('NONCE_SALT',       'av$tkW(;+QSk[D@EV-;1M=S${wLz_ILlx]m}&1U+LT5!;Z-b)hL`y@!h@t{(_!vN');

/**#@-*/

/**
 * Префикс таблиц в базе данных WordPress.
 *
 * Можно установить несколько блогов в одну базу данных, если вы будете использовать
 * разные префиксы. Пожалуйста, указывайте только цифры, буквы и знак подчеркивания.
 */
$table_prefix = 'wp_';

/**
 * Язык локализации WordPress, по умолчанию английский.
 *
 * Измените этот параметр, чтобы настроить локализацию. Соответствующий MO-файл
 * для выбранного языка должен быть установлен в wp-content/languages. Например,
 * чтобы включить поддержку русского языка, скопируйте ru_RU.mo в wp-content/languages
 * и присвойте WPLANG значение 'ru_RU'.
 */
define('WPLANG', 'ru_RU');

/**
 * Для разработчиков: Режим отладки WordPress.
 *
 * Измените это значение на true, чтобы включить отображение уведомлений при разработке.
 * Настоятельно рекомендуется, чтобы разработчики плагинов и тем использовали WP_DEBUG
 * в своём рабочем окружении.
 */
define('WP_DEBUG', false);

// Enable Debug logging to the /wp-content/debug.log file
//define('WP_DEBUG_LOG', true);

// Disable display of errors and warnings 
//define('WP_DEBUG_DISPLAY', true);
//@ini_set('display_errors', 1);

// Use dev versions of core JS and CSS files (only needed if you are modifying these core files)
//define('SCRIPT_DEBUG', true);


/* Это всё, дальше не редактируем. Успехов! */

/** Абсолютный путь к директории WordPress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Инициализирует переменные WordPress и подключает файлы. */
require_once(ABSPATH . 'wp-settings.php');
