<?php
/*
Plugin Name: WP Database Optimizer
Plugin URI: http://www.matthewaprice.com/wp-database-optimizer/
Description: Ever wished that you could take some measure to speed up your Wordpress site? Have you created and deleted a lot of posts as well as experimented with plugins? All these deleted entries leave overhead on the database that should be routinely cleared out. This plugin allows you to have your Wordpress tables to be optimized on a schedule. It uses the Wordpress Database Class to do the work, and you can set the number of days in between optimizations.
Version: 1.2.1.3
Author: Matthew Price
Author URI: http://www.matthewaprice.com
License: GPL2
*/

if($_GET['action']); {

	switch ($_GET['action']) {

	case 'update-frequency': wp_database_optimizer_update_frequency(); break;
	case 'optimize-now': wp_database_optimizer_now(); break;

	}
}

function wp_database_optimizer_input_todays_date() {
global $wpdb;
	$tables = $wpdb->get_results("show tables like '%$wpdb->prefix%'");
	$count = count($tables);
	$i = 1;
	foreach ($tables as $table) {
		foreach ($table as $t) {
			if ($count == $i) {
			$optimizer .= $t;
			} else {
			$optimizer .= $t . ", ";
			}
		}
	$i++;
	}
	$optimize = $wpdb->get_row("OPTIMIZE TABLE ".$optimizer." ");

	$option_name = "wp_database_optimizer_last_date";
	$value = date('Y/m/d');
    $deprecated=' ';
    $autoload='no';
    add_option($option_name, $value, $deprecated, $autoload);
}

function wp_database_optimizer_input_default_frequency() {
	$option_name = "wp_database_optimizer_freq";
	$value = "7"; // Start with 7 Days
    $deprecated=' ';
    $autoload='no';
    add_option($option_name, $value, $deprecated, $autoload);
}

register_activation_hook( __FILE__, 'wp_database_optimizer_input_todays_date' );
register_activation_hook( __FILE__, 'wp_database_optimizer_input_default_frequency' );

function wp_database_optimizer_deactivate_plugin () {

delete_option('wp_database_optimizer_last_date');
delete_option('wp_database_optimizer_freq');

}

register_deactivation_hook( __FILE__, 'wp_database_optimizer_deactivate_plugin' );


// THIS FUNCTION GENERATES THE SETTINGS PAGE UNDER THE "TOOLS" MENU
function wp_database_optimizer_settings_page() {
global $wpdb;

	if ( isset( $_POST['submit'] ) && check_admin_referer('wp-database-optimizer-frequency') ) {
		update_option( 'wp_database_optimizer_freq', $_POST['wp_database_optimizer_freq'] );
		$updated = true;
	}
?>

	<div class="wrap">
		<?php if ( isset($updated) ) : ?><?php echo "<div id='message' class='updated fade'><p>" . __( 'WP Database Optimizer Frequency Updated.' ) . "</p></div>" ?><?php endif; ?>
		<?php screen_icon(); ?>
		<h2>WP Database Optimizer Options</h2>
		<br>
		<form method="post" name="wp-database-optimizer-last-date" action="">
		How Often Do You Want the Optimizer to run? (in days)<br><br>
			<?php $optimize_freq = get_option('wp_database_optimizer_freq'); ?>
			Currently Set at <strong><?php echo $optimize_freq; ?></strong>
			<?php if ($optimize_freq > 1) { echo "days"; } else { echo "day"; } ?>
			<br><br>
			<input name="wp_database_optimizer_freq" value="">
			<input type="submit" name="submit" value="Update Frequency">
			<?php wp_nonce_field( 'wp-database-optimizer-frequency' ); ?>
		</form>

		<br>
		Database Optimization Last Occurred on: <?php echo get_option('wp_database_optimizer_last_date'); ?>
		<br>
		<form method="post" name="wp-database-optimizer-now" action="?action=optimize-now">
		<br>
		Looking to optimize now?
			<input type="submit" name="submit" value="Optimize Now">
		</form>
		<br>
		<?php
		$tables = $wpdb->get_results("show tables like '%$wpdb->prefix%'");
		echo '<table class="widefat" cellspacing="0">';
		echo '<thead>';
		echo '<tr class="thead">';
		echo '<th scope="col" class="manage-column">Table Name</th>';
		echo '<th scope="col" class="manage-column">Overhead</th>';
		echo '</tr>';
		echo '</thead>';
		echo '<tbody>';
		foreach ($tables as $table) {
			foreach ($table as $t) {
				$overhead = $wpdb->get_results("SHOW TABLE STATUS LIKE '%$t%'");
				foreach ($overhead as $oh) {
				echo '<tr>';
					echo "<td>" . $oh->Name . "</td>";
					if ($oh->Data_free == '0') {
					echo "<td>okay!</td>";
					} else {
					echo "<td style=\"color: #FF0000;\">" . number_format($oh->Data_free, 0)  . " B</td>";
					}
				echo '</tr>';
				}
			}
		}
		echo '</tbody>';
		echo '</table>';
		?>
		<br><br>
		Make sure that you backup your database as necessary.  i recommend	<a href="http://wordpress.org/extend/plugins/wp-db-backup/" target="_blank">WordPress Database Backup</a>
	</div>

<?php

}

function wp_database_optimizer_now() {
global $wpdb;

	$tables = $wpdb->get_results("show tables like '%$wpdb->prefix%'");
	$count = count($tables);
	$i = 1;
	foreach ($tables as $table) {
		foreach ($table as $t) {
			if ($count == $i) {
			$optimizer .= $t;
			} else {
			$optimizer .= $t . ", ";
			}
		}
	$i++;
	}
	$optimize = $wpdb->get_row("OPTIMIZE TABLE ".$optimizer." ");

	$option_name = "wp_database_optimizer_last_date";
	$value = date('Y/m/d');
    update_option($option_name, $value);
	header("Location: " . $_SERVER['PHP_SELF'] . "?page=wp-database-optimizer");

}

function wp_database_optimizer_check() {
global $wpdb;
$wp_database_optimizer_last_date = get_option('wp_database_optimizer_last_date');
$wp_database_optimizer_freq = get_option('wp_database_optimizer_freq');
$curr_date = strtotime(date('Y/m/d'));

$freq_future_date = strtotime('+'.$wp_database_optimizer_freq.' day', strtotime($wp_database_optimizer_last_date));

if ($curr_date > $freq_future_date) {

	$tables = $wpdb->get_results("show tables like '%$wpdb->prefix%'");
	$count = count($tables);
	$i = 1;
	foreach ($tables as $table) {
		foreach ($table as $t) {
			if ($count == $i) {
			$optimizer .= $t;
			} else {
			$optimizer .= $t . ", ";
			}
		}
	$i++;
	}
	$optimize = $wpdb->get_row("OPTIMIZE TABLE ".$optimizer." ");

	$option_name = "wp_database_optimizer_last_date";
	$value = date('Y/m/d');
    update_option($option_name, $value);

} else {

echo ""; // Do nothing as not enough time has passed
}

}

function wp_database_optimizer_add_settings_page_to_menu() {
add_management_page('WP Database Optimizer', 'WP Database Optimizer', 'manage_options', 'wp-database-optimizer', 'wp_database_optimizer_settings_page');
}

add_action('admin_menu', 'wp_database_optimizer_add_settings_page_to_menu');
add_action( 'wp_head', 'wp_database_optimizer_check' );
?>