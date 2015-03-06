<?php

//////////////////////////////////////////////////////////////////
// Register sidebar and footer widgets
//////////////////////////////////////////////////////////////////


if( !function_exists('rehub_register_sidebars') ) {
function rehub_register_sidebars() {

	register_sidebar(array(
		'id' => 'sidebar-1',
		'name' => __('Sidebar Area', 'rehub_framework'),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<div class="title">',
		'after_title' => '</div>',
	));
	register_sidebar(array(
		'id' => 'sidebar-2',
		'name' => __('Footer 1', 'rehub_framework'),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<div class="title_b">',
		'after_title' => '</div>',
	));
	register_sidebar(array(
		'id' => 'sidebar-3',
		'name' => __('Footer 2', 'rehub_framework'),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<div class="title_b">',
		'after_title' => '</div>',
	));
	register_sidebar(array(
		'id' => 'sidebar-4',
		'name' => __('Footer 3', 'rehub_framework'),
		'before_widget' => '<div id="%1$s" class="widget last %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<div class="title_b">',
		'after_title' => '</div>',
	));
	register_sidebar(array(
		'id' => 'sidebar-5',
		'name' => __('Ecwid widget', 'rehub_framework'),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<div class="title_ecwid">',
		'after_title' => '</div>',
	));	
}
}
add_action( 'widgets_init', 'rehub_register_sidebars' );
 
//////////////////////////////////////////////////////////////////
// Include widgets
//////////////////////////////////////////////////////////////////


include (TEMPLATEPATH . '/inc/widgets/tabs_widget.php');
include (TEMPLATEPATH . '/inc/widgets/posts_list.php');
include (TEMPLATEPATH . '/inc/widgets/featured_sidebar.php');
include (TEMPLATEPATH . '/inc/widgets/latest_video.php');
include (TEMPLATEPATH . '/inc/widgets/feedburner_widget.php');
include (TEMPLATEPATH . '/inc/widgets/login_widget.php');
include (TEMPLATEPATH . '/inc/widgets/facebook_widget.php');
include (TEMPLATEPATH . '/inc/widgets/social_link_widget.php');
include (TEMPLATEPATH . '/inc/widgets/sticky_scroll.php');
include (TEMPLATEPATH . '/inc/widgets/related_reviews.php');
include (TEMPLATEPATH . '/inc/widgets/outer_ads.php');



//////////////////////////////////////////////////////////////////
// Sidebar widget functions
//////////////////////////////////////////////////////////////////

if( !function_exists('rehub_most_popular_widget_block') ) {
function rehub_most_popular_widget_block($basedby = 'comments') { ?>

	<?php 
	global $post;
	if ($basedby == 'views') {$popular_posts = new WP_Query('showposts=5&meta_key=rehub_views&orderby=meta_value_num&order=DESC&ignore_sticky_posts=1');}
	else {$popular_posts = new WP_Query('showposts=5&orderby=comment_count&order=DESC&ignore_sticky_posts=1');}	
	if($popular_posts->have_posts()): ?>
	
	
		<?php  while ($popular_posts->have_posts()) : $popular_posts->the_post(); ?>
		
			<div class="clearfix">
	            <figure><a href="<?php the_permalink();?>"><?php wpsm_thumb ('med_thumbs') ?></a></figure>
	            <div class="detail">
		            <h5><a href="<?php the_permalink();?>"><?php the_title();?></a></h5>
	            	<div class="rcnt_meta">
	              		<?php $category = get_the_category($post->ID); $first_cat = $category[0]->term_id;?>
	                	<?php if ($basedby == 'views') {meta_small( false, $first_cat, false, true );} else {meta_small( false, $first_cat, true, false );}  ?>
	                </div>
	                <?php rehub_format_score('small') ?>
	            </div>
            </div>
		
		<?php endwhile; ?>
		<?php wp_reset_query(); ?>
		<?php endif; 
		remove_filter('posts_where', 'filter_where_month'); 
		remove_filter('posts_where', 'filter_where_t_month'); 
		remove_filter('posts_where', 'filter_where_week');?>


<?php
}
}

