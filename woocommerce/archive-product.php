<?php
defined('ABSPATH') || exit;

get_header();

do_action('woocommerce_before_main_content');
?>

<header class="shop-header">
  <h1><?php woocommerce_page_title(); ?></h1>
</header>
<div class="container shop-layout">


  <aside class="shop-filters">

    <h3>Filtrar</h3>

    <?php
    /**
     * ATRIBUTOS DINÁMICOS
     * Ej: pa_talle, pa_color, pa_marca
     */
    $attributes = wc_get_attribute_taxonomies();

    foreach ($attributes as $attribute) :

        $taxonomy = wc_attribute_taxonomy_name($attribute->attribute_name);

        if (!taxonomy_exists($taxonomy)) continue;

        // Obtener términos SOLO si hay productos en esta query
        $terms = get_terms([
            'taxonomy'   => $taxonomy,
            'hide_empty' => true,
        ]);

        if (empty($terms)) continue;
    ?>

    <div class="filter-block">
      <h4><?php echo esc_html($attribute->attribute_label); ?></h4>

      <div class="filter-options">
        <?php foreach ($terms as $term) : ?>
          <label class="filter-checkbox">
            <input type="checkbox"
                  class="filter-term"
                  data-taxonomy="<?php echo esc_attr($taxonomy); ?>"
                  value="<?php echo esc_attr($term->slug); ?>">
            <?php echo esc_html($term->name); ?>
          </label>
        <?php endforeach; ?>
      </div>
    </div>


    <?php endforeach; ?>

    <!-- PRECIO -->
    <div class="filter-block">
      <h4>Precio</h4>
      <input type="range" id="price-filter" min="0" max="1000000" step="1000">
      <span id="price-output"></span>
    </div>

  </aside>

  <main class="shop-results">



    <div id="products-list">
      <?php if (woocommerce_product_loop()) : ?>

        <?php woocommerce_product_loop_start(); ?>

        <?php while (have_posts()) : the_post(); ?>
          <?php wc_get_template_part('content', 'product'); ?>
        <?php endwhile; ?>

        <?php woocommerce_product_loop_end(); ?>

      <?php else : ?>
        <p>No se encontraron productos.</p>
      <?php endif; ?>
    </div>

  </main>

</div>

<?php
do_action('woocommerce_after_main_content');
get_footer();
