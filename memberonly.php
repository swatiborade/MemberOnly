<?php
/*
 Plugin Name: Member Only Content
 Plugin URI: https://github.com/NerdOfLinux/MemberOnly
 Description: A simple plugin to create member only content
 Version: 0.2
 Author: NerdOfLinux
 Author URI: https://gateblogs.com/user/nerdoflinux
 License: GPL
 */
/* Include the settings page */
include("memberonly-settings.php");
add_filter( 'the_content', 'post_filter' );
/* Create the function */
function post_filter( $content ) {
    /* Get variables or use defaults */
    $redirect = get_option("redirect");
    $login_link = get_option("loginURL", "/wp-login.php");
    $member_categories = get_option("categories", "member-only");
    $message = get_option("message", "Sorry, this post is for members only.  [sign_in]");
    $loginText = get_option("loginText", "Sign In/Register.");
    /* Create categories that are member only*/
    $categories = explode(",", $member_categories);
    /* If the post is in the category */
    if ( in_category( $categories ) ) {
        /* If the user is logged in, then show the content*/
        if ( is_user_logged_in() ) {
            return $content;
            /* Else tell the user to log in */
        } else {
            $content = "";
            if ( $redirect ){
                $link = get_the_permalink();
                $link = str_replace(':', '%3A', $link);
                $link = str_replace('/', '%2F', $link);
                $loginMessage = str_replace('[sign_in]', "<a href=\"$login_link?redirect_to=$link\">$loginText</a></p>", $message);
                $content = "<p>$loginMessage</p>";
            } else {
                $loginMessage = str_replace('[sign_in]', "<a href=\"$login_link\">$loginText</a></p>", $message);
                $content = "<p>$loginMessage</p>"; }
            return $content;
        }
    } else {
        return $content;
    }
}
?>
