
<?php


function grid_oondeo_func( $atts=null, $content=null ){
	$info_path = basename(__FILE__, '.php') . '.php->' . __FUNCTION__ . '.txt';

	document_info($info_path, "ARGS", array(
		'atts' => $atts,
		'content' => $content
	));
	
	$tax_query = false;
	if( $atts ){
		$post_type = isset($atts['post_type']) ? $atts['post_type'] : 'post';
		$grid = isset($atts['grid']) ? $atts['grid'] : 4;
		$pagination = isset($atts['pagination']) ? $atts['pagination'] : false;
		$posts_per_page = isset($atts['posts_per_page']) ? $atts['posts_per_page'] : $grid;
		if(isset($atts['taxonomy']) && isset($atts['term']) && isset($atts['term'])){
			$tax_query = array(array());
			$tax_query[0]['taxonomy'] = isset($atts['taxonomy']) ? $atts['taxonomy'] : '';
			$tax_query[0]['field'] = isset($atts['tax_field']) ? $atts['tax_field'] : '';
			$tax_query[0]['terms'] = isset($atts['term']) ? array($atts['term']) : array();
		}
		$button_text = isset($atts['button_text']) ? $atts['button_text'] : 'Ver más';
	}

	$args = array(
		'post_type' => $post_type,
		'posts_per_page' => $grid,
		'posts_per_page' => $posts_per_page
	);

	if( $tax_query ){
		$args['tax_query'] = $tax_query;
	}

	$posts = oo_get_posts( $args );
	document_info($info_path, "Posts", $posts, true);

	$html = <<<EOT
	<div class="oondeo-grid grid-$grid-col">
	EOT;

	// Opción por defecto de la imagen(thumbnail), hay una genérica, y una por cada categoría (curso, consultoria)
	$default_thumbnail = '/wp-content/uploads/2023/08/img-defecto.png';
	foreach( $posts as $post ){
		switch ($tax_query[0]['terms'][0]) {
			case "curso":
				$default_thumbnail = '/wp-content/uploads/2023/08/img-defecto-curso.png';
				break;
			
			case "consultoria":
				$default_thumbnail = '/wp-content/uploads/2023/08/img-defecto-videoconsultoria.png';
				break;
		}
		$thumbnail = (isset($post['thumbnail']) && !empty($post['thumbnail'])) ? $post['thumbnail'] : $default_thumbnail;

		$content_summary = substr( $post["post_content"], 0, 100 );

		$html .= <<<EOT
		<div class="grid-item">
			<a class="grid-card" href="{$post["guid"]}" class="grid-item-inner">
				<div class="grid-card-img">
					<img src="$thumbnail" />
				</div>
				<div class="grid-card-body">
					<div class="grid-item-title">
						<h3>{$post["post_title"]}</h3>
					</div>
					<div class="grid-item-content">
						<p>{$content_summary}</p>
					</div>
					<div class="grid-item-more">
						<button>$button_text</button>
					</div>
				</div>
			</a>
		</div>
		EOT;
	}

	$html .= "</div>";

	return $html;
}
/* [grid_oondeo post_type="lp_course" grid=3 taxonomy='course_category' tax_field='slug' term='consultoria' posts_per_page=-1 button_text='Ver detalles del curso'] */
add_shortcode('grid_oondeo', 'grid_oondeo_func');


function get_woocommerce_product_card( $post_id ){
	
}