if( !function_exists('rehub_latest_comment_widget_block') ) {
function rehub_latest_comment_widget_block() { ?>
<div class="last_comments_widget">

	<?php
	global $wpdb;
	$sql = "SELECT DISTINCT ID, post_title, post_password, comment_ID, comment_post_ID, comment_author, comment_author_email, comment_date_gmt, comment_approved, comment_type, comment_author_url, SUBSTRING(comment_content,1,78) AS com_excerpt FROM $wpdb->comments LEFT OUTER JOIN $wpdb->posts ON ($wpdb->comments.comment_post_ID = $wpdb->posts.ID) WHERE comment_approved = '1' AND comment_type = '' AND post_password = '' ORDER BY comment_date_gmt DESC LIMIT 5";
	$comments = $wpdb->get_results($sql);
	foreach ($comments as $comment) { 
	?>
		<div class="lastcomm-item">
			<div class="side-item comment">	
				<?php echo get_avatar( $comment, '40' ); ?>
				<div>
					<span><strong><?php echo strip_tags($comment->comment_author); ?></strong></span> 
					<?php echo strip_tags($comment->com_excerpt); ?>...
					<span class="lastcomm-cat">
						<a href="<?php echo get_permalink($comment->ID); ?>#comment-<?php echo $comment->comment_ID; ?>" title="<?php echo strip_tags($comment->comment_author); ?> - <?php echo $comment->post_title; ?>"><?php echo $comment->post_title; ?></a>
					</span>		
				</div>
			</div>
		</div>

	<?php } ?>

</div>
<?php
}
}

if( !function_exists('rehub_category_widget_block') ) {
function rehub_category_widget_block() { ?>

<div class="category_tab">
	<ul class="cat_widget_custom">
	<?php
		$variable = wp_list_categories('echo=0&show_count=1&title_li=');
		$variable = str_replace('</a> (', '</a> <span class="counts">', $variable);
  		$variable = str_replace(')', '</span>', $variable);
		echo $variable;
	?>
	</ul>
</div>

<?php
}
}

if( !function_exists('rehub_login_form') ) {
function rehub_login_form( $login_only  = 0 ) {
	global $user_ID, $user_identity, $user_level;
	
	if ( $user_ID ) : ?>
		<?php if( empty( $login_only ) ): ?>
		<div id="user-login">
			<p class="welcome-frase"><?php _e( 'Welcome' , 'rehub_framework' ) ?> <strong><?php echo $user_identity ?></strong></p>
			<span class="author-avatar"><?php echo get_avatar( $user_ID, $size = '60'); ?></span>
			<ul>
				<li><a href="<?php echo home_url() ?>/wp-admin/"><?php _e( 'Dashboard' , 'rehub_framework' ) ?> </a></li>
				<li><a href="<?php echo home_url() ?>/wp-admin/profile.php"><?php _e( 'Your Profile' , 'rehub_framework' ) ?> </a></li>
				<li><a href="<?php echo wp_logout_url(); ?>"><?php _e( 'Logout' , 'rehub_framework' ) ?> </a></li>
			</ul>
			<div class="clear"></div>
		</div>
		<?php endif; ?>
	<?php else: ?>
		<div id="login-form">
			<form action="<?php echo home_url() ?>/wp-login.php" method="post">
				<p id="log-username"><input type="text" class="def_inp" name="log" id="log" value="<?php _e( 'Username' , 'rehub_framework' ) ?>" onfocus="if (this.value == '<?php _e( 'Username' , 'rehub_framework' ) ?>') {this.value = '';}" onblur="if (this.value == '') {this.value = '<?php _e( 'Username' , 'rehub_framework' ) ?>';}"  size="33" /></p>
				<p id="log-pass"><input type="password" class="def_inp" name="pwd" id="pwd" value="<?php _e( 'Password' , 'rehub_framework' ) ?>" onfocus="if (this.value == '<?php _e( 'Password' , 'rehub_framework' ) ?>') {this.value = '';}" onblur="if (this.value == '') {this.value = '<?php _e( 'Password' , 'rehub_framework' ) ?>';}" size="33" /></p>
				<input type="submit" name="submit" value="<?php _e( 'Log in' , 'rehub_framework' ) ?>" class="def_btn sys_btn" />
				<label for="rememberme"><input name="rememberme" id="rememberme" type="checkbox" checked="checked" value="forever" /> <?php _e( 'Remember Me' , 'rehub_framework' ) ?></label>
				<input type="hidden" name="redirect_to" value="<?php echo $_SERVER['REQUEST_URI']; ?>"/>
			</form>
			<ul class="login-links">
				<?php if ( get_option('users_can_register') ) : ?><?php echo wp_register() ?><?php endif; ?>
				<li><a href="<?php echo home_url() ?>/wp-login.php?action=lostpassword"><?php _e( 'Lost your password?' , 'rehub_framework' ) ?></a></li>
			</ul>
		</div>
	<?php endif;
}
}

