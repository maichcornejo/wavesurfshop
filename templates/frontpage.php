<?php 
/* Template Name: Home Page Template */
get_header(); 
?>

<div id="home-wrapper">

    <?php get_template_part('components/brands'); ?>

    <?php get_template_part('components/gender-grid'); ?>

    <?php get_template_part('components/wc-latest'); ?>

    <?php get_template_part('components/wc-featured'); ?>

    <?php get_template_part('components/snap-scroll'); ?>

</div>

<?php get_footer(); ?>
