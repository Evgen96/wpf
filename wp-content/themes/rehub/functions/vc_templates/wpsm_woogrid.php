<?php 
    $type = $enable_pagination = $data_source = $cat = $ids = $orderby = $order = $show = $columns = $no_border = '';
    extract(shortcode_atts(array(
        'type' => '',
        'enable_pagination' => '',
        'data_source' => '',
        'cat' => '',
        'ids' => '',
        'orderby' => '',
        'order' => 'DESC',
        'show' => '',  
        'columns' => '3_col', 
        'no_border' => ''   
    ), $atts));  
    $no_border = ($no_border !='') ? ' no_boxed' : '';     
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
<?php $wp_query = new WP_Query( $args ); $i=1; if ( $wp_query->have_posts() ) : ?>                      
<?php while ( $wp_query->have_posts() ) : $wp_query->the_post();  global $product;  ?>
    
    <?php $offer_price = $product->get_price_html() ?>
    <?php $offer_desc = get_the_excerpt() ?>
    <?php $offer_url = get_post_permalink(get_the_ID()) ?>
    <?php $offer_title = $product->get_title() ?>
    
    
    <?php if ($columns == '3_col') :?>
    <div class="offer_grid woocommerce yith_float_btns column_grid<?php if (($i % 3) == '0') :?> last-col<?php endif ?><?php if (($i % 3) == '1') :?> first-col<?php endif ?><?php echo $no_border;?>">
    <?php elseif ($columns == '4_col') :?>
    <div class="offer_grid woocommerce yith_float_btns col_4_grid column_grid<?php if (($i % 4) == '0') :?> last-col<?php endif ?><?php if (($i % 4) == '1') :?> first-col<?php endif ?><?php echo $no_border;?>">      
    <?php endif ;?>

        <div class="aff_grid_top">
            <div class="aff_tag">
                <?php $product_cats = strip_tags($product->get_categories('|||', '', '')); ?>
                <a href="<?php the_permalink(); ?>"><?php list($firstpart) = explode('|||', $product_cats); echo $firstpart; ?></a>          
            </div>
        </div> 

        <div class="image_container imagechange3"> 
            <div class="button_action"> 
                <?php if (in_array( 'yith-woocommerce-compare/init.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) )  { ?>               
                    <?php echo do_shortcode('[yith_compare_button]'); ?>                
                <?php } ?>
                <?php if (in_array( 'yith-woocommerce-wishlist/init.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) )  { ?> 
                    <?php echo do_shortcode('[yith_wcwl_add_to_wishlist]'); ?> 
                <?php } ?>                                       
            </div>            
            <a href="<?php the_permalink(); ?>" class="prodimglink">
                <?php if ($product->is_on_sale()) : ?><div class="sale_tag"><?php _e('Sale!', 'rehub_framework')?></div><?php endif ?>
                <div class="loop_product front"><?php echo get_the_post_thumbnail( get_the_ID(), 'news_big') ?></div>
                <?php $attachment_ids = $product->get_gallery_attachment_ids();?>
                    <?php if ( $attachment_ids ) :?>                    
                        <?php $loop = 0;                    
                        foreach ( $attachment_ids as $attachment_id ) {
                            $image_link = wp_get_attachment_url( $attachment_id );
                            if ( ! $image_link )
                                continue;
                            $loop++;
                            printf( '<div class="loop_products back">%s</div>', wp_get_attachment_image( $attachment_id, 'news_big' ) );
                            if ($loop == 1) break;
                        }
                        ?>
                    <?php else : ?>
                    <div class="loop_products back"><?php wpsm_thumb( 'news_big') ?></div>
                    <?php endif ;?>
            </a>
                <div class="clearfix"></div>                        
        </div>

        <div class="desc_col">
            <h4><a href="<?php echo $offer_url ;?>"><?php echo $offer_title ;?></a></h4>
            <div class="r_offer_details">
                <?php if (!empty($offer_desc)) :?>
                    <span id="r_show_hide"><?php _e('Details +', 'rehub_framework') ?></span>
                    <p><?php echo $offer_desc ;?></p>
                <?php endif ;?>    
            </div>
        </div>  

        <div class="buttons_col">
            <div class="grid_price_count"><?php do_action( 'woocommerce_after_shop_loop_item_title' ); ?></div>
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
<?php $i++; endwhile; endif;  wp_reset_postdata(); ?>    
<div class="clearfix"></div>

<?php if ($enable_pagination != '') :?>
    <div class="pagination"><?php rehub_pagination()?></div>
<?php endif ;?>
<?php  $wp_query = null; $wp_query = $temp; wp_reset_postdata(); ?>