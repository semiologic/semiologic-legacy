<?php
/**
 * sem_template
 *
 * @package Semiologic Theme
 **/

if ( !is_admin() ) {
	add_action('wp', array('sem_template', 'wp'), 0);
	add_action('template_redirect', array('sem_template' ,'template_redirect'), 0);
	add_action('wp_print_scripts', array('sem_template', 'scripts'));
	add_action('wp_print_styles', array('sem_template', 'styles'));
	add_action('wp_head', array('sem_template' ,'trackback_rdf'), 100);
	add_filter('body_class', array('sem_template', 'body_class'));
	add_filter('widget_title', array('sem_template', 'widget_title'));
	add_action('wp_footer', array('sem_template', 'display_credits'));
	remove_action('wp_print_styles', array('external_links', 'styles'), 5);
} else {
	add_action('admin_menu', array('sem_template', 'admin_menu'));
	add_action('admin_menu', array('sem_template', 'meta_boxes'));
}

class sem_template {
	/**
	 * admin_menu()
	 *
	 * @return void
	 **/

	function admin_menu() {
		add_theme_page(
			__('Header', 'sem-theme'),
			__('Header', 'sem-theme'),
			'switch_themes',
			'header',
			array('sem_header', 'edit_options')
			);
		add_theme_page(
			__('Skin', 'sem-theme'),
			__('Skin', 'sem-theme'),
			'switch_themes',
			'skin',
			array('sem_skin', 'edit_options')
			);
		add_theme_page(
			__('Manage Layout', 'sem-theme'),
			__('Layout', 'sem-theme'),
			'switch_themes',
			'layout',
			array('sem_layout', 'edit_options')
			);
	} # admin_menu()
	
	
	/**
	 * meta_boxes()
	 *
	 * @return void
	 **/

	function meta_boxes() {
		if ( current_user_can('switch_themes') ) {
			add_meta_box('sem_header', __('Post-Specific Header', 'sem-theme'), array('sem_header', 'edit_entry_header'), 'post');
			add_meta_box('sem_header', __('Page-Specific Header', 'sem-theme'), array('sem_header', 'edit_entry_header'), 'page');
		}
	} # meta_boxes()
	
	
	/**
	 * body_class()
	 *
	 * @param array $classes
	 * @return array $classes
	 **/

	function body_class($classes) {
		global $sem_options;
		
		$active_layout = apply_filters('active_layout', $sem_options['active_layout']);
		$active_width = apply_filters('active_width', $sem_options['active_width']);
		
		$classes[] = $active_layout;
		$classes[] = $active_width;
		$classes[] = $active_width . '_' . $active_layout;
		
		$classes[] = preg_replace("/[^a-z]+/", '_', $sem_options['active_skin']);
		
		if ( $sem_options['active_font'] )
			$classes[] = preg_replace("/[^a-z]+/", '_', $sem_options['active_font']);
		
		$classes[] = preg_replace("/[^a-z]+/", '_', $sem_options['active_font_size']);
		
		if ( is_page() ) {
			global $wp_the_query;
			
			$template = get_post_meta(intval($wp_the_query->get_queried_object_id()), '_wp_page_template', true);
			
			if ( $template != 'default' ) {
				$template = preg_replace("/\.[^\.]+$/", "", $template);

				$classes[] = $template;
			}
		}
		
		if ( header::get() )
			$classes['header_bg'];
		
		return $classes;
	} # body_class()
	
	
	/**
	 * scripts()
	 *
	 * @return void
	 **/

	function scripts() {
		global $wp_the_query;
		if ( is_singular() && comments_open($wp_the_query->get_queried_object_id()) ) {
			wp_enqueue_script('comment-reply');
			wp_enqueue_script('jquery');
		}
	} # scripts()
	
	
	/**
	 * styles()
	 *
	 * @return void
	 **/

