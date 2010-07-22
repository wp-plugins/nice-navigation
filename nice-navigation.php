<?php
/*
Plugin Name: Nice Navigation
Plugin URI: http://eskapism.se/code-playground/nice-navigation/
Description: Adds a widget that makes your page list expandable/collapsible with a nice slide animation effect
Author: Pär Thernström
Version: 1.1
Author URI: http://eskapism.se/
*/

class Nice_Navigation extends WP_Widget {

	var $arr_types = array(
		"wp_list_pages" => array("function" => "wp_list_pages", "name" => "List pages" ),
		"wp_page_menu" => array("function" => "wp_page_menu", "name" => "List pages as menu"),
		"wp_list_categories" => array("function" => "wp_list_categories", "name" => "List categories"),
		"wp_get_archives" => array("function" => "wp_get_archives", "name" => "List archives")
	);
	
	var $arr_looks = array(
		"wikipedia" => array("look" => "wikipedia", "name" => "Wikipedia-like"),
		#"explorer" => array("look" => "explorer", "name" => "Windows Explorer"),
		#"finder" => array("look" => "finder", "name" => "OSX Finder")
	);

	function Nice_Navigation() {
		parent::WP_Widget('nice_navigation', 'Nice Navigation', array('description' => 'Outputs pages/categories with a very nice effect.', 'class' => 'nice-navigation-class'));	
	}

	function form($instance) {
		#print_r($instance);
		// outputs the options form on admin

		$field_id = $this->get_field_id('title');
		$field_name = $this->get_field_name('title');
		$title_value = esc_html($instance["title"]);
		echo "<p>";
		echo "<label for='$field_id'>Title</label>";
		echo "<input class='widefat' type='text' value='$title_value' name='$field_name' id='$field_id' />";
		echo "</p>";

		$field_id = $this->get_field_id('function');
		$field_name = $this->get_field_name('function');
		echo "<p>";
		echo "<label for='$field_id'>Function</label>";
		echo "<select id='$field_id' name='$field_name' class='widefat'>";
		foreach ($this->arr_types as $type) {
			$selected = "";
			if ($instance["function"] == $type["function"]) {
				$selected = ' selected="selected" ';
			}
			echo "<option $selected value='{$type["function"]}'>{$type["name"]}</option>";
		}
		echo "</select>";
		echo "</p>";
		
		$field_id = $this->get_field_id('arguments');
		$field_name = $this->get_field_name('arguments');
		$arguments_value = esc_html($instance["arguments"]);
		echo "<p>";
		echo "<label for='$field_id'>Arguments</label>";
		echo "<input class='widefat' type='text' value='$arguments_value' name='$field_name' id='$field_id' />";
		echo "</p>";

		$field_id = $this->get_field_id('look');
		$field_name = $this->get_field_name('look');
		echo "<p>";
		echo "<label for='$field_id'>Look</label>";
		echo "<select id='$field_id' name='$field_name' class='widefat'>";
		foreach ($this->arr_looks as $look) {
			$selected = "";
			if ($instance["look"] == $look["look"]) {
				$selected = ' selected="selected" ';
			}
			echo "<option $selected value='{$look["look"]}'>{$look["name"]}</option>";
		}
		echo "</select>";
		echo "</p>";
		
	}

	function update($new_instance, $old_instance) {
		#var_dump($new_instance);
		#var_dump($old_instance);
		// processes widget options to be saved
		// fill current state with old data to be sure we not loose anything
		$instance = $old_instance;
		// for example we want title always have capitalized first letter
		#$instance['title'] = strip_tags($new_instance['title']);
		// and now we return new values and wordpress do all work for you
		
		$instance["title"] = $new_instance["title"];
		$instance["function"] = $new_instance["function"];
		$instance["arguments"] = $new_instance["arguments"];
		$instance["look"] = $new_instance["look"];
		
		return $instance;
	}

	function widget($args, $instance) {
		// outputs the content of the widget
		#echo "yeaha"; 
		#echo "<pre>";print_r($args);echo "</pre>";
		#echo "<pre>";print_r($instance);echo "</pre>";
		/*
		$instance
		Array
		(
		    [function] => wp_list_pages
		    [arguments] => title=test
		)
		*/
		
		echo $args["before_widget"];
		echo $args["before_title"];
		echo $instance["title"];
		echo $args["after_title"];
		
		$function = $instance["function"];
		$arguments = $instance["arguments"];
		$arguments .= "&title_li=0";
		
		$look = $instance["look"];
		if (function_exists($function)) {
			echo "<div class='nice_navigation nice_navigation_look_$look'>";
			#echo "<br>function: $function";
			#echo "<br>arguments: $arguments";
			// wp_list_pages
			if ($function == "wp_list_pages") {
				echo "<ul>";
			}

			call_user_func($function, $arguments);

			if ($function == "wp_list_pages") {
				echo "</ul>";
			}
			echo "</div>";
		} else {
			echo "<p>Could not find function '$function'.";
		}
		
		echo $args["after_widget"];
		
	}

}

add_action('widgets_init', create_function('', 'return register_widget("Nice_Navigation");'));

add_action("init", "nice_navigation_init");
function nice_navigation_init() {

	define( "NICE_NAVIGATION_URL", WP_PLUGIN_URL . '/nice-navigation/');
	define( "NICE_NAVIGATION_VERSION", "0.2");

	wp_enqueue_script( "nice-navigation", NICE_NAVIGATION_URL . "script.js", array("jquery"), NICE_NAVIGATION_VERSION );
	wp_enqueue_style( "nice-navigation", NICE_NAVIGATION_URL . "styles.css", array(), NICE_NAVIGATION_VERSION);

	add_filter('page_css_class', 'nice_navigation_page_css_class', 10, 2);

}

/**
 * adds class "page-has-children" to all pages that have children
 * @param array $class.	The page css class being modified, passed as an array from Walker_Page
 * @param object $page.	The page object passed from Walker_Page
 * @return array			Returns the new page css class.
 */
function nice_navigation_page_css_class($class, $page) {

	// check if current page has children
	$children = get_pages('child_of='.$page->ID);
	if (sizeof($children) > 0) {
		$class[] = "page-has-children";
	}
 
	return $class;
 
}


add_action("wp_head", "nice_navigation_wp_head");
function nice_navigation_wp_head() {
	/*
	nice_navigation nice_navigation_look_wikipedia
	*/
	?>
	<?php
}


?>