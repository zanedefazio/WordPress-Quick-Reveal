<?php
/*==========================================
Name: 		WordPress Quick Reveal Beta 0.2
Author: 	Zane DeFazio
Website: 	http://zanedefazio.com/
==========================================*/

// Load WordPress
require_once('wp-load.php');

// Count Posts
// Types: publish, future, draft, pending, private, trash, auto-draft, inherit
function countPosts($type) {
	$count_posts = wp_count_posts();
	if($count_posts->$type == NULL) {
		echo 0;
	}
	else {
		echo $count_posts->$type;
	}
}
 
// Count Pages
// Types: publish, future, draft, pending, private, trash, auto-draft, inherit
function countPages($type) {
	$count_posts = wp_count_posts('page');
	if($count_posts->$type == NULL) {
		echo 0;
	}
	else {
		echo $count_posts->$type;
	}
}

// Count Revisions
$revisions = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_type = 'revision';");

// Count Categories
$categories = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->terms;");


// Count Comments
// Types: moderated, trash, total_comments, approved, spam, post-trashed
function countComments($type) {
	$count_comments = wp_count_comments();
	if($count_comments->$type == NULL) {
		echo 0;
	}
	else {
		echo $count_comments->$type;
	}
}

// Total Users
function totalUsers() {
	$count_users = count_users();
	echo $count_users['total_users'];
}

// Total Roles
function totalRoles() {
	$count_roles = count_users();
	foreach($count_roles['avail_roles'] as $role => $count) {
		echo '<li><strong>'.ucfirst($role).'s:</strong> '.$count.'</li>';
	}
}

// Status | Checks if wp_option is enabled or disabled via wp get_option()
function status($option) {
	$enabled = get_option("$option");
	if($enabled == 1) {
		echo 'Enabled';
	}
	else {
		echo 'Disabled';
	}
}

// Active Plugins
function activePlugins() {
	$plugins = get_option('active_plugins');
	foreach($plugins as $plugin) {
		$plugin = explode("/",$plugin); 
		$plugin = $plugin['0'];
		echo '<li>'.$plugin.'</li>';
	}
}

// Recent Plugins
function recentPlugins() {
	$plugins = get_option('recently_activated');
	foreach($plugins as $plugin => $v) {
		$plugin = explode("/",$plugin); 
		$plugin = $plugin['0'];
		echo '<li>'.$plugin.'</li>';
	}
}

// Cron Jobs
function cronJobs() {
	$crons = get_option('cron');
	foreach($crons as $cron) {
		if(is_array($cron)) {
			foreach($cron as $name => $value) {
				echo '<div class="third"><h2>'.$name.'</h2><ul>';
				foreach($value as $key => $v) {
					foreach($v as $a => $b) {
						if($a != "args") {
							echo '<li><strong>'.$a.':</strong> '.$b.'</li>';
						}
					}
				echo '</ul></div>';
				}
			}
		}
	}
}

// MySQL Information
function mysqlInfo() {
	global $wpdb;
	$mysqlinfo = $wpdb->get_results('SHOW TABLE STATUS');
	foreach($mysqlinfo as $class => $table) {
		$size = number_format(($table->Data_length+$table->Index_length)/1024, 2);
		echo '<div class="third"><h2>'.$table->Name.':</h2> <ul><li><strong>Rows:</strong> '.$table->Rows.'</li><li><strong>Size:</strong> '.$size.' Kbs</li></ul></div>';
	}
}

// Additional Parsed PHP.ini
function addParsed() {
	$parsed = php_ini_scanned_files();
	if(empty($parsed)) {
		echo '(none)';
	}
	else {
		echo $parsed;
	}
}

?>
<html>
<head>
<title>WP-Quick-Reveal</title>
<link rel="stylesheet" href="wp-admin/css/install.css" type="text/css"/>
<style type="text/css">
.half {width:50%}
.third {width:33%}
.half,.third {float:left}
.clear {clear:both;}
</style>
</head>
<body>

<?php 

// Delete Self
unlink('wp-qr.php'); 

// Check if file exists
if (file_exists(wp-qr.php)) {
		echo '<h1 style="text-align:center; color:red;">WARNING WAS UNABLE TO DELETE SELF</h1>';
	}

?>

<h1 id="logo"> 
<a href="http://wordpress.org/"><img alt="WordPress" src="wp-admin/images/wordpress-logo.png" width="250" height="68"/></a> 
<br/> Quick Reveal Ver 0.2
</h1>

<h1>Install Details</h1>
	<div>
		<div class="third">
			<ul>
				<li><strong>Core Version:</strong> <?php echo $wp_version; ?></li>
				<li><strong>Database Version:</strong> <?php echo $wp_db_version; ?></li>
				<li><strong>Encoding:</strong> <?php echo get_settings('blog_charset'); ?></li>
				<li><strong>Gzip:</strong> <?php status('gzipcompression'); ?></li>
			</ul>
		</div>
		<div class="third">
			<ul>
				<li><strong>Can Compress Scripts:</strong> <?php echo status('can_compress_scripts'); ?></li>
				<li><strong>Home:</strong> <?php echo get_option('home'); ?></li>
				<li><strong>Site URL:</strong> <?php echo get_bloginfo('wpurl'); ?></li>
				<li><strong>Upload Path:</strong> <?php echo get_option('upload_path'); ?></li>
				
			</ul>
		</div>
		<div class="third">
			<ul>
				<li><strong>Theme:</strong> <?php echo get_option('template'); ?></li>
				<li><strong>Stylesheet:</strong> <?php echo get_option('stylesheet'); ?></li>
			</ul>
		</div>
		<div class="clear"></div>
	</div>

