<?php

/*
Plugin Name: Slideshow Gallery
Plugin URI: http://wpgallery.tribulant.net
Author: Tribulant Software
Author URI: http://tribulant.com
Description: Feature content in a JavaScript powered slideshow gallery showcase on your WordPress website. The slideshow is flexible and all aspects can easily be configured. Embedding or hardcoding the slideshow gallery is a breeze. To embed into a post/page, simply insert <code>[tribulant_slideshow]</code> into its content with an optional <code>post_id</code> parameter. To hardcode into any PHP file of your WordPress theme, simply use <code>&lt;?php if (function_exists('slideshow')) { slideshow($output = true, $post_id = false, $gallery_id = false, $params = array()); } ?&gt;</code>.
Version: 1.4.8
License: GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Tags: slideshow gallery, slideshow, gallery, slider, jquery, bfithumb, galleries, photos, images
Text Domain: slideshow-gallery
Domain Path: /languages
*/

if (!defined('DS')) { define('DS', DIRECTORY_SEPARATOR); }

$path = dirname(__FILE__) . DS . 'slideshow-gallery-plugin.php';
if (file_exists($path)) {
	require_once($path);
}

if (!class_exists('Gallery')) {
	class Gallery extends GalleryPlugin {
		
		function Gallery() {		
			$url = explode("&", $_SERVER['REQUEST_URI']);
			$this -> url = $url[0];
			$this -> referer = (empty($_SERVER['HTTP_REFERER'])) ? $this -> url : $_SERVER['HTTP_REFERER'];
			$this -> plugin_name = basename(dirname(__FILE__));
			$this -> plugin_file = plugin_basename(__FILE__);
			$this -> register_plugin($this -> plugin_name, __FILE__);
			
			//WordPress action hooks
			$this -> add_action('wp_head');
			$this -> add_action('admin_menu');
			$this -> add_action('admin_head');
			$this -> add_action('admin_notices');
			$this -> add_action('wp_print_styles', 'print_styles');
			$this -> add_action('admin_print_styles', 'print_styles');
			$this -> add_action('wp_print_scripts', 'print_scripts');
			$this -> add_action('admin_print_scripts', 'print_scripts');
			$this -> add_action('init', 'init_textdomain', 10, 1);
			$this -> add_action('admin_init', 'custom_redirect', 1, 1);
			
			//WordPress Ajax hooks
			$this -> add_action('wp_ajax_slideshow_slides_order', 'ajax_slides_order', 10, 1);
			$this -> add_action('wp_ajax_slideshow_tinymce', 'ajax_tinymce', 10, 1);
			
			//WordPress filter hooks
			$this -> add_filter('mce_buttons');
			$this -> add_filter('mce_external_plugins');
			$this -> add_filter("plugin_action_links_" . $this -> plugin_file, 'plugin_settings_link', 10, 1);
			
			if (!is_admin()) { 
				add_shortcode('slideshow', array($this, 'embed')); 
				add_shortcode('tribulant_slideshow', array($this, 'embed'));
			}
			
			$this -> updating_plugin();
		}
		
		function plugin_settings_link($links) { 
			$settings_link = '<a href="' . admin_url('admin.php') . '?page=' . $this -> sections -> settings . '">' . __('Settings', $this -> plugin_name) . '</a>'; 
			array_unshift($links, $settings_link); 
			return $links; 
		}
		
		function init_textdomain() {		
			if (function_exists('load_plugin_textdomain')) {
				load_plugin_textdomain($this -> plugin_name, $this -> plugin_name . DS . 'languages', dirname(plugin_basename(__FILE__)) . DS . 'languages');
			}	
		}
		
		function wp_head() {
			
		}
		
		function admin_menu() {
			$this -> check_roles();
			add_menu_page(__('Slideshow', $this -> plugin_name), __('Slideshow', $this -> plugin_name), 'slideshow_slides', $this -> sections -> slides, array($this, 'admin_slides'), false);
			$this -> menus['slideshow-slides'] = add_submenu_page($this -> sections -> slides, __('Manage Slides', $this -> plugin_name), __('Manage Slides', $this -> plugin_name), 'slideshow_slides', $this -> sections -> slides, array($this, 'admin_slides'));
			$this -> menus['slideshow-galleries'] = add_submenu_page($this -> sections -> slides, __('Manage Galleries', $this -> plugin_name), __('Manage Galleries', $this -> plugin_name), 'slideshow_galleries', $this -> sections -> galleries, array($this, 'admin_galleries'));
			$this -> menus['slideshow-settings'] = add_submenu_page($this -> sections -> slides, __('Configuration', $this -> plugin_name), __('Configuration', $this -> plugin_name), 'slideshow_settings', $this -> sections -> settings, array($this, 'admin_settings'));
			
			add_action('admin_head-' . $this -> menus['slideshow-settings'], array($this, 'admin_head_gallery_settings'));
			
			add_dashboard_page(sprintf('Slideshow Gallery %s', $this -> version), sprintf('Slideshow Gallery %s', $this -> version), 'read', 'slideshow-gallery-about', array($this, 'slideshow_gallery_about'));
			remove_submenu_page('index.php', 'slideshow-gallery-about');
		}
		
		function slideshow_gallery_about() {
			$this -> render('about', false, true, 'admin');
		}
		
		function admin_head() {
			$this -> render('head', false, true, 'admin');
		}
		
		function admin_head_gallery_settings() {		
			add_meta_box('submitdiv', __('Save Settings', $this -> plugin_name), array($this -> Metabox, "settings_submit"), $this -> menus['slideshow-settings'], 'side', 'core');
			add_meta_box('aboutdiv', __('About This Plugin', $this -> plugin_name) . $this -> Html -> help(__('More about this plugin and the creators of it', $this -> plugin_name)), array($this -> Metabox, "settings_about"), $this -> menus['slideshow-settings'], 'side', 'core');
			add_meta_box('pluginsdiv', __('Recommended Plugin', $this -> plugin_name), array($this -> Metabox, "settings_plugins"), $this -> menus['slideshow-settings'], 'side', 'core');
			add_meta_box('generaldiv', __('General Settings', $this -> plugin_name) . $this -> Html -> help(__('General configuration settings for the inner workings and some default behaviours', $this -> plugin_name)), array($this -> Metabox, "settings_general"), $this -> menus['slideshow-settings'], 'normal', 'core');
			add_meta_box('linksimagesdiv', __('Links &amp; Images Overlay', $this -> plugin_name) . $this -> Html -> help(__('Configure the way that slides with links are opened', $this -> plugin_name)), array($this -> Metabox, "settings_linksimages"), $this -> menus['slideshow-settings'], 'normal', 'core');
			add_meta_box('stylesdiv', __('Appearance &amp; Styles', $this -> plugin_name) . $this -> Html -> help(__('Change the way the slideshows look so that it suits your needs', $this -> plugin_name)), array($this -> Metabox, "settings_styles"), $this -> menus['slideshow-settings'], 'normal', 'core');
			add_meta_box('wprelateddiv', __('WordPress Related', $this -> plugin_name) . $this -> Html -> help(__('Settings specifically related to WordPress', $this -> plugin_name)), array($this -> Metabox, "settings_wprelated"), $this -> menus['slideshow-settings'], 'normal', 'core');
			
			do_action('do_meta_boxes', $this -> menus['slideshow-settings'], 'normal');
			do_action('do_meta_boxes', $this -> menus['slideshow-settings'], 'side');
		}
		
		function admin_notices() {
			$this -> check_uploaddir();
		
			if (!empty($_GET[$this -> pre . 'message'])) {		
				$msg_type = (!empty($_GET[$this -> pre . 'updated'])) ? 'msg' : 'err';
				call_user_func(array($this, 'render_' . $msg_type), $_GET[$this -> pre . 'message']);
			}
		}
		
		function mce_buttons($buttons) {
			array_push($buttons, "separator", "gallery");
			return $buttons;
		}
		
		function mce_external_plugins($plugins) {
			$plugins['gallery'] = $this -> url() . '/js/tinymce/editor_plugin.js';
			return $plugins;
		}
		
		function slideshow($output = true, $post_id = null, $exclude = null) {		
			$params['post_id'] = $post_id;
			$params['exclude'] = $exclude;
		
			$content = $this -> embed($params, false);
			
			if ($output == true) {
				echo $content;
			} else {
				return $content;
			}
		}
		
		function embed($atts = array(), $content = null) {
			//global variables
			global $wpdb;
			$styles = $this -> get_option('styles');
			
			$autoheight = $this -> get_option('autoheight');
		
			// default shortcode parameters
			$defaults = array(
				'source'				=>	"slides",
				'products'				=>	false,
				'productsnumber'		=>	10,
				'featured'				=>	false,
				'featurednumber'		=>	10,
				'featuredtype'			=>	"post",
				'gallery_id'			=>	false,
				'orderby'				=>	array('order', "ASC"),
				'resizeimages'			=>	(($styles['resizeimages'] == "Y") ? "true" : "false"),
				'imagesoverlay'			=>	(($this -> get_option('imagesthickbox') == "Y") ? "true" : "false"),
				'layout'				=>	($styles['layout']),
				'width'					=>	($styles['width']),
				'height'				=>	($styles['height']),
				'autoheight'			=>	((!empty($autoheight)) ? "true" : "false"),
				'resheight'				=>	($styles['resheight']),
				'resheighttype'			=>	($styles['resheighttype']),
				'auto'					=>	(($this -> get_option('autoslide') == "Y") ? "true" : "false"),
				'autospeed'				=>	($this -> get_option('autospeed')),
				'alwaysauto'			=>	($this -> get_option('alwaysauto')),
				'fadespeed'				=>	($this -> get_option('fadespeed')),
				'shownav'				=>	(($this -> get_option('shownav') == "Y") ? "true" : "false"),
				'navopacity'			=>	($this -> get_option('navopacity')),
				'navhoveropacity'		=>	($this -> get_option('navhover')),
				'showinfo'				=>	(($this -> get_option('information') == "Y") ? "true" : "false"),
				'infospeed'				=>	($this -> get_option('infospeed')),
				'showthumbs'			=>	(($this -> get_option('thumbnails') == "Y") ? "true" : "false"),
				'thumbsposition'		=>	($this -> get_option('thumbposition')),
				'thumbsborder'			=>	($styles['thumbactive']),
				'thumbsspeed'			=>	($this -> get_option('thumbscrollspeed')),
				'thumbsspacing'			=>	($this -> get_option('thumbspacing')),
				'post_id' 				=> 	null, 
				'exclude' 				=> 	null, 
				'custom' 				=> 	null,
			);
					
			$s = shortcode_atts($defaults, $atts);
			extract($s);
			
			// if this is an RSS/Atom feed, it should not continue...
			if (is_feed()) { return false; }
			
			if (!empty($products)) {
				include_once(ABSPATH . 'wp-admin/includes/plugin.php');			
				if (is_plugin_active('wp-checkout' . DS . 'wp-checkout.php')) {
					$slides = array();
					
					if (class_exists('wpCheckout')) {
						if ($wpCheckout = new wpCheckout()) {
							global $wpcoDb, $Product;
							$wpcoDb -> model = $Product -> model;
							$productstype = $products;
						
							switch ($productstype) {
								case 'latest'		:
									$products = $wpcoDb -> find_all(false, false, array('created', "DESC"), $productsnumber);
									break;
								case 'featured'		:
									$products = $wpcoDb -> find_all(array('featured' => "1"), false, array('created', "DESC"), $productsnumber);
									break;
							}
						}
					}
					
					$content = $this -> render('gallery', array('slides' => $products, 'unique' => 'products' . $productstype . $productsnumber, 'products' => true, 'options' => $s, 'frompost' => false), false, 'default');
				} else {
					$error = sprintf(__('You need the %sShopping Cart plugin%s to display products slides.', $this -> plugin_name), '<a href="http://tribulant.com/plugins/view/10/wordpress-shopping-cart-plugin" target="_blank">', '</a>');
				}
				
				if (!empty($error)) {
					$content = '';
					$content .= '<p class="slideshow-gallery-error">';
					$content .= stripslashes($error);
					$content .= '</p>';
				}
			} elseif (!empty($featured)) {
				global $post;
			
				$args = array(
					'numberposts'				=>	$featurednumber,            	// should show 5 but only shows 3
					'post_type'					=>	'post',         				// posts only
					'meta_key'					=>	'_thumbnail_id', 				// with thumbnail
					'exclude'					=>	$post -> ID         			// exclude current post
				);
				
				if ($posts = get_posts($args)) {
					$content = $this -> render('gallery', array('slides' => $posts, 'unique' => 'featured' . $featuredtype . $featurednumber, 'featured' => true, 'options' => $s, 'frompost' => false), false, 'default');
				} else {
					$error = sprintf(__('No posts with featured images are available. Ensure your theme includes %s support.', $this -> plugin_name), '<code>add_theme_support("post-thumbnails");</code>');
				}
				
				if (!empty($error)) {
					$content = '';
					$content .= '<p class="slideshow-gallery-error">';
					$content .= stripslashes($error);
					$content .= '</p>';
				}
			} elseif (!empty($gallery_id)) {
				if (!is_array($orderby) || $orderby == "random") {
					$orderbystring = "ORDER BY RAND()";
				} else {
					list($orderf, $orderd) = $orderby;
					
					if ($orderf == "order") {
						$orderbystring = "ORDER BY " . $this -> GallerySlides -> table . ".order " . $orderd . "";
					} else {
						$orderbystring = "ORDER BY " . $this -> Slide -> table . "." . $orderf . " " . $orderd . "";
					}
				}
			
				$slidesquery = "SELECT * FROM " . $this -> Slide -> table . " LEFT JOIN " . $this -> GallerySlides -> table . 
				" ON " . $this -> Slide -> table . ".id = " . $this -> GallerySlides -> table . ".slide_id WHERE " . 
				$this -> GallerySlides -> table . ".gallery_id = '" . $gallery_id . "' " . $orderbystring;
				
				$query_hash = md5($slidesquery);
				if ($oc_slides = wp_cache_get($query_hash, 'slideshowgallery')) {
					$slides = $oc_slides;
				} else {
					$slides = $wpdb -> get_results($slidesquery);
					wp_cache_set($query_hash, $slides, 'slideshowgallery', 0);
				}
				
				if (!empty($slides)) {				
					$imagespath = $this -> get_option('imagespath');
				
					foreach ($slides as $skey => $slide) {
						if (empty($imagespath)) {
							$slides[$skey] -> image_path = $this -> Html -> uploads_path() . DS . 'slideshow-gallery' . DS . $slide -> image;
						} else {
							$slides[$skey] -> image_path = rtrim($imagespath, DS) . DS . $slide -> image;
						}
					}
				
					if ($orderby == "random") { shuffle($slides); }
					$content = $this -> render('gallery', array('slides' => $slides, 'unique' => 'gallery' . $gallery_id, 'options' => $s, 'frompost' => false), false, 'default');	
				}
			} elseif (!empty($custom) || empty($post_id)) {
				$slides = $this -> Slide -> find_all(null, null, $orderby);
				
				if (!empty($exclude)) {
					$exclude = array_map('trim', explode(',', $exclude));
					
					foreach ($slides as $slide_key => $slide) {
						if (in_array($slide -> id, $exclude)) {
							unset($slides[$slide_key]);
						}
					}
				}
				
				if ($orderby == "random") { shuffle($slides); }
				$content = $this -> render('gallery', array('slides' => $slides, 'unique' => "custom", 'options' => $s, 'frompost' => false), false, 'default');
			} else {
				global $post;
				$pid = (empty($post_id)) ? $post -> ID : $post_id;
			
				if (!empty($pid) && $post = get_post($pid)) {
					$children_attributes = array(
						'numberposts'					=>	false,
						'post_parent'					=>	$post -> ID,
						'post_type'						=>	"attachment",
						'post_status'					=>	"any",
						'post_mime_type'				=>	"image",
						'orderby'						=>	"menu_order",
						'order'							=>	"ASC",
					);
				
					if ($attachments = get_children($children_attributes)) {
					//if ($attachments = get_children("post_parent=" . $post -> ID . "&post_type=attachment&post_mime_type=image&orderby=" . ((!empty($orderby) && $orderby == "random") ? "rand" : "menu_order ASC, ID ASC"))) {
						if (!empty($exclude)) {
							$exclude = array_map('trim', explode(',', $exclude));
							
							$a = 0;
							foreach ($attachments as $id => $attachment) {
								
								$a++;
								if (in_array($a, $exclude)) {
									unset($attachments[$id]);
								}
							}
						}
					
						if ($orderby == "random") { shuffle($attachments); }
						$content = $this -> render('gallery', array('slides' => $attachments, 'unique' => $pid, 'options' => $s, 'frompost' => true), false, 'default');
					}
				}
			}
			
			return $content;
		}
		
		function admin_slides() {
			global $wpdb;
			
			switch ($_GET['method']) {
				case 'delete'			:
					if (!empty($_GET['id'])) {
						if ($this -> Slide -> delete($_GET['id'])) {
							$msg_type = 'message';
							$message = __('Slide has been removed', $this -> plugin_name);
						} else {
							$msg_type = 'error';
							$message = __('Slide cannot be removed', $this -> plugin_name);	
						}
					} else {
						$msg_type = 'error';
						$message = __('No slide was specified', $this -> plugin_name);
					}
					
					$this -> redirect($this -> referer, $msg_type, $message);
					break;
				case 'save'				:
					if (!empty($_POST)) {
						if ($this -> Slide -> save($_POST, true)) {
							$message = __('Slide has been saved', $this -> plugin_name);
							$this -> redirect($this -> url, "message", $message);
						} else {
							$this -> render('slides' . DS . 'save', false, true, 'admin');
						}
					} else {
						$this -> Db -> model = $this -> Slide -> model;
						$this -> Slide -> find(array('id' => $_GET['id']));
						$this -> render('slides' . DS . 'save', false, true, 'admin');
					}
					break;
				case 'mass'				:
					if (!empty($_POST['action'])) {
						if (!empty($_POST['Slide']['checklist'])) {						
							switch ($_POST['action']) {
								case 'delete'				:							
									foreach ($_POST['Slide']['checklist'] as $slide_id) {
										$this -> Slide -> delete($slide_id);
									}
									
									$message = __('Selected slides have been removed', $this -> plugin_name);
									$this -> redirect($this -> url, 'message', $message);
									break;
							}
						} else {
							$message = __('No slides were selected', $this -> plugin_name);
							$this -> redirect($this -> url, "error", $message);
						}
					} else {
						$message = __('No action was specified', $this -> plugin_name);
						$this -> redirect($this -> url, "error", $message);
					}
					break;
				case 'order'			:
					if (!empty($_GET['gallery_id'])) {
						$gallery = $this -> Gallery -> find(array('id' => $_GET['gallery_id']));
						
						$slides = array();
						$gsquery = "SELECT gs.slide_id FROM `" . $this -> GallerySlides -> table . "` gs WHERE `gallery_id` = '" . $gallery -> id . "' ORDER BY gs.order ASC";
						
						$query_hash = md5($gsquery);
						if ($oc_gs = wp_cache_get($query_hash, 'slideshowgallery')) {
							$gs = $oc_gs;
						} else {
							$gs = $wpdb -> get_results($gsquery);
							wp_cache_set($query_hash, $gs, 'slideshowgallery', 0);
						}
						
						if (!empty($gs)) {
							foreach ($gs as $galleryslide) {
								$slides[] = $this -> Slide -> find(array('id' => $galleryslide -> slide_id));
							}
						}
						
						$this -> render('slides' . DS . 'order', array('gallery' => $gallery, 'slides' => $slides), true, 'admin');	
					} else {
						$slides = $this -> Slide -> find_all(null, null, array('order', "ASC"));
						$this -> render('slides' . DS . 'order', array('slides' => $slides), true, 'admin');
					}
					break;
				default					:
					$orderfield = (empty($_GET['orderby'])) ? 'modified' : $_GET['orderby'];
					$orderdirection = (empty($_GET['order'])) ? 'DESC' : strtoupper($_GET['order']);
					$order = array($orderfield, $orderdirection);
					$data = $this -> paginate('Slide', false, false, false, false, 10, $order);				
					$this -> render('slides' . DS . 'index', array('slides' => $data[$this -> Slide -> model], 'paginate' => $data['Paginate']), true, 'admin');
					break;
			}
		}
		
		function admin_galleries() {
			
			switch ($_GET['method']) {
				case 'save'						:
					if (!empty($_POST)) {
						if ($this -> Gallery -> save($_POST, true)) {
							$message = __('Gallery has been saved', $this -> plugin_name);
							$this -> redirect($this -> url, "message", $message);
						} else {
							$this -> render('galleries' . DS . 'save', false, true, 'admin');
						}
					} else {
						$this -> Db -> model = $this -> Gallery -> model;
						$this -> Gallery -> find(array('id' => $_GET['id']));
						$this -> render('galleries' . DS . 'save', false, true, 'admin');
					}
					break;
				case 'view'						:
					$this -> Db -> model = $this -> Gallery -> model;
					$gallery = $this -> Gallery -> find(array('id' => $_GET['id']));
					$data = $this -> paginate('GallerySlides', "*", false, array('gallery_id' => $gallery -> id));
					
					$data['Slide'] = array();
					if (!empty($data[$this -> GallerySlides -> model])) {
						foreach ($data[$this -> GallerySlides -> model] as $galleryslide) {
							$this -> Db -> model = $this -> Slide -> model;
							$data['Slide'][] = $this -> Slide -> find(array('id' => $galleryslide -> slide_id));
						}
					}
					
					$this -> render('galleries' . DS . 'view', array('gallery' => $gallery, 'slides' => $data[$this -> Slide -> model], 'paginate' => $data['Paginate']), true, 'admin');
					break;
				case 'hardcode'			:
					$this -> Db -> model = $this -> Gallery -> model;
					$gallery = $this -> Gallery -> find(array('id' => $_GET['id']));					
					$this -> render('galleries' . DS . 'hardcode', array('gallery' => $gallery), true, 'admin');
					break;
				case 'delete'			:
					if (!empty($_GET['id'])) {
						if ($this -> Gallery -> delete($_GET['id'])) {
							$msg_type = 'message';
							$message = __('Gallery has been removed', $this -> plugin_name);
						} else {
							$msg_type = 'error';
							$message = __('Gallery cannot be removed', $this -> plugin_name);	
						}
					} else {
						$msg_type = 'error';
						$message = __('No gallery was specified', $this -> plugin_name);
					}
					
					$this -> redirect($this -> referer, $msg_type, $message);
					break;
				case 'mass'				:
					if (!empty($_POST['action'])) {
						if (!empty($_POST['Gallery']['checklist'])) {						
							switch ($_POST['action']) {
								case 'delete'				:							
									foreach ($_POST['Gallery']['checklist'] as $gallery_id) {
										$this -> Gallery -> delete($gallery_id);
									}
									
									$message = __('Selected galleries have been removed', $this -> plugin_name);
									$this -> redirect($this -> url, 'message', $message);
									break;
							}
						} else {
							$message = __('No slides were selected', $this -> plugin_name);
							$this -> redirect($this -> url, "error", $message);
						}
					} else {
						$message = __('No action was specified', $this -> plugin_name);
						$this -> redirect($this -> url, "error", $message);
					}
					break;
				default							:
					$orderfield = (empty($_GET['orderby'])) ? 'modified' : $_GET['orderby'];
					$orderdirection = (empty($_GET['order'])) ? 'DESC' : strtoupper($_GET['order']);
					$order = array($orderfield, $orderdirection);
					$data = $this -> paginate('Gallery', false, false, false, false, 10, $order);	
					$this -> render('galleries' . DS . 'index', array('galleries' => $data[$this -> Gallery -> model], 'paginate' => $data['Paginate']), true, 'admin');
					break;
			}
		}
		
		function admin_settings() {
			//$this -> initialize_options();
		
			switch ($_GET['method']) {
				case 'dismiss'			:
					if (!empty($_GET['dismiss'])) {
						$this -> update_option('dismiss_' . $_GET['dismiss'], 1);
					}
					
					$this -> redirect($this -> referer);
					break;
				case 'reset'			:
					global $wpdb;
					$query = "DELETE FROM `" . $wpdb -> prefix . "options` WHERE `option_name` LIKE '" . $this -> pre . "%';";
					
					if ($wpdb -> query($query)) {
						$this -> initialize_options();
					
						$message = __('All configuration settings have been reset to their defaults', $this -> plugin_name);
						$msg_type = 'message';
						$this -> render_msg($message);	
					} else {
						$message = __('Configuration settings could not be reset', $this -> plugin_name);
						$msg_type = 'error';
						$this -> render_err($message);
					}
					
					$this -> redirect($this -> url, $msg_type, $message);
					break;
				default					:
					if (!empty($_POST)) {
						delete_option('tridebugging');
						$this -> delete_option('infohideonmobile');
						$this -> delete_option('autoheight');
					
						foreach ($_POST as $pkey => $pval) {					
							switch ($pkey) {
								case 'debugging'			:
									if (!empty($pval)) {
										update_option('tridebugging', 1);
									}
									break;
								case 'permissions'			:
									global $wp_roles;
									$role_names = $wp_roles -> get_names();
								
									if (!empty($_POST['permissions'])) {
										$permissions = $_POST['permissions'];
										
										foreach ($role_names as $role_key => $role_name) {
											foreach ($this -> sections as $section_key => $section_name) {
												$wp_roles -> remove_cap($role_key, 'slideshow_' . $section_key);
												
												if (!empty($permissions[$role_key]) && in_array($section_key, $permissions[$role_key])) {
													$wp_roles -> add_cap($role_key, 'slideshow_' . $section_key);
												}
												
												if ($role_key == "administrator") {
													$wp_roles -> add_cap("administrator", 'slideshow_' . $section_key);
													$permissions[$role_key][] = $section_key;
												}
											}
										}
									}
									
									$this -> update_option('permissions', $permissions);
									break;
								default						:								
									$this -> update_option($pkey, $pval);
									break;
							}
						}
						
						$message = __('Configuration has been saved', $this -> plugin_name);
						$this -> render_msg($message);
					}	
					
					$this -> render('settings', false, true, 'admin');
					break;
			}
		}
		
		function update_plugin_complete_actions($upgrade_actions = null, $plugin = null) {
			$this_plugin = plugin_basename(__FILE__);
			
			if (!empty($plugin) && $plugin == $this_plugin) {
				$this -> add_option('activation_redirect', true);
			}
			
			return $upgrade_actions;
		}
		
		function activation_hook() {
			$this -> add_option('activation_redirect', true);
			wp_redirect(admin_url('index.php') . "?page=slideshow-gallery-about");
		}
		
		function custom_redirect() {
			$activation_redirect = $this -> get_option('activation_redirect');
			
			if (is_admin() && !empty($activation_redirect)) {
				$this -> delete_option('activation_redirect');
				wp_redirect(admin_url('index.php') . "?page=slideshow-gallery-about");
			}
		}
	}
}

//initialize a Gallery object
$Gallery = new Gallery();
register_activation_hook(plugin_basename(__FILE__), array($Gallery, 'initialize_options'));
register_activation_hook(plugin_basename(__FILE__), array($Gallery, 'activation_hook'));
add_filter('update_plugin_complete_actions', array($Gallery, 'update_plugin_complete_actions'), 10, 2);

if (!function_exists('slideshow')) {
	function slideshow($output = true, $gallery_id = null, $post_id = null, $params = array()) {
		$params['gallery_id'] = $gallery_id;
		$params['post_id'] = $post_id;
	
		$Gallery = new Gallery();
		$content = $Gallery -> embed($params, false);
		
		if ($output == true) {
			echo $content;
		} else {
			return $content;
		}
	}
}

?>