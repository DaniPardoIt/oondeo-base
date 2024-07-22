
<?php 

add_shortcode('oo_woo_mini_cart', 'oo_woo_mini_cart_func');
function oo_woo_mini_cart_func(){
	$info_path = basename(__FILE__, '.php') . '.php->' . __FUNCTION__ . '.txt';


	$cart_content_count = WC()->cart->get_cart_contents_count();
	$cart_content_subtotal = WC()->cart->get_cart_subtotal();
	$subtotal = WC()->cart->subtotal;

	document_info( $info_path, 'Woocommerce Info', array(
		'cart_content_count' => $cart_content_count,
		'cart_content_subtotal' => $cart_content_subtotal,
		'subtotal' => $subtotal,
	) );

	document_info( $info_path, 'Woocommerce Checks', array(
		'class_exists' => class_exists( 'woocommerce' )
	), true );

	$html = <<<EOT
		<div class="oo-woo-mini-cart">
			<a href="/carrito" class="oo-woo-mini-cart-button">
				<i class="fa fa-shopping-cart" aria-hidden="true"></i>
				<div class="basket-item-count">
					<span class="cart-items-count count">
						{$cart_content_count}
					</span>
				</div>
				<div class="basket-items-subtotal">
					<span>Total: </span>
					{$cart_content_subtotal}
				</div>
			</a>
		</div>
	EOT;

	echo $html;
}

function woocommerce_product_card( $post ){
	$info_path = basename(__FILE__, '.php') . '.php->' . __FUNCTION__ . '.txt';

	document_info( $info_path, 'POST', $post );
	$html = '';
}
