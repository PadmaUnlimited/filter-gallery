<?php

/*
Plugin Name:	Padma Filter Gallery
Plugin URI:		https://www.padmaunlimited/plugins/filter-gallery
Description:  	Filter Gallery For Wordpress. Based on Padma Filter Gallery 0.0.1 by A WP Life
Version:		0.0.3
Author: 		Padma Unlimited Team
Author URI: 	https://www.padmaunlimited.com/
License:      	GPL2
License URI:  	https://www.gnu.org/licenses/gpl-2.0.html
Text Domain:  	padma-filter-gallery
Domain Path:  	/languages


Padma Filter Gallery plugin is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
Padma Filter Gallery plugin is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with Padma Filter Gallery plugin. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
*/


defined('ABSPATH') or die( 'Access Forbidden!' );


if ( ! class_exists( 'Padma_Filter_Gallery' ) ) {

	class Padma_Filter_Gallery {		
		
		public function __construct() {
			$this->_constants();
			$this->_hooks();
		}		
		
		protected function _constants() {
			//Plugin Version
			define( 'PFG_PLUGIN_VER', '0.0.2' );
			
			//Plugin Text Domain
			define("PFG_TXTDM","padma-filter-gallery" );

			//Plugin Name
			define( 'PFG_PLUGIN_NAME', __( 'Padma Filter Gallery', PFG_TXTDM ) );

			//Plugin Slug
			define( 'PFG_PLUGIN_SLUG', 'padma_filter_gallery' );

			//Plugin Directory Path
			define( 'PFG_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

			//Plugin Directory URL
			define( 'PFG_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

			define( 'PFG_SECURE_KEY', md5( NONCE_KEY ) );
			
		} // end of constructor function
		
		protected function _hooks() {
			
			//Load text domain
			add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
			
			//add gallery menu item, change menu filter for multisite
			add_action( 'admin_menu', array( $this, 'pfg_menu' ), 101 );
			
			//Create Padma Filter Gallery Custom Post
			add_action( 'init', array( $this, 'Padma_Filter_Gallery' ));
			
			//Add meta box to custom post
			add_action( 'add_meta_boxes', array( $this, 'admin_add_meta_box' ) );
			 
			//loaded during admin init 
			add_action( 'admin_init', array( $this, 'admin_add_meta_box' ) );
			
			add_action('wp_ajax_pfg_gallery_js', array(&$this, '_ajax_pfg_gallery'));
		
			add_action('save_post', array(&$this, '_pfg_save_settings'));

			//Shortcode Compatibility in Text Widgets
			add_filter('widget_text', 'do_shortcode');

		} // end of hook function
		
		public function load_textdomain() {
			load_plugin_textdomain( PFG_TXTDM, false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
		}
		
		public function pfg_menu() {
			$filter_menu = add_submenu_page( 'edit.php?post_type='.PFG_PLUGIN_SLUG, __( 'Filters', PFG_TXTDM ), __( 'Filters', PFG_TXTDM ), 'administrator', 'pfg-filter-page', array( $this, 'padma_filter_page') );
			$doc_menu    = add_submenu_page( 'edit.php?post_type='.PFG_PLUGIN_SLUG, __( 'Docs', PFG_TXTDM ), __( 'Docs', PFG_TXTDM ), 'administrator', 'sr-doc-page', array( $this, 'pfg_doc_page') );			
		}
		
		public function Padma_Filter_Gallery() {
			$labels = array(
				'name'                => _x( 'Padma Filter Gallery', 'Post Type General Name', PFG_TXTDM ),
				'singular_name'       => _x( 'Padma Filter Gallery', 'Post Type Singular Name', PFG_TXTDM ),
				'menu_name'           => __( 'Portfolio Gallery', PFG_TXTDM ),
				'name_admin_bar'      => __( 'Portfolio Filter', PFG_TXTDM ),
				'parent_item_colon'   => __( 'Parent Item:', PFG_TXTDM ),
				'all_items'           => __( 'All Gallery', PFG_TXTDM ),
				'add_new_item'        => __( 'Add New Gallery', PFG_TXTDM ),
				'add_new'             => __( 'Add New Gallery', PFG_TXTDM ),
				'new_item'            => __( 'New Padma Filter Gallery', PFG_TXTDM ),
				'edit_item'           => __( 'Edit Padma Filter Gallery', PFG_TXTDM ),
				'update_item'         => __( 'Update Padma Filter Gallery', PFG_TXTDM ),
				'search_items'        => __( 'Search Padma Filter Gallery', PFG_TXTDM ),
				'not_found'           => __( 'Padma Filter Gallery Not found', PFG_TXTDM ),
				'not_found_in_trash'  => __( 'Padma Filter Gallery Not found in Trash', PFG_TXTDM ),
			);
			$args = array(
				'label'               => __( 'Padma Filter Gallery', PFG_TXTDM ),
				'description'         => __( 'Custom Post Type For Padma Filter Gallery', PFG_TXTDM ),
				'labels'              => $labels,
				'supports'            => array('title'),
				'taxonomies'          => array(),
				'hierarchical'        => false,
				'public'              => true,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'menu_position'       => 65,
				'menu_icon'           => 'dashicons-screenoptions',
				'show_in_admin_bar'   => true,
				'show_in_nav_menus'   => true,
				'can_export'          => true,
				'has_archive'         => true,		
				'exclude_from_search' => false,
				'publicly_queryable'  => true,
				'capability_type'     => 'page',
			);
			register_post_type( 'padma_filter_gallery', $args );
		} // end of post type function
		
		public function admin_add_meta_box() {
			add_meta_box( __('Add Padma Filter Gallery', PFG_TXTDM), __('Add Padma Filter Gallery', PFG_TXTDM), array(&$this, 'pfg_image_upload'), 'padma_filter_gallery', 'normal', 'default' );
		}
			
		public function pfg_image_upload($post) {		
			wp_enqueue_script('jquery');
			wp_enqueue_script('media-upload');
			wp_enqueue_script('pfg-uploader.js', PFG_PLUGIN_URL . 'js/pfg-uploader.js', array('jquery'));
			wp_enqueue_style('pfg-uploader-css', PFG_PLUGIN_URL . 'css/pfg-uploader.css');
			wp_enqueue_script( 'pfg-color-picker-js', plugins_url('js/pfg-color-picker.js', __FILE__ ), array( 'wp-color-picker' ), false, true );
			wp_enqueue_media();			
			wp_enqueue_style( 'wp-color-picker' );
			?>
			<div id="image-gallery">
				<p><strong><?php _e('First add filters for images by clicking filters menu.', PFG_TXTDM); ?></strong></p>
				<p><strong><?php _e('Please do not reapeat images. Use control ( Ctrl ) or shift ( Shift ) key for select multiple filters. For unselect filters use ( Ctrl ) key.', PFG_TXTDM); ?></strong></p>
				
				<input type="button" id="remove-all-images" name="remove-all-images" class="button button-large remove-all-images" rel="" value="<?php _e('Delete All Images', PFG_TXTDM); ?>">
				<ul id="remove-images" class="sbox">
					<?php
					$allimagesetting = unserialize(base64_decode(get_post_meta( $post->ID, 'padma_filter_gallery'.$post->ID, true)));
					
					$all_category = get_option('padma_portfolio_filter_gallery_categories');
					if(isset($allimagesetting['image-ids'])) {
						$filters = $allimagesetting['filters'];
						$count = 0;
						foreach($allimagesetting['image-ids'] as $id) {
						$thumbnail = wp_get_attachment_image_src($id, 'medium', true);
						$attachment = get_post( $id );
						$image_link = $allimagesetting['image-link'][$count];
						$image_type =  $allimagesetting['slide-type'][$id];
						?>
						<li class="image">
							<img class="new-image" src="<?php echo $thumbnail[0]; ?>" alt="<?php echo get_the_title($id); ?>" style="height: 150px; width: 100%; border-radius: 5px;">
							
							<select id="slide-type[<?php echo $id; ?>]" name="slide-type[<?php echo $id; ?>]" class="form-control" style="width: 100%;" placeholder="Image Title" value="<?php echo $image_type; ?>" >
								<option value="image" <?php if($image_type == "image") echo "selected=selected"; ?>> Image </option>
								<option value="video" <?php if($image_type == "video") echo "selected=selected"; ?>> Video </option>
							</select>
							
							<input type="hidden" id="image-ids[]" name="image-ids[]" value="<?php echo $id; ?>" />
							<input type="text" name="image-title[]" id="image-title[]" style="width: 100%;" placeholder="Image Title" value="<?php echo get_the_title($id); ?>">
							<input type="text" name="image-link[]" id="image-link[]" style="width: 100%;" placeholder="Image Link URL" value="<?php echo $image_link; ?>">
							<?php
							if(isset($filters[$id])) {
								$selected_filters_array = $filters[$id];
							} else {
								$selected_filters_array = array();
							}
							?>
							<select name="filters[<?php echo $id; ?>][]" multiple="multiple" id="filters" style="width: 100%;">
								<?php
								foreach ($all_category as $key => $value) {
									if($key != 0) {																					
									?><strong><option value="<?php echo $key; ?>" <?php if(count($selected_filters_array)) { if(in_array($key, $selected_filters_array)) echo "selected=selected"; } ?>><?php echo ucwords($value); ?></option></strong><?php
									}
								}							
								?>
							</select>
							<input type="button" name="remove-image" id="remove-image" class="button remove-single-image button-danger" style="width: 100%;" value="Delete">
						</li>
					<?php $count++; } // end of foreach
					} //end of if
					?>
				</ul>
			</div>
			<!--Add New Image Button-->
			<div name="add-new-images" id="add-new-images" class="new-images" style="height: 170px; width: 170px; border-radius: 8px;">
				<div class="menu-icon dashicons dashicons-camera"></div>
				<div class="add-text"><?php _e('Add Image', PFG_TXTDM); ?></div>
			</div>
			<div style="clear:left;"></div>
			<br>
			<br>
			
			<hr>
			<p class="input-text-wrap">
				<input type="text" name="shortcode" id="shortcode" value="<?php echo "[PFG id=".$post->ID."]"; ?>" readonly style="height: 60px; text-align: center; font-size: 24px; width: 25%; border: 2px dashed;" onmouseover="return pulseOff();" onmouseout="return pulseStart();">
				<p><?php _e('Copy & Embed shortcode into any Page/ Post / Text Widget to display your gallery on site.', PFG_TXTDM); ?><br></p>
			</p>
			<br>
			<br>
			<hr>
			<?php
			require_once('filter-gallery-settings.php');	
		}// end of upload multiple image
		
		public function _pfg_ajax_callback_function($id) {
			//wp_get_attachment_image_src ( int $attachment_id, string|array $size = 'thumbnail', bool $icon = false );
			//thumb, thumbnail, medium, large, post-thumbnail
			$thumbnail = wp_get_attachment_image_src($id, 'medium', true);
			$attachment = get_post( $id ); // $id = attachment id
			$all_category = get_option('padma_portfolio_filter_gallery_categories');
			?>
			<li class="image">
				<img class="new-image" src="<?php echo $thumbnail[0]; ?>" alt="<?php echo get_the_title($id); ?>" style="height: 150px; width: 100%; border-radius: 5px;">
				<input type="hidden" id="image-ids[]" name="image-ids[]" value="<?php echo $id; ?>" />
				
				<select id="slide-type[<?php echo $id; ?>]" name="slide-type[<?php echo $id; ?>]" class="form-control" style="width: 100%;" placeholder="Image Title" value="<?php echo $image_type; ?>" >
					<option value="image" <?php if($image_type == "image") echo "selected=selected"; ?>> Image </option>
					<option value="video" <?php if($image_type == "video") echo "selected=selected"; ?>> Video </option>
				</select>
				
				<input type="text" name="image-title[]" id="image-title[]" style="width: 100%;" placeholder="Image Title" value="<?php echo get_the_title($id); ?>">
				<input type="text" name="image-link[]" id="image-link[]" style="width: 100%;" placeholder="Image Link URL">
				<select name="filters[<?php echo $id; ?>][]" multiple id="filters" style="width: 100%;">
					<?php
					foreach ($all_category as $key => $value) {
						if($key != 0) {
							?><strong><option value="<?php echo $key; ?>"><?php echo ucfirst($value); ?></option></strong><?php
						}
					}							
					?>
				</select>
				<input type="button" name="remove-image" id="remove-image" style="width: 100%;" class="button" value="Delete">
			</li>
			<?php
		}
		
		public function _ajax_pfg_gallery() {
			echo $this->_pfg_ajax_callback_function($_POST['PFGimageId']);
			die;
		}
		
		public function _pfg_save_settings($post_id) {
			if(isset($_POST['pfg_save_nonce'])) {
				if (!isset( $_POST['pfg_save_nonce'] ) || ! wp_verify_nonce( $_POST['pfg_save_nonce'], 'pfg_save_settings' ) ) {
				   print 'Sorry, your nonce did not verify.';
				   exit;
				} else {
					
					$image_ids 		= $_POST['image-ids'];
					$image_titles 	= $_POST['image-title'];
					
					$i = 0;
					foreach($image_ids as $image_id) {
						$single_image_update = array(
							'ID'           => $image_id,
							'post_title'   => $image_titles[$i],						
						);
						wp_update_post( $single_image_update );
						$i++;
					}
					
					$padma_image_gallery_shortcode_setting = "padma_filter_gallery".$post_id;
					update_post_meta($post_id, $padma_image_gallery_shortcode_setting, base64_encode(serialize($_POST)));
				}
			}
		}// end save setting
		
		//filter/category page
		public function padma_filter_page() {
			require_once('filters.php');
		}
		
		//Doc page
		public function pfg_doc_page() {
			require_once('docs.php');
		}	
		
	}
	
	$pfg_portfolio_gallery_object = new Padma_Filter_Gallery();		
	require_once('filter-gallery-shortcode.php');
}


// Updates
if(is_admin()){
    add_action('after_setup_theme', 'padma_lifesaver_updates');
    function padma_lifesaver_updates(){
        if ( ! empty ( $GLOBALS[ 'PadmaUpdater' ] ) ){
            $GLOBALS[ 'PadmaUpdater' ]->updater('padma-lifesaver',__DIR__);
        }
    }
}