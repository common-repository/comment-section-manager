<?php

/*
Plugin Name: Comment Section Manager
Plugin URI: eyuva.com/comment-section-manager/
Description: This plugin will allow you to enable and disable comments from pages and category.
Author: Hiren Wadhiya
Version: 1.0
Author URI: https://en.gravatar.com/hirenwadhiya
 */

function csm_required_css_files() {
    wp_register_style( 'csm_comm_dec_man_script', plugin_dir_url( __FILE__ ) . 'css/style.css');
    wp_enqueue_style( 'csm_comm_dec_man_script');
}
add_action( 'admin_print_styles', 'csm_required_css_files' );

function csm_comment_remover_init(){
	register_setting('comment_remover_options','comment');
}
add_action('admin_init','csm_comment_remover_init');

function csm_comment_remover_option_page(){
	global $wpdb;
	?>	
		<h2>Comment Section Manager</h2>
		<hr/><br/>
		<table id="allpagestable">
			<tr>
				<th>
					Name
				</th>
				<th>
					Visibility
				</th>
				<th>
					Action
				</th>
			</tr>
			
			<tr>
			<form action="" method="POST" >
				<td>
					Comments In All Pages
				</td>
				<td>
					<select name="comment" id="comment">
						<option value="">Select Option</option>
						<option value="open" name="open">Allow</option>
						<option value="closed" name="closed">Disallow</option>
					</select>
				</td>
				<td>
					<?php submit_button(); ?>
				</td>
			</form>
			</tr>
		</table>
	<?php

		if ( $_POST['comment'] == 'closed' ) {
			$wpdb->query( $wpdb->prepare( 'UPDATE `wp_posts` SET `comment_status` = "closed" WHERE `post_type` = "page"','all_pages' ) );
		}else{
			$wpdb->query( $wpdb->prepare( 'UPDATE `wp_posts` SET `comment_status` = "open" WHERE `post_type` = "page"','all_pages' ) );
		}
}

function csm_remove_comment_pages(){
	?>
		<h2>Allow or Disallow Comments on Pages</h2>
		<hr><br/>
		<table id="page_table">
			<tr>
				<th>
					Sr. No.
				</th>
				<th>
					Page Name
				</th>
				<th>
					Visibility
				</th>
				<th>
					Action
				</th>
			</tr>
		
	<?php 	
			global $wpdb;
			$pages = get_pages(); 
			$j=0;

			foreach ($pages as $page) {
			$j++;
	?> 	
			<tr>
				<td>
					<?php echo $j; ?>
				</td>
		<form action="" method="POST">
			<div>
				<td>
					<?php 
						$page_name = $page->post_title; 
						$Id = $page->ID;
						echo $page_name;
					?>
				</td>

				<td>
					<select name="<?php echo $Id ; ?>" id= "<?php echo $Id; ?>">
						<option value="">Select Option</option>
						<option value="open" name="open">Allow</option>
						<option value="closed" name="closed">Deny</option>
					</select>
				</td>
					
				<td>
					<?php submit_button('save'); ?>
				</td>
					
			</div>
		</form>
			</tr>
	<?php
			}
	?>
		</table>
	<?php

			foreach ($pages as $page) {
				$submitted_page_data = $_POST[$page->ID];
				if($_POST[$page->ID]){
					$pname = $page->post_title;

					if ( $submitted_page_data == 'closed') {
						$wpdb->query( $wpdb->prepare( 'UPDATE `wp_posts` SET `comment_status` = "closed" WHERE `post_title` = "'.$pname.'" AND `post_type` = "page"','pages' ) );
					}else{
						$wpdb->query( $wpdb->prepare( 'UPDATE `wp_posts` SET `comment_status` = "open" WHERE `post_title` = "'.$pname.'" AND `post_type` = "page"','pages' ) );
					}
				}
			}		
	?> 
	<?php
}

function csm_comment_remover_categories(){
	?>
		<h2>Allow or Disallow Comments By Category</h2>
		<hr><br/>
		<table id="cat_table">
		<tr>
			<th>
				Sr. No.
			</th>
			<th>
				Category
			</th>
			<th>
				Visibility
			</th>
			<th>
				Action
			</th>
		</tr>
	<?php
		global $wpdb;	
		$categories = get_categories();
		$i=0;

		foreach ($categories as $category) {
			$i++;
	?>
		<tr>
			<td>
				<?php echo $i;?>
			</td>
		<form action="" method="POST">

			<td>
				<?php	
					$cat_id = $category->cat_ID;
					echo $category->name; 
				?>
			</td>
			<td>
				<select name="<?php echo $cat_id; ?>" id= "<?php echo $cat_id; ?>">
						<option value="">Select Options</option>
						<option value="open" name="open">Allow</option>
						<option value="closed" name="closed">Disallow</option>
				</select>
			</td>
			<td class="save_button">
				<?php submit_button('save'); ?>
			</td>
		</form>		
		</tr>
		
	<?php	
		}
	?>
		</table>
	<?php

		foreach ($categories as $category) {
			$submitted_category_data = $_POST[$category->cat_ID];
			if ($_POST[$category->cat_ID]) {
				$c_id = $category->cat_ID;
				$posts = get_posts(array('numberposts' => -1, 'category' => $c_id));
				
				foreach ($posts as $post) {
					$p_id = $post->ID;

					if ($submitted_category_data == 'closed') {
						$wpdb->query( $wpdb->prepare( 'UPDATE `wp_posts` SET `comment_status` = "closed" WHERE `ID` = "'.$p_id.'" AND `post_type` = "post"','category' ) );
					}else{
						$wpdb->query( $wpdb->prepare( 'UPDATE `wp_posts` SET `comment_status` = "open" WHERE `ID` = "'.$p_id.'" AND `post_type` = "post"','category' ) );
					}
				}	
			}
		}
}

function comment_remover_plugin_menu(){
	add_menu_page('Comment Section Manager Settings','Comment Section Manager','manage_options','comment_remover_plugin','csm_comment_remover_option_page');
	add_submenu_page('comment_remover_plugin','Comment Section Manager Settings','All Pages Setting','manage_options','comment_remover_plugin');
	add_submenu_page('comment_remover_plugin','Pages','Pages','manage_options','comment_remover_pages','csm_remove_comment_pages');
	add_submenu_page('comment_remover_plugin','Categories','Categories','manage_options','comment_remover_categories','csm_comment_remover_categories');
}
add_action('admin_menu','comment_remover_plugin_menu');