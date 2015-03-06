<?php
//////////////////////////////////////////////////////////////////
// WooCommerce
//////////////////////////////////////////////////////////////////
if (class_exists('Woocommerce')) {
	if ( version_compare( WOOCOMMERCE_VERSION, "2.1" ) >= 0 ) {
	   add_filter( 'woocommerce_enqueue_styles', '__return_false' );
	} else {
	   define( 'WOOCOMMERCE_USE_CSS', false );
	}
}
// Display number products per page.
add_filter( 'loop_shop_per_page', create_function( '$cols', 'return 12;' ), 20 );


add_action('woocommerce_before_shop_loop', 'rehub_woocommerce_wrapper_start3', 33);
function rehub_woocommerce_wrapper_start3() {
  echo '<div class="clear"></div>';
}


global $pagenow;
if ( is_admin() && isset( $_GET['activated'] ) && $pagenow == 'themes.php' )
	add_action( 'init', 'rehub_woocommerce_image_dimensions', 1 );

if( !function_exists('rehub_woocommerce_image_dimensions') ) {
function rehub_woocommerce_image_dimensions() {
  	$catalog = array(
		'width' 	=> '400',	// px
		'height'	=> '400',	// px
		'crop'		=> 1 		// true
	);
 
	$single = array(
		'width' 	=> '600',	// px
		'height'	=> '600',	// px
		'crop'		=> 1 		// true
	);
 
	$thumbnail = array(
		'width' 	=> '200',	// px
		'height'	=> '200',	// px
		'crop'		=> 1 		// false
	);
 
	// Image sizes
	update_option( 'shop_catalog_image_size', $catalog ); 		// Product category thumbs
	update_option( 'shop_single_image_size', $single ); 		// Single product image
	update_option( 'shop_thumbnail_image_size', $thumbnail ); 	// Image gallery thumbs
}
}

// Ensure cart contents update when products are added to the cart via AJAX (place the following in functions.php)
add_filter('add_to_cart_fragments', 'woocommerce_header_add_to_cart_fragment');

if( !function_exists('woocommerce_header_add_to_cart_fragment') ) { 
function woocommerce_header_add_to_cart_fragment( $fragments ) {
	global $woocommerce;
	ob_start();
	?>
		<a class="cart-contents" href="<?php echo $woocommerce->cart->get_cart_url(); ?>"><i class="fa fa-shopping-cart"></i> <?php _e( 'Cart', 'rehub_framework' ); ?> (<?php echo $woocommerce->cart->cart_contents_count; ?>) - <?php echo $woocommerce->cart->get_cart_total(); ?></a>
	<?php
	$fragments['a.cart-contents'] = ob_get_clean();
	return $fragments;
}
}

if( !function_exists('woo_dealslinks_rehub') ) {
function woo_dealslinks_rehub() {
?>
<div class="deals_woo_rehub">
	<div class="title_deal_wrap"><div class="title_deal"><?php _e('Choose your deal', 'rehub_framework'); ?></div></div>
	<?php $rehub_aff_post_ids = vp_metabox('rehub_framework_woo.review_woo_links');
	if(function_exists('thirstyInit') && !empty($rehub_aff_post_ids)) :?>
		<div class="wooaff_offer_links">
		<?php 
		$rehub_aff_posts = get_posts(array(
			'post_type'        => 'thirstylink',
			'post__in' => $rehub_aff_post_ids,
			'meta_key' => 'rehub_aff_sticky',
			'orderby' => 'meta_value',
			'order' => 'DESC',
			'numberposts' => '-1'			
		));
		foreach($rehub_aff_posts as $aff_post) { ?>	
			<?php 	$attachments = get_posts( array(
	            'post_type' => 'attachment',
				'post_mime_type' => 'image',
	            'posts_per_page' => -1,
	            'post_parent' => $aff_post->ID,
        	) );
			if (!empty($attachments)) {$aff_thumb_list = wp_get_attachment_url( $attachments[0]->ID );} else {$aff_thumb_list ='';}
			$term_list = wp_get_post_terms($aff_post->ID, 'thirstylink-category', array("fields" => "names")); 
			$term_ids =  wp_get_post_terms($aff_post->ID, 'thirstylink-category', array("fields" => "ids")); if (!empty($term_ids)) {$term_brand = $term_ids[0]; $term_brand_image = get_option("taxonomy_term_$term_ids[0]");} else {$term_brand_image ='';}
			?>
			<div class="woorow_aff">
				<div class="product-pic-wrapper">
					<a href="<?php echo get_post_permalink($aff_post) ?>">
						<?php if (!empty($aff_thumb_list) ) :?>	
	            			<img src="<?php $params = array( 'width' => 100, 'height' => 100 ); echo bfi_thumb( $aff_thumb_list, $params ); ?>" alt="<?php echo $aff_post->post_title; ?>" />
	            		<?php elseif (!empty($term_brand_image['brand_image'])) :?>
	            			<img src="<?php $params = array( 'width' => 100, 'height' => 100 ); echo bfi_thumb( $term_brand_image['brand_image'], $params ); ?>" alt="<?php echo $aff_post->post_title; ?>" />
	            		<?php else :?>
	            			<img src="<?php echo get_template_directory_uri(); ?>/images/default/noimage_100_70.png" alt="<?php echo $aff_post->post_title; ?>" />
	            		<?php endif?>
	            	</a>				
				</div>
				<div class="product-details">
					<div class="product-name">
						<div class="aff_name"><a href="<?php echo get_post_permalink($aff_post) ?>"><?php echo $aff_post->post_title; ?></a></div>
						<p><?php echo get_post_meta( $aff_post->ID, 'rehub_aff_desc', true );?></p>
					</div>
					<div class="left_data_aff">
						<div class="wooprice_count">
							<?php $product_price = get_post_meta( $aff_post->ID, 'rehub_aff_price', true );?>
							<?php echo $product_price ;?>
						</div>					
						<div class="wooaff_tag">
				            <?php if (!empty($term_brand_image['brand_image'])) :?>
				            	<img src="<?php $params = array( 'width' => 100, 'height' => 100 ); echo bfi_thumb( $term_brand_image['brand_image'], $params ); ?>" alt="<?php the_title_attribute(); ?>" />
				            <?php elseif (!empty($term_list[0])) :?> 
				            	<?php echo $term_list[0]; ?>
				            <?php endif; ?> 							
						</div>
					</div>	
					<?php $offer_btn_text = get_post_meta( $aff_post->ID, 'rehub_aff_btn_text', true ) ?>				
					<div class="woobuy_butt">
						<a class="woobtn_offer_block" href="<?php echo get_post_permalink($aff_post) ?>" target="_blank" rel="nofollow"><?php if($offer_btn_text !='') :?><?php echo $offer_btn_text ; ?><?php elseif(rehub_option('rehub_btn_text') !='') :?><?php echo rehub_option('rehub_btn_text') ; ?><?php else :?><?php _e('See it', 'rehub_framework') ?><?php endif ;?></a>
					</div>
				</div>
			</div>	
		<?php 
		}
		?>
		</div>
	<?php endif;?>
</div>
<?php
}
}
          
add_action( 'woocommerce_after_single_product_summary', 'woo_dealslinks_rehub', 9 ); //add affiliate links to woocommerce

?>