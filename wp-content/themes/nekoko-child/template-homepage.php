<?php
/**
 * Template Name: Homepage
 */
defined( 'ABSPATH' ) || exit;
get_header();
?><main class="nekoko-homepage"><?php
while ( have_posts() ) {
	the_post();
	the_content();
}
?></main><?php
get_footer();
