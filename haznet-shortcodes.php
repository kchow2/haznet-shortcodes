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

/** Author Tax List shortcode
 * @param type $atts - array of parameters. $atts['tax'] specifies the taxonomy to search for. $atts['term'] specifies the term to search for
 * @return string - A list of authors that have submitted articles containing that term, with the article count beside them.
 */
function author_tax_list_sc($atts){ 
    if(!isset($atts['tax']) || !isset($atts['term'])){
        return "Usage: [author-tax-list tax=<XX> term=<YY> ]";
    }
    
    $taxSlug = $atts['tax'];
    $termSlugs = array($atts['term']);
    $posts = get_posts( array(
        'post_type'=>array('post', 'resilience-post'),
        'posts_per_page' => -1,
        'tax_query' => array(
            array(
                    'taxonomy' => $taxSlug,
                    'field'    => 'slug',
                    'terms'    => $termSlugs,
            )
        )
    ));

    $authorPostCount = array();
    foreach ($posts as $p) {
        $userRoles = get_userdata($p->post_author)->roles;
        //var_dump($p->post_title);
        //var_dump($userRoles);
        if(in_array("author", $userRoles)){
            $authorID = intval($p->post_author);
            $authorPostCount[$authorID]++;
        }
    }
    
    $authorNames = array();
    foreach(array_keys($authorPostCount) as $authorID){
        $authorNames[$authorID] = get_the_author_meta('display_name', $authorID);
    }
            
    return author_tax_list_format_result($authorPostCount, $authorNames);
}
add_shortcode('author-tax-list', 'author_tax_list_sc');

function author_tax_list_format_result($authorPostCount, $authorNames){   
    $res = "";
    $res .= '<div class="auth-tax-list"><div class="row">';
    foreach($authorPostCount as $authorID=>$count){
        $res .= '<div class="col-sm-6 col-lg-3">';
        $res .= '<div class="auth-name">' . $authorNames[$authorID] . '</div>';
        $res .= '<div class="auth-term-count">' . ' (' . $count . ')' . '</div>';
        $res .= '</div>';
    }
    $res .= '</div></div>';
    return $res;
}

function hz_copyright_sc($atts){
    $copyYear = 2017; 
    $curYear = date('Y'); 
    return '&copy; HazNet (' . $copyYear . (($copyYear != $curYear) ? '-' . $curYear : '').')';
}
add_shortcode('hz-copyright', 'hz_copyright_sc');