<?php get_header(); ?>
<!-- CONTENT -->

<div class="content">
  <div class="clearfix"> 
    <!-- Main Side -->
    <div class="main-side page clearfix">
      <?php if(class_exists('MetaDataFilterPage')) :?> <?php echo do_shortcode('[mdf_sort_panel]'); ?><div class="clearfix"></div><?php endif; ?>
      <?php  wp_enqueue_script('masonry'); wp_enqueue_script('imagesloaded'); wp_enqueue_script('masonry_init');?>
      <div class="masonry_grid_fullwidth two-col-gridhub">
        <?php  
global $more;
// set $more to 0 in order to only get the first part of the post
$more = 0; 
if(have_posts()): while(have_posts()): the_post(); ?>
        <?php include(locate_template('inc/parts/query_type3.php')); ?>
        <?php endwhile; endif; ?>
      </div>
      <div class="clearfix"></div>
      <div class="pagination">
        <?php rehub_pagination();?>
      </div>
    </div>
    <!-- /Main Side --> 
    <!-- Sidebar -->
    <?php get_sidebar(); ?>
    <!-- /Sidebar --> 
  </div>
</div>
<!-- /CONTENT --> 
<!-- FOOTER -->
<?php get_footer(); ?>
