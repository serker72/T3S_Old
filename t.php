<?php
function return_bytes ($size_str)
{
    switch (substr ($size_str, -1))
    {
        case 'M': case 'm': return (int)$size_str * 1048576;
        case 'K': case 'k': return (int)$size_str * 1024;
        case 'G': case 'g': return (int)$size_str * 1073741824;
        default: return $size_str;
    }
}

echo 'display_errors = ' . ini_get('display_errors') . "<br/>";
echo 'register_globals = ' . ini_get('register_globals') . "<br/>";
echo 'post_max_size = ' . ini_get('post_max_size') . "<br/>";
echo 'post_max_size+1 = ' . (ini_get('post_max_size') +1 ) . "<br/>";
echo 'post_max_size in bytes = ' . return_bytes(ini_get('post_max_size')). "<br/>";
echo 'max_file_uploads = ' . ini_get('max_file_uploads') . "<br/>";
echo 'upload_max_filesize = ' . ini_get('upload_max_filesize') . "<br/>";

define( 'TZS_PR_MAX_IMAGES', 7 );

include_once('wp-content/plugins/tzs/front-end/tzs.trade.images.php');
$_POST['image_id_lists'] = '193;194;195;196;197;198';
tzs_front_end_pr_images_handler();
