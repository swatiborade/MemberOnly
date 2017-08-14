<?php
 /*
  Plugin Name: Member Only Content
  Plugin URI: https://gateblogs.com
  Description: A simple plugin to create member only content
  Version: 0.1
  Author: NerdOfLinux
  Author URI: https://gateblogs.com
  License: MIT
 */
    class Member_Only {
        /* Create blank array */
        public function __construct() {
            //$this = [];
            // Hook into the admin menu
            add_action( 'admin_menu', array( $this, 'settings_page' ) );
            add_action( 'admin_init', array( $this, 'setup_init' ) );
        }
        public function settings_page() {
            //Create the menu item and page
            $parent_slug = "member_only_fields";
            $page_title = "Member Only Content Settings Page";
            $menu_title = "Member Only Content";
            $capability = "manage_options";
            $slug = "member_only_fields";
            $callback = array( $this, 'settings_page_content' );
            add_submenu_page( "options-general.php", $page_title, $menu_title, $capability, $slug, $callback );
        }
        /* Create the page*/
        public function settings_page_content() { ?>
            <div class="wrap">
            <h2> Member Only Content </h2>
            <form method="post" action="options.php">
            <?php
                settings_fields("member_only_fields");
                do_settings_sections("member_only_fields");
                submit_button();
                ?>
            </form>
            <?php
        }
    /* Setup section_callback */
    public function section_callback( $arguments ) {
        /* Set up input*/
        switch( $arguments['id'] ){
            case "categories" :
                echo "Categories that will trigger the member only message.";
                break;
            case "loginURL":
                echo "The login URL of your site. ";
                break;
        }
    }
    public function setup_init() {
        register_setting("member_only_fields", "categories");
    
        add_settings_section("categories", "Member Only Categories: ", array($this, 'section_callback'), "member_only_fields");
        add_settings_field( 'categories', 'Categories: ', array( $this, 'field_callback' ), 'member_only_fields', 'categories', array( 'context' => 'categories') );
        
        register_setting("member_only_fields", "loginURL");
        add_settings_section("loginURL", "Login URL: ", array($this, 'section_callback'), "member_only_fields");
        add_settings_field( 'loginURL', 'Login URL: ', array( $this, 'field_callback' ), 'member_only_fields', 'loginURL',  array( 'context' => 'loginURL'));
    }
    /* Create input fields*/
    public function field_callback ( $args ) {
        if ( "categories" === $args[ 'context' ]){
                echo "<input name=\"categories\" id=\"categories\" type=\"text\" value=\"" .get_option("categories"). "\"\>";
        }else if ( "loginURL" === $args['context']){
            echo "<input name=\"loginURL\" id=\"loginURL\" type=\"text\" value=\"" .get_option("loginURL"). "\"\>"; }
    }
    }
new member_only();
add_filter( 'the_content', 'post_filter' );
/* Create the function */
function post_filter( $content ) {
    /* Get variables */
    $options = get_option("member_only_fields");
    /* Create categories that are member only*/
    $categories = array(
     'premium',
    );
    /* If the post is in the category */
    if ( in_category( $categories ) ) {
     /* If the user is logged in, then show the content*/
     if ( is_user_logged_in() ) {
         $test = $options['categories'];
         echo "test1";
         echo $test;
         echo "test2";
         return $content;
     /* Else tell the user to log in */
     } else {
         /* $link = get_the_permalink();
         $link = str_replace(':', '%3A', $link);
         $link = str_replace('/', '%2F', $link);
         $content = "<p>Sorry, this post is only available to members. <a href=\"gateblogs.com/login?redirect_to=$link\">Sign in/Register</a></p>"; */
         $content= "<p> Sorry, this post is member only. <p>";
         return $content;
     }
    } else {
      return $content;
    }
}
?>
