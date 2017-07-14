<?php
/*
Plugin Name: Cryout ThemeSwitch
Plugin URI: http://www.cryoutcreations.eu/
Description: Adds a quick theme switcher to the WordPress Admin Bar with all parent/child themes. Based on Matty's Theme QuickSwitch plugin, but updated for Wordpress 4.0+ with extra features.
Author: Cryout Creations
Author URI: http://www.cryoutcreations.eu/
Version: 0.5.1
License: GPL v3 - http://www.gnu.org/licenses/gpl-3.0.html
*/

class Cryout_ThemeSwitch {

	private $_version = '0.5.1';

	function __construct(){
		add_action( 'admin_bar_menu', array($this, 'admin_menu'), 90 );
		add_action( 'admin_enqueue_scripts', array($this, 'styling'), 10 );
		add_action( 'admin_enqueue_scripts', array($this, 'scripting'), 10 );
		add_action( 'wp_enqueue_scripts', array($this, 'styling'), 10 );
		add_action( 'wp_enqueue_scripts', array($this, 'scripting'), 10 );
	} // __construct()

	/**
	 * Add the theme switcher menu to the WordPress Toolbar.
	 */
	function admin_menu () {
		global $wp_admin_bar;

		if ( ! current_user_can( 'switch_themes' ) ) { return; }

		$child_themes = array();
		$parent_themes = array();

		$themes = wp_get_themes();

		if ( ! isset( $themes  ) || ! is_array( $themes ) ) { return; }

		$current_theme = wp_get_theme();

		$menu_label = $current_theme->display( 'Name' );

		$menu_label_ex = '<span class="ab-icon dashicons-admin-appearance"></span><span class="ab-label">' .
						__( 'Theme: ','cryout-themeswitch' ) . '<strong>' . $menu_label . '</strong></span>';

		$count = 0;
		$has_child_themes = false;
		$end_child_themes = false;

		$menu_id = 'cryout-themeswitch';

		foreach ( $themes as $k => $v ) {
			if ( $v['Template'] != $v['Stylesheet'] ) {
				$child_themes[] = $v;
			} else {
				$parent_themes[] = $v;
			}
		}



		// Main Menu Item
		$wp_admin_bar->add_node( array(
			'id'    => $menu_id,
			'title' => $menu_label_ex,
			'href'  => admin_url('themes.php'),
		));

		// Parent themes placeholder
		$wp_admin_bar->add_node( array(
			'id'    => 'heading-parent-themes',
			'parent'    => $menu_id,
			'title' => __( 'Parent Themes', 'cryout-themeswitch' ),
			'href'  => '',
			'meta'  => array( 'tabindex' => 0 ),
		));

		if ( count( $child_themes ) > 0 ) {
			$has_child_themes = true;
		}

		$themes = array_merge( $child_themes, $parent_themes );

		if ( $has_child_themes ) {
			// child themes placeholder
			$wp_admin_bar->add_node( array(
				'id'    => 'heading-child-themes',
				'parent'    => $menu_id,
				'title' => __( 'Child Themes', 'cryout-themeswitch' ),
				'href'  => '',
				'meta'  => array( 'tabindex' => 0 ),
			));
		}

		// themes list
		$themes_array = array();

		foreach ( $themes as $k => $v ) {
			$count++;

			$template = $v->get_template();
			$stylesheet = $v->get_stylesheet();
			$screenshot = $v->get_screenshot();

			$id = urlencode( str_replace( '/', '-', strtolower( $stylesheet ) ) );
			$activate_link = admin_url( wp_nonce_url( "themes.php?action=activate&amp;template=" . urlencode( $template ) . "&amp;stylesheet=" . urlencode( $stylesheet ), 'switch-theme_' . $stylesheet) );

			$name = $v['Name'];
			//$name .= '<img class="themeswitch-screenshot" src="' . $screenshot . '" />';
			if ( $name == $menu_label ) { $name = '<strong>' . $name . '</strong>'; }

			if ($template == $stylesheet): // parent theme
				$themes_array[] = array( 'id' => $id, 'name' => $name, 'link' => $activate_link, 'type' => 'parent' );
			else: 						   // child theme
				$themes_array[] = array( 'id' => $id, 'name' => $name, 'link' => $activate_link, 'type' => 'child' );
			endif;

		}

		// add themes to the menus
		foreach ($themes_array as $thm) {
			$wp_admin_bar->add_node( array(
				'id'    => $thm['id'],
				'parent'  => ($thm['type']=='parent'?'heading-parent-themes':'heading-child-themes'),
				'title' => $thm['name'],
				'href'  => $thm['link'],
			));
			$wp_admin_bar->add_node( array(
				'id'    => 'list_'.$thm['id'],
				'parent'  => $menu_id,
				'title' => $thm['name'],
				'href'  => $thm['link'],
				'meta'  => array('class'=>'the_list hide-theme'),
			));
		}
	} // admin_menu()


	/**
	 * Load CSS for the plugin.
	 */
	function styling() {
		if ( ! current_user_can( 'switch_themes' ) ) { return; }

		$plugin_url = trailingslashit( plugin_dir_url( __FILE__ ) );

		wp_register_style( 'cryout-themeswitch', $plugin_url . 'resources/style.css', 'screen', $this->_version );
		wp_enqueue_style( 'cryout-themeswitch' );
	} // styling()

	/**
	 * Load JavaScript for the plugin.
	 */
	function scripting() {
		if ( ! current_user_can( 'switch_themes' ) ) { return; }

		$plugin_url = trailingslashit( plugin_dir_url( __FILE__ ) );

		wp_register_script( 'cryout-themeswitch', $plugin_url . 'resources/scripting.js', array( 'jquery' ), $this->_version );
		wp_enqueue_script( 'cryout-themeswitch' );
	} // scripting()

} // class Cryout_ThemeSwitch

new Cryout_ThemeSwitch;

// FIN