	function styles() {
		global $sem_options;
		$skin_dir = sem_path . '/skins/' . $sem_options['active_skin'];
		$skin_url = sem_url . '/skins/' . $sem_options['active_skin'];
		
		wp_enqueue_style('style', sem_url . '/style.css', null, sem_version);
		wp_enqueue_style('layout', sem_url . '/css/layout.css', null, sem_version);
		wp_enqueue_style('font', sem_url . '/font.css', null, sem_version);
		
		if ( file_exists($skin_dir . '/icons.css') )
			wp_enqueue_style('icons', $skin_url . '/icons.css', null, sem_version);
		else
			wp_enqueue_style('icons', sem_url . '/icons/icons.css', null, sem_version);
		
		if ( isset($_GET['action']) && $_GET['action'] == 'print' ) {
			if ( file_exists($skin_dir . '/print.css') )
				wp_enqueue_style('skin', $skin_url . '/print.css', null, sem_version);
			else
				wp_enqueue_style('skin', sem_url . '/css/print.css', null, sem_version);
		} elseif ( apply_filters('active_width', $sem_options['active_width']) == 'letter' ) {
			if ( file_exists($skin_dir . '/letter.css') )
				wp_enqueue_style('skin', $skin_url . '/letter.css', null, sem_version);
			else
				wp_enqueue_style('skin', sem_url . '/css/letter.css', null, sem_version);
		} else {
			wp_enqueue_style('skin', $skin_url . '/skin.css', null, sem_version);
		}
		
		if ( file_exists(sem_path . '/custom.css') )
			wp_enqueue_style('custom', sem_url . '/custom.css', null, sem_version);
		if ( file_exists($skin_path . '/custom.css') )
			wp_enqueue_style('custom-skin', $skin_url . '/custom.css', null, sem_version);
	} # styles()
	
	
	/**
	 * strip_sidebars()
	 *
	 * @param string $layout
	 * @return string $layout
	 **/

	function strip_sidebars($layout) {
		return str_replace(array('s', 'e'), $layout);
	} # strip_sidebars()
	
	
	/**
	 * force_letter()
	 *
	 * @param string $width
	 * @return string $width
	 **/

	function force_letter($width) {
		return 'letter';
	} # force_letter()
	
	
	/**
	 * trackback_rdf()
	 *
	 * @return void
	 **/
	
	function trackback_rdf() {
		global $wp_the_query;
		if ( is_singular() && comments_open($wp_the_query->get_queried_object_id()) ) {
			echo '<!--' . "\n";
			trackback_rdf();
			echo "\n" . '-->' . "\n";
		}
	} # trackback_rdf()
	
	
	/**
	 * wp()
	 *
	 * @param object &$wp
	 * @return void
	 **/

	function wp(&$wp) {
		static $done = false;
		
		if ( $done )
			return;
		
		if ( is_attachment() ) {
			add_filter('option_blog_public', 'false');
			add_filter('comments_open', 'false');
			add_filter('pings_open', 'false');
		} elseif ( is_404() || is_search() ) {
			add_filter('option_blog_public', 'false');
		}
		
		if ( is_singular() ) {
			global $post;
			global $wp_the_query;
			$post = $wp_the_query->posts[0];
			setup_postdata($post);
		}
		
		$done = true;
	} # wp()
	
	
	/**
	 * template_redirect()
	 *
	 * @return void
	 **/
	
	function template_redirect() {
		if ( !isset($_GET['action']) || $_GET['action'] != 'print' )
			return;

		add_filter('option_blog_public', 'false');
		add_filter('comments_open', 'false');
		add_filter('pings_open', 'false');
		remove_action('wp_footer', array('sem_template', 'display_credits'));
		
		include_once sem_path . '/print.php';
		die;
	} # template_redirect()
	
	
	/**
	 * widget_title()
	 *
	 * @param string $title
	 * @return string $title
	 **/

	function widget_title($title) {
		return $title == '&nbsp;' ? '' : $title;
	} # widget_title()
	
	
	/**
	 * display_credits()
	 *
	 * @return void
	 **/

	function display_credits() {
		global $sem_options;
		
		echo '<div id="credits">' . "\n"
			. '<div id="credits_top"><div class="hidden"></div></div>' . "\n"
			. '<div id="credits_bg">' . "\n";
		
		if ( $sem_options['credits'] ) {
			$theme_credits = sem_template::get_theme_credits();
			$skin_credits = sem_template::get_skin_credits();
			
			$credits = sprintf($sem_options['credits'], $theme_credits, $skin_credits['skin_name'], $skin_credits['skin_author']);
			
			echo '<div class="pad">'
				. $credits
				. '</div>' . "\n";
		}
		
		echo '</div>' . "\n"
			. '<div id="credits_bottom"><div class="hidden"></div></div>' . "\n"
			. '</div><!-- credits -->' . "\n";
	} # display_credits()
	
	
	/**
	 * get_theme_credits()
	 *
	 * @return string $credits
	 **/

