<?php
/**
 * The sidebar containing the main widget area
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Shoes Store Elementor
 */
?>

<div class="sidebar-area <?php if( get_theme_mod('shoes_store_elementor_enable_sticky_sidebar', false) == 1) { ?> sidebar-sticky <?php } else { ?> close-sticky <?php } ?> 
  <?php echo esc_attr( get_theme_mod('shoes_store_elementor_enable_sidebar_animation', true) ? 'zoomInRight wow' : '' ); ?>">
  <?php if ( ! dynamic_sidebar( 'shoes-store-elementor-sidebar' ) ) : ?>
    <div role="complementary" aria-label="<?php echo esc_attr__( 'sidebar1', 'shoes-store-elementor' ); ?>" id="Search" class="sidebar-widget">
      <h4 class="title" ><?php esc_html_e( 'Search', 'shoes-store-elementor' ); ?></h4>
      <?php get_search_form(); ?>
    </div>
    <div role="complementary" aria-label="<?php echo esc_attr__( 'sidebar2', 'shoes-store-elementor' ); ?>" id="archives" class="sidebar-widget">
      <h4 class="title" ><?php esc_html_e( 'Archives', 'shoes-store-elementor' ); ?></h4>
      <ul>
          <?php wp_get_archives( array( 'type' => 'monthly' ) ); ?>
      </ul>
    </div>
    <div role="complementary" aria-label="<?php echo esc_attr__( 'sidebar3', 'shoes-store-elementor' ); ?>" id="meta" class="sidebar-widget">
      <h4 class="title"><?php esc_html_e( 'Meta', 'shoes-store-elementor' ); ?></h4>
      <ul>
        <?php wp_register(); ?>
        <li><?php wp_loginout(); ?></li>
        <?php wp_meta(); ?>
      </ul>
    </div>
    <div role="complementary" aria-label="<?php echo esc_attr__( 'sidebar4', 'shoes-store-elementor' ); ?>" id="tag-cloud" class="sidebar-widget">
      <h4 class="title" ><?php esc_html_e( 'Tag Cloud', 'shoes-store-elementor' ); ?></h4>
      <?php wp_tag_cloud(); ?>
    </div>
  <?php endif; // end sidebar widget area ?>
</div>