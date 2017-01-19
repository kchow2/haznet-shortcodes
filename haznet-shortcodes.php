<?php

/*
  Plugin Name: Haznet Shortcodes
  Plugin URI:  http://www.google.ca
  Description: This plugin implements shortcodes for the Haznet website.
  Version:     0.1
  Author:      Kevin Chow
  Author URI:  http://www.google.ca
 */

error_reporting(E_ALL); 

/** Author Taxonomies shortcode
 * @param type $atts - array of parameters. $atts['auth'] specifies the author ID. $atts['tax'] specifies the taxonomy name
 * @return string - A list of taxonomy terms the author has written about, with the article count next to each term.
 */
function author_taxonomies_sc($atts){ 
    if(!isset($atts['auth']) || !isset($atts['tax'])){
        return "Usage: [author-taxonomies auth=<XX> tax=<YY>]";
    }
    
    $authorID = $atts['auth'];
    $taxonomyName = $atts['tax'];
    $posts = get_posts( array(
        'posts_per_page' => -1, 
        'author' => $authorID) 
    );
    $authorTerms = array();
    foreach ($posts as $p) {
        $terms = wp_get_object_terms( $p->ID, $taxonomyName);
        foreach ($terms as $t) {
            if(!array_key_exists($t->name, $authorTerms)){
                $authorTerms[$t->name] = 1;
            }
            else{
                $authorTerms[$t->name]++;
            }
        }
    }
    return author_taxonomies_format_result($authorTerms);
}
add_shortcode('author-taxonomies', 'author_taxonomies_sc');

function author_taxonomies_format_result($authorTerms){   
    $res = "";
    foreach($authorTerms as $term=>$count){
        $res .= '<div class="auth-term">';
        $res .= $term;
        $res .= '<div class="auth-term-count">' . $count . '</div>';
        $res .= '</div>';
    }
    return $res;
}