<h1>Server Setup</h1>
	<div>
		<div class="half">
			<ul>
				<li><strong>PHP Version:</strong> <?php echo phpversion(); ?></li>
				<li><strong>Server API:</strong> <?php echo php_sapi_name(); ?></li>
				<li><strong>PHP.ini Path:</strong> <?php echo get_include_path(); ?></li>
				<li><strong>Loaded PHP.ini:</strong> <?php echo php_ini_loaded_file(); ?></li>
				<li><strong>Additional PHP.ini:</strong> <?php echo addParsed(); ?></li>
				<li><strong>XCache:</strong> <?php $cache = ini_get('xcache.cacher'); if($cache == 1) { echo 'Enabled'; } else { echo 'Disabled'; } ?></li>
			</ul>
		</div>
		<div class="half">
			<ul>
				<li><strong>post_max_size:</strong> <?php echo ini_get('post_max_size'); ?></li>
				<li><strong>max_execution_time:</strong> <?php echo ini_get('max_execution_time'); ?></li>
				<li><strong>max_file_uploads:</strong> <?php echo ini_get('max_file_uploads'); ?></li>
				<li><strong>memory_limit:</strong> <?php echo ini_get('memory_limit'); ?></li>
				<li><strong>upload_max_filesize:</strong> <?php echo ini_get('upload_max_filesize'); ?></li>
				<li><strong>MySQL connect_timeout:</strong> <?php echo ini_get('mysql.connect_timeout'); ?></li>
			</ul>
		</div>
		<div class="clear"></div>
	</div>

<h1>MySQL Details</h1>
<div>
<?php mysqlInfo(); ?>
<div class="clear"></div>
</div>
<h1>Posts/Pages Details</h1>
	<div>
		<div class="third">
			<h2>Posts</h2>
			<ul>
				<li><strong>Published:</strong> <?php countPosts('publish'); ?></li>
				<li><strong>Future</strong>: <?php countPosts('future'); ?></li>
				<li><strong>Drafts:</strong> <?php countPosts('draft'); ?></li>
				<li><strong>Pending:</strong> <?php countPosts('pending'); ?></li>
				<li><strong>Private:</strong> <?php countPosts('private'); ?></li>
				<li><strong>Trash:</strong> <?php countPosts('trash'); ?></li>
				<li><strong>Auto Draft:</strong> <?php countPosts('auto-draft'); ?></li>
				<li><strong>Inherit:</strong> <?php countPosts('inherit'); ?></li>
			</ul>
		</div>
		<div class="third">
			<h2>Pages</h2>
			<ul>
				<li><strong>Published:</strong> <?php countPages('publish'); ?></li>
				<li><strong>Future</strong>: <?php countPages('future'); ?></li>
				<li><strong>Drafts:</strong> <?php countPages('draft'); ?></li>
				<li><strong>Pending:</strong> <?php countPages('pending'); ?></li>
				<li><strong>Private:</strong> <?php countPages('private'); ?></li>
				<li><strong>Trash:</strong> <?php countPages('trash'); ?></li>
				<li><strong>Auto Draft:</strong> <?php countPages('auto-draft'); ?></li>
				<li><strong>Inherit:</strong> <?php countPages('inherit'); ?></li>
			</ul>
		</div>
		<div class="third">
			<h2>Misc.</h2>
			<ul>
				<li><strong>Revisions:</strong> <?php echo $revisions; ?></li>
				<li><strong>Categories:</strong> <?php echo $categories; ?></li>
			</ul>
		</div>
		<div class="clear"></div>
	</div>

<h1>Comments Details</h1>
<ul>
	<li><strong>Total:</strong> <?php countComments('total_comments'); ?></li>
	<li><strong>Approved:</strong> <?php countComments('approved'); ?></li>
	<li><strong>Pending:</strong> <?php countComments('moderated'); ?></li>
	<li><strong>Spam:</strong> <?php countComments('spam'); ?></li>
	<li><strong>Trash:</strong> <?php countComments('trash'); ?></li>
</ul>

<h1>User Details</h1>
	<ul>
		<li><strong>Total:</strong> <?php totalUsers(); ?></li>
		<?php totalRoles(); ?>
	</ul>

<h1>Active Plugins</h1>
	<ul>
		<?php activePlugins(); ?>
	</ul>
	
<h1>Recent Plugins</h1>
	<ul>
		<?php recentPlugins(); ?>
	</ul>

<h1>Cron Jobs</h1>
	<div>
	<?php cronJobs(); ?>
	<div class="clear"></div>
	</div>
</body>
</html>