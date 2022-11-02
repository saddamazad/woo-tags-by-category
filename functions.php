add_shortcode( 'get_tags_by_category', 'get_tags_by_category_shortcode' );
function get_tags_by_category_shortcode($atts) {	
    $args = shortcode_atts(array(
        'categories' => ''
    ), $atts);	
	extract($args);
	
	//$categories = explode(",", $category);
	$tags = get_category_tags($categories);
	
	ob_start();
	echo '<ul class="tag_lists">';
	echo '<li><a href="'.get_permalink(919).'">All</li>';
	foreach ($tags as $tag) {   
		//echo '<a href="'.$tag->tag_link.'">' . $tag->tag_name . '</a>';
		echo '<li class="cat-item"><a href="/veneer/?cat_tags='.$tag->tag_slug.'">'.$tag->tag_name.'</a></li>';
	}
	echo '</ul>';
	return ob_get_clean();
}

function get_category_tags($categories) {
    global $wpdb;
    $tags = $wpdb->get_results
    ("
        SELECT DISTINCT terms2.term_id as tag_id, terms2.name as tag_name, terms2.slug as tag_slug, null as tag_link
        FROM
            {$wpdb->prefix}posts as p1
            LEFT JOIN {$wpdb->prefix}term_relationships as r1 ON p1.ID = r1.object_ID
            LEFT JOIN {$wpdb->prefix}term_taxonomy as t1 ON r1.term_taxonomy_id = t1.term_taxonomy_id
            LEFT JOIN {$wpdb->prefix}terms as terms1 ON t1.term_id = terms1.term_id,

            {$wpdb->prefix}posts as p2
            LEFT JOIN {$wpdb->prefix}term_relationships as r2 ON p2.ID = r2.object_ID
            LEFT JOIN {$wpdb->prefix}term_taxonomy as t2 ON r2.term_taxonomy_id = t2.term_taxonomy_id
            LEFT JOIN {$wpdb->prefix}terms as terms2 ON t2.term_id = terms2.term_id
        WHERE
            t1.taxonomy = 'product_cat' AND p1.post_status = 'publish' AND terms1.term_id IN (".$categories.") AND
            t2.taxonomy = 'product_tag' AND p2.post_status = 'publish'
            AND p1.ID = p2.ID
        ORDER by tag_name
    ");
    $count = 0;
    foreach ($tags as $tag) {
        $tags[$count]->tag_link = get_tag_link($tag->tag_id);
        $count++;
    }
    return $tags;
}
