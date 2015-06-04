<?php

// Возвращает список страниц, в контенте которых обнаружена поисковая строка, в виде массива
function tzs_get_pages_by_content_search($search_keyword, $orderby_array=array()) {
    $arr_lis = array();
    
    if (($search_keyword !== null) && (strlen($search_keyword) > 0)) {
        if (($orderby_array !== null) && (count($orderby_array) > 0)) {
            $myposts = new WP_Query("s=$search_keyword", array('orderby' => $search_keyword));
        } else {
            $myposts = new WP_Query("s=$search_keyword");
        }
        $i=0;
        while ( $myposts->have_posts() ) : $myposts->the_post(); 
            $id = get_the_ID();
            $link = get_permalink(); 
            $title = the_title('', '', false);
            $arr_lis[$i] = array('id'=>$id, 'title'=>$title, 'link'=>$link );
            $i++;
        endwhile; 

        wp_reset_postdata();
    }
    
    return $arr_lis; 
}

// Возвращает ID первой страницы, в контенте которых обнаружена поисковая строка
function tzs_get_first_page_id_by_content_search($search_keyword, $orderby_array=array()) {
    $arr_lis = tzs_get_pages_by_content_search($search_keyword, $orderby_array);
    
    if (count($arr_lis) > 0) {
        return $arr_lis[0]['id'];
    } else {
        return 0;
    }
}

// Возвращает список дочерних страниц в виде массива
function tzs_get_children_pages($post_parent) {
    $arr_lis = array();
    $myposts = new WP_Query(array( 'post_type' => 'page', 'post_parent' => $post_parent, 'orderby' => array('ID' => 'ASC')));
    $i=0;
    while ( $myposts->have_posts() ) : $myposts->the_post(); 
        $id = get_the_ID();
        $link = get_permalink(); 
        $title = the_title('', '', false);
        $arr_lis[$id] = array('id'=>$id, 'title'=>$title, 'link'=>$link );
        $i++;
    endwhile; 
    
    wp_reset_postdata();
    
    return $arr_lis; 
}

// Возвращает список дочерних страниц в виде элементов выпадающего списка select
function tzs_build_product_types($field_name, $post_parent) {
    $res = tzs_get_children_pages($post_parent);
    
    if (count($res) == 0) {
            // do nothink
    } else {
        $counter = 0;
        foreach ( $res as $row ) {
            $type_id = $row['id'];
            $title = $row['title'];
            ?>
            <option value="<?php echo $type_id; ?>" <?php
            if ((isset($_POST[$field_name]) && $_POST[$field_name] == $type_id)) {
                echo 'selected="selected"';
            }
            ?>
            ><?php echo $title; ?></option>
            <?php
            $counter++;
        }
    }
}

// Возвращает список дочерних страниц в виде строки с указанным разделителем
function tzs_build_product_types_id_str($post_parent, $delim=',') {
    $res_str = '';
    $res = tzs_get_children_pages($post_parent);
    
    if (count($res) > 0) {
        $counter = 0;
        foreach ( $res as $row ) {
            if (strlen($res_str) > 0) {
                $res_str .= $delim;
            }
            $res_str .= $row['id'];
            $counter++;
        }
    }
    
    return $res_str;
}

?>