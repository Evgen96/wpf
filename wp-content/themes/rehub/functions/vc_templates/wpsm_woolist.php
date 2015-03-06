<?php 
    $type = $enable_pagination = $data_source = $cat = $ids = $orderby = $order = $show = '';
    extract(shortcode_atts(array(
        'type' => '',
        'enable_pagination' => '',
        'data_source' => '',
        'cat' => '',
        'ids' => '',
        'orderby' => '',
        'order' => 'DESC',
        'show' => '',      
    ), $atts));         
?>
<?php
	if ( get_query_var('paged') ) { $paged = get_query_var('paged'); } else if ( get_query_var('page') ) {$paged = get_query_var('page'); } else {$paged = 1; }
    if ($data_source == 'ids' && $ids !='') {
        $ids = explode(',', $ids);
        $args = array(
            'post__in' => $ids,
            'numberposts' => '-1',
            'orderby' => 'post__in', 
            'post_type' => 'product',
            'ignore_sticky_posts'   => 1,           
        );
    }
    else {
        $args = array(
            'post_type' => 'product',
            'posts_per_page'   => $show, 
            'orderby' => $orderby,
            'order' => $order,
            'ignore_sticky_posts' => 1,                  
        );
        if ($enable_pagination != '') {$args['paged'] = $paged;}
        if ($data_source == 'cat' && $cat !='') {
            $cat = explode(',', $cat);
            $args['tax_query'] = array(array('taxonomy' => 'product_cat', 'terms' => $cat, 'field' => 'id'));
        }
        if ($data_source == 'type') {
            if($type =='featured') {$args['meta_query']=array(array('key' => '_featured', 'value' => 'yes'));}
            elseif($type =='sale') {
                $product_ids_on_sale = wc_get_product_ids_on_sale();
                $meta_query   = array();
                $meta_query[] = WC()->query->visibility_meta_query();
                $meta_query[] = WC()->query->stock_status_meta_query();
                $meta_query   = array_filter( $meta_query );
                $args['meta_query'] = $meta_query;
                $args['post__in'] = array_merge( array( 0 ), $product_ids_on_sale );
                $args['no_found_rows'] = 1;
            }
            elseif($type =='best_sale') {$args['meta_key']='total_sales'; $args['orderby']='meta_value_num';}
        }
    }    
    global $post; global $woocommerce; global $wp_query; $temp = $wp_query; $wp_query = null;  
?>
<?php $wp_query = new WP_Query( $args ); if ( $wp_query->have_posts() ) : ?>                      
<?php while ( $wp_query->have_posts() ) : $wp_query->the_post();  global $product;  ?>
    
    <?php $offer_price = $product->get_price_html() ?>
    <?php $offer_desc = get_the_excerpt() ?>
    <?php $offer_url = get_post_permalink(get_the_ID()) ?>
    <?php $offer_title = $product->get_title() ?> 

    <article class="rehub_listing woocommerce yith_float_btns">
        <div class="offer_thumb">
            <?php if ($product->is_on_sale()) : ?><div class="sale_tag"><?php _e('Sale!', 'rehub_framework')?></div><?php endif ?>
            <div class="button_action"> 
                <?php if (in_array( 'yith-woocommerce-compare/init.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) )  { ?>               
                    <?php echo do_shortcode('[yith_compare_button]'); ?>                
                <?php } ?>
                <?php if (in_array( 'yith-woocommerce-wishlist/init.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) )  { ?> 
                    <?php echo do_shortcode('[yith_wcwl_add_to_wishlist]'); ?> 
                <?php } ?>                                       
            </div>   
            <a href="<?php echo $offer_url ;?>"><?php wpsm_thumb('news_big') ;?></a>
        </div>
        <div class="listing_text">
            <div class="desc_col">
                <h3 class="offer_title"><a href="<?php echo $offer_url ;?>"><?php echo $offer_title ;?></a></h3>
                <?php do_action( 'woocommerce_after_shop_loop_item_title' ); ?>
                <p><?php kama_excerpt('maxchar=200'); ?></p>
            </div>
            <div class="buttons_col">
                <?php if ( $product->is_in_stock() &&  $product->add_to_cart_url() !='') : ?>
                 <?php  echo apply_filters( 'woocommerce_loop_add_to_cart_link',
                        sprintf( '<a href="%s" rel="nofollow" data-product_id="%s" data-product_sku="%s" class="quick_buy %s product_type_%s"%s>%s</a>',
                        esc_url( $product->add_to_cart_url() ),
                        esc_attr( $product->id ),
                        esc_attr( $product->get_sku() ),
                        $product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : 'add_to_cart_button',
                        esc_attr( $product->product_type ),
                        $product->product_type =='external' ? ' target="_blank"' : '',
                        esc_html( $product->add_to_cart_text() )
                        ),
                $product );?>
                <?php endif; ?>
            </div> 
        </div>
                                            
    </article>
    <div class="clearfix"></div>

<?php endwhile; endif; ?>    


<?php if ($enable_pagination != '') :?>
    <div class="pagination"><?php rehub_pagination()?></div>
<?php endif ;?>
<?php  $wp_query = null; $wp_query = $temp; wp_reset_postdata(); ?>