	function get_theme_credits() {
		if ( get_option('sem_api_key') ) {
			return '<a href="http://www.getsemiologic.com">'
				. __('Semiologic Pro', 'sem-theme')
				. '</a>';
		} else {
			$theme_descriptions = array(
				__('the <a href="http://www.semiologic.com/software/sem-theme/">Semiologic theme</a>', 'sem-theme'),
				__('an <a href="http://www.semiologic.com/software/sem-theme/">easy to use WordPress theme</a>', 'sem-theme'),
				__('an <a href="http://www.semiologic.com/software/sem-theme/">easy to customize WordPress theme</a>', 'sem-theme'),
				);
			
			$i = rand(0, sizeof($theme_descriptions) - 1);

			return $theme_descriptions[$i];
		}
	} # get_theme_credits()
	
	
	/**
	 * get_skin_credits()
	 *
	 * @return array $credits
	 **/

	function get_skin_credits() {
		global $sem_options;
		
		if ( !isset($sem_options['skin_data']) || !is_array($sem_options['skin_data']) ) {
			$details = sem_template::get_skin_data($sem_options['active_skin']);
			$sem_options['skin_data'] = $details;
			if ( !defined('sem_install_test') )
				update_option('sem5_options', $sem_options);
		} else {
			$details = $sem_options['skin_data'];
		}
		
		$name = $details['uri']
			? ( '<a href="' . esc_url($details['uri']) . '">'
				. $details['name']
				. '</a>' )
			: $details['name'];
		$author = $details['author_uri']
			? ( '<a href="' . esc_url($details['author_uri']) . '">'
				. $details['author_name']
				. '</a>' )
			: $details['author_name'];
		
		return array(
			'skin_name' => $name,
			'skin_author' => $author,
			);
	} # get_skin_credits()
	
	
	/**
	 * get_skin_data()
	 *
	 * @return array $data
	 **/

	function get_skin_data($skin_id) {
		$fields = array( 'name', 'uri', 'version', 'author_name', 'author_uri', 'description', 'tags' );
		
		$allowed_tags = array(
			'a' => array(
				'href' => array(),'title' => array()
				),
			'abbr' => array(
				'title' => array()
				),
			'acronym' => array(
				'title' => array()
				),
			'code' => array(),
			'em' => array(),
			'strong' => array()
		);

		$fp = @fopen(sem_path . '/skins/' . $skin_id . '/skin.css', 'r');
		
		if ( !$fp ) {
			foreach ( $fields as $field )
				$$field = '';
			$tags = array();
			return compact($fields);
		}

		$skin_data = fread( $fp, 4096 );
		
		fclose($fp);
		
		$skin_data = str_replace("\r", "\n", $skin_data);

		preg_match('/Skin(?:\s+name)?\s*:(.*)/i', $skin_data, $name);
		preg_match('/Skin\s+ur[il]\s*:(.*)/i', $skin_data, $uri);
		preg_match('/Version\s*:(.*)/i', $skin_data, $version);
		preg_match('/Author\s*:(.*)/i', $skin_data, $author_name);
		preg_match('/Author\s+ur[il]\s*:(.*)/i', $skin_data, $author_uri);
		preg_match('/Description\s*:(.*)/i', $skin_data, $description);
		preg_match('/Tags\s*:(.*)/i', $skin_data, $tags);
		
		foreach ( $fields as $field ) {
			if ( !empty( ${$field} ) )
				${$field} = _cleanup_header_comment(${$field}[1]);
			else
				${$field} = '';
			
			switch ( $field ) {
			case 'uri':
			case 'author_uri':
				$$field = esc_url_raw($$field);
				break;
			case 'tags':
				$$field = strip_tags($$field);
				if ( $$field ) {
					$$field = explode(',', $$field);
					$$field = array_map('trim', $$field);
				} else {
					$$field = array();
				}
			case 'description':
				$$field = wp_kses($$field, $allowed_tags);
				break;
			default:
				$$field = strip_tags($$field);
				break;
			}
		}
		
		return compact($fields);
	} # get_skin_data()
} # sem_template
?>