if( !function_exists('rehub_get_social_links') ) {
function rehub_get_social_links($icon_size='big'){
?>
	<div class="social_icon <?php echo $icon_size; ?>_i">
		

		<?php if ( rehub_option('rehub_facebook') != '' ) :?>
			<a href="<?php echo rehub_option('rehub_facebook'); ?>" class="fb" rel="nofollow"><i class="fa fa-facebook"></i></a>
		<?php endif;?>	

		<?php if ( rehub_option('rehub_twitter') != '' ) :?>
			<a href="<?php echo rehub_option('rehub_twitter'); ?>" class="tw" rel="nofollow"><i class="fa fa-twitter"></i></a>
		<?php endif;?>

		<?php if ( rehub_option('rehub_google') != '' ) :?>
			<a href="<?php echo rehub_option('rehub_google'); ?>" class="gp" rel="nofollow"><i class="fa fa-google-plus"></i></a>
		<?php endif;?>

		<?php if ( rehub_option('rehub_instagram') != '' ) :?>
			<a href="<?php echo rehub_option('rehub_instagram'); ?>" class="ins" rel="nofollow"><i class="fa fa-instagram"></i></a>
		<?php endif;?>

		<?php if ( rehub_option('rehub_tumblr') != '' ) :?>
			<a href="<?php echo rehub_option('rehub_tumblr'); ?>" class="tm" rel="nofollow"><i class="fa fa-tumblr"></i></a>
		<?php endif;?>	

		<?php if ( rehub_option('rehub_youtube') != '' ) :?>
			<a href="<?php echo rehub_option('rehub_youtube'); ?>" class="yt" rel="nofollow"><i class="fa fa-youtube"></i></a>
		<?php endif;?>

		<?php if ( rehub_option('rehub_vimeo') != '' ) :?>
			<a href="<?php echo rehub_option('rehub_vimeo'); ?>" class="vim" rel="nofollow"><i class="fa fa-vimeo-square"></i></a>
		<?php endif;?>			
		
		<?php if ( rehub_option('rehub_pinterest') != '' ) :?>
			<a href="<?php echo rehub_option('rehub_pinterest'); ?>" class="pn" rel="nofollow"><i class="fa fa-pinterest"></i></a>
		<?php endif;?>

		<?php if ( rehub_option('rehub_linkedin') != '' ) :?>
			<a href="<?php echo rehub_option('rehub_linkedin'); ?>" class="in" rel="nofollow"><i class="fa fa-linkedin"></i></a>
		<?php endif;?>

		<?php if ( rehub_option('rehub_soundcloud') != '' ) :?>
			<a href="<?php echo rehub_option('rehub_soundcloud'); ?>" class="sc" rel="nofollow"><i class="fa fa-cloud"></i></a>
		<?php endif;?>

		<?php if ( rehub_option('rehub_dribbble') != '' ) :?>
			<a href="<?php echo rehub_option('rehub_dribbble'); ?>" class="db" rel="nofollow"><i class="fa fa-dribbble"></i></a>
		<?php endif;?>

		<?php if ( rehub_option('rehub_vk') != '' ) :?>
			<a href="<?php echo rehub_option('rehub_vk'); ?>" class="vk" rel="nofollow"><i class="fa fa-vk"></i></a>
		<?php endif;?>	

		<?php if ( rehub_option('rehub_rss') != '' ) :?>
			<a href="<?php echo rehub_option('rehub_rss'); ?>" class="rss" rel="nofollow"><i class="fa fa-rss"></i></a>
		<?php endif;?>																		

	</div>

<?php
}
}

?>