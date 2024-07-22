<?php


/**
 * Decodes the arguments passed as a string and returns an array of key-value pairs.
 *
 * @param string $str_args The string containing the arguments in the format 'key=value||key=value'.
 * @throws None
 * @return array The array of key-value pairs representing the decoded arguments.
 */
function decode_args( $str_args ){
	/* $str_args format: post_type=lp_course||post_status=publish */

	$info_path = basename(__FILE__, '.php') . '.php->' . __FUNCTION__ . '.txt';

	$separated_args = explode('||', $str_args);
	/* $separated_args format: array('post_type=lp_course','post_status=publish') */

	$args = array();
	foreach( $separated_args as $arg ){
		$separated = explode('=', $arg);
		$key = $separated[0];
		$value = $separated[1];

		$args[$key] = $value; 
	}

	document_info($info_path, "ARGS", array(
		'str_args' => $str_args,
		'separated_args' => $separated_args,
		'args' => $args
	), true);

	return $args;
}

//* GET POSTS

/**
 * Retrieves posts based on the provided arguments.
 *
 * @param array $user_args An array of arguments to customize the post retrieval. Default is an empty array.
 * @throws None
 * @return array An array of WP_Post objects representing the retrieved posts.
 */
function oo_get_posts( $user_args = array() ){
	$info_path = basename(__FILE__, '.php') . '.php->' . __FUNCTION__ . '.txt';
	
	$default_args = array(
		'posts_per_page' => 10,
		'post_status' => 'publish'
	);

	$args = array_merge( $default_args, $user_args );
	
	error_log("\noo_get_posts\n".print_r($args, true)."\n"); 
	document_info($info_path, "ARGS", array(
		'default' => $default_args,
		'user' => $user_args,
		'args' => $args
	));
	
	$results = new WP_Query( $args );
	document_info($info_path, "Results", $results, true);
	
	$posts = $results->posts;
	document_info($info_path, "Posts", $posts, true);

	$full_posts = array();
	foreach( $posts as $post ){
		$full_posts[] = get_full_post( $post );
	}
	document_info($info_path, "Full Posts", $full_posts, true);	
	
	return $full_posts;
}


add_action('wp_ajax_get_async_posts', 'oo_get_async_posts');
add_action('wp_ajax_nopriv_get_async_posts', 'oo_get_async_posts');
function oo_get_async_posts( ){
	$info_path = basename(__FILE__, '.php') . '.php->' . __FUNCTION__ . '.txt';

	document_info($info_path, '$_POST', $_POST);
	document_info($info_path, '$_GET', $_GET, true);

	$args = array();
	if (isset($_POST['args']) && !empty($_POST['args'])) {
		document_info($info_path, "IS SET && NOT EMPTY ARGS", $_POST['args'], true);
		$args = decode_args( $_POST['args'] );
		document_info($info_path, "decoded args", $args, true);
	}else{
		document_info($info_path, "IS NOT SET || EMPTY ARGS", $_POST['args'], true);
		$args = array('post_type' => 'pepino');
	}

	document_info($info_path, "ARGS", $args, true);

	$posts = oo_get_posts( $args );

	document_info($info_path, "Posts", $posts, true);

	echo json_encode( $posts ) ;
	wp_die();
}
//! GET POSTS



//* GET SINGLE POST
function oo_get_unique_post($user_args = array())
{
	$info_path = basename(__FILE__, '.php') . '.php->' . __FUNCTION__ . '.txt';

	$default_args = array(
		'posts_per_page' => 1,
		'post_status' => 'publish'
	);

	$args = array_merge($default_args, $user_args);

	document_info($info_path, "ARGS", array(
		'default' => $default_args,
		'user' => $user_args,
		'args' => $args
	)
	);

	$results = new WP_Query($args);
	document_info($info_path, "Results", $results, true);

	$post = $results->posts;
	document_info($info_path, "Post", $post, true);

	$full_post = get_full_post( $post[0] );
	document_info($info_path, "Full Post", $full_post, true);

	return $full_post;
}


/**
 * Devuelve el Post como un array asociativo con todos sus metadatos y datos de interés (taxonomias, url del thumbnail, etc)
 *
 * @param mixed $post The post object or ID.
 * @throws Exception if an error occurs.
 * @return array The post information including post array, post meta, thumbnail, post taxonomies, and post terms.
 */
function get_full_post( $post ){
	$info_path = basename(__FILE__, '.php') . '.php->' . __FUNCTION__ . '.txt';

	$post_arr = (array) $post;
	document_info($info_path, "Post Arr", $post_arr, true);

	$post_meta = get_post_meta($post_arr['ID']);
	document_info($info_path, "Post Meta", $post_meta, true);
	$post_arr['meta'] = $post_meta;

	$post_arr['thumbnail'] = get_the_post_thumbnail_url($post_arr['ID']);

	$post_taxonomies = get_post_taxonomies($post_arr['ID']);
	document_info($info_path, "Post Taxonomies", $post_taxonomies, true);

	$post_terms = array();
	foreach ($post_taxonomies as $taxonomy) {
		$post_terms[$taxonomy] = get_the_terms($post_arr['ID'], $taxonomy);
	}
	document_info($info_path, "Post Terms", $post_terms, true);

	$post_arr['terms'] = $post_terms;
	return $post_arr;
}


add_action('wp_ajax_get_async_unique_post', 'oo_get_async_unique_post');
add_action('wp_ajax_nopriv_get_async_unique_post', 'oo_get_async_unique_post');
function oo_get_async_unique_post()
{
	$info_path = basename(__FILE__, '.php') . '.php->' . __FUNCTION__ . '.txt';

	document_info($info_path, '$_POST', $_POST);
	document_info($info_path, '$_GET', $_GET, true);

	$args = array();
	if (isset($_POST['args']) && !empty($_POST['args'])) {
		document_info($info_path, "IS SET && NOT EMPTY ARGS", $_POST['args'], true);
		$args = decode_args($_POST['args']);
		document_info($info_path, "decoded args", $args, true);
	} else {
		document_info($info_path, "IS NOT SET || EMPTY ARGS", $_POST['args'], true);
		$args = array('post_type' => 'pepino');
	}

	document_info($info_path, "ARGS", $args, true);

	$post = oo_get_unique_post($args);

	document_info($info_path, "Post", $post, true);

	echo json_encode($post);
	wp_die();
}
//! GET SINGLE POST


/**
 * Get metadata of a given post.
 *
 * @param mixed $post The post object or post ID.
 */
function oo_get_metadata( $post ){
	$info_path = basename(__FILE__, '.php') . '.php->' . __FUNCTION__ . '.txt';

	if( gettype( $post ) == 'object'){
		$post_id = $post->ID;
	}else{
		$post_id = $post;
	}

	document_info($info_path, "POST($post_id)", $post);

	$post_meta = get_post_meta( $post_id );
	document_info($info_path, "POST_META", $post_meta);

	return $post_meta;
}

/**
 * Generates a list of posts in HTML format.
 *
 * Example: [oo_post_list excerpt=0]
 * 
 * @param array|null $atts An associative array of attributes for the post list.
 *   - post_type (string): The post type to display. Default is 'post'.
 *   - thumbnail (bool): Whether to display the post thumbnail. Default is true.
 *   - excerpt (bool): Whether to display the post excerpt. Default is true.
 *   - offset (int): The number of posts to skip. Default is 0.
 *   - posts_per_page (int): The number of posts to display. Default is 5.
 * @param null $content Not used in this function.
 * @return string The HTML code for the post list.
 */
function oo_post_list_func( $atts=null, $content=null ){
	$info_path = basename(__FILE__, '.php') . '.php->' . __FUNCTION__ . '.txt';
	document_info($info_path, "ARGS", array(
			'atts' => $atts,
			'content' => $content
		)
	);

	if ($atts) {
		$post_type = isset($atts['post_type']) ? $atts['post_type'] : 'post';
		$thumbnail = isset($atts['thumbnail']) ? $atts['thumbnail'] : true;
		$excerpt = isset($atts['excerpt']) ? $atts['excerpt'] : true;
		$offset = isset($atts['offset']) ? $atts['offset'] : 0;
		$posts_per_page = isset($atts['posts_per_page']) ? $atts['posts_per_page'] : 5;
	}

	$args = array(
		'post_type' => $post_type,
		'posts_per_page' => $posts_per_page,
		'offset' => $offset
	);

	$posts = oo_get_posts( $args );
	document_info($info_path, "Posts", $posts, true);

	$html = <<<EOT
	<div class="oo-post-list-container">
		<div class="oo-post-list-wrapper">
			<ul class="oo-post-list">
	EOT;
				foreach( $posts as $post ){
					$html .= '<li class="oo-post-list-item"><a href="'.$post['guid'].'">';
						if( $thumbnail ){
							$html .= '<div class="oo-post-thumbnail"><img src="'.$post['thumbnail'].'"></div>';
						}
						$html .= '<div class="oo-post-title"><h3>'.$post['post_title'].'</h3></div>';
						if( $excerpt ){
							$html .= '<div class="oo-post-excerpt">'.$post['post_excerpt'].'</div>';
						}
					$html .= '</a></li>';
				}
	$html .= <<<EOT
			</ul>
		</div>
	</div>
	EOT;

	echo "<h2>POST LIST</h2>";
	return $html;
}
/* [oo_post_list excerpt=0] */
add_shortcode('oo_post_list', 'oo_post_list_func');
