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

    <div class="filters-head">
      <h3 style="margin:0; color:#fff">Filtrar</h3>
      <button type="button" class="filters-reset" id="filters-reset">Limpiar</button>
    </div>

    <?php
    $attributes = wc_get_attribute_taxonomies();

    foreach ($attributes as $attribute) :
      $taxonomy = wc_attribute_taxonomy_name($attribute->attribute_name);
      if (!taxonomy_exists($taxonomy)) continue;

      $terms = get_terms([
        'taxonomy'   => $taxonomy,
        'hide_empty' => true,
      ]);

      if (empty($terms)) continue;

      $count_terms = count($terms);
      $is_size = ($attribute->attribute_name === 'talle' || stripos($attribute->attribute_label, 'talle') !== false);
    ?>
      <div class="filter-block" data-taxonomy="<?php echo esc_attr($taxonomy); ?>">

        <button type="button" class="filter-toggle" aria-expanded="true">
          <span class="label"><?php echo esc_html($attribute->attribute_label); ?></span>
          <span class="meta">
            <span class="selected-count">0</span>
            <span class="chev">▾</span>
          </span>
        </button>

        <div class="filter-body">
          <?php if ($count_terms > 12): ?>
            <input type="text" class="filter-search" placeholder="Buscar...">
          <?php endif; ?>

          <div class="filter-options <?php echo $is_size ? 'is-size' : ''; ?>" data-limit="14">
            <?php foreach ($terms as $term) : ?>
              <label class="filter-checkbox" data-term-name="<?php echo esc_attr(mb_strtolower($term->name)); ?>">
                <input type="checkbox"
                      class="filter-term"
                      data-taxonomy="<?php echo esc_attr($taxonomy); ?>"
                      value="<?php echo esc_attr($term->slug); ?>">
                <?php echo esc_html($term->name); ?>
              </label>
            <?php endforeach; ?>
          </div>

          <?php if ($count_terms > 14): ?>
            <button type="button" class="filter-more">Ver más</button>
          <?php endif; ?>
        </div>

      </div>
    <?php endforeach; ?>

    <!-- PRECIO -->
    <div class="filter-block">
      <button type="button" class="filter-toggle" aria-expanded="true">
        <span class="label">Precio</span>
        <span class="meta"><span class="chev">▾</span></span>
      </button>
      <div class="filter-body">
        <input type="range" id="price-filter" min="0" max="1000000" step="1000">
        <span id="price-output"></span>
      </div>
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
        <?php do_action('woocommerce_after_shop_loop'); ?>

      <?php else : ?>
        <p>No se encontraron productos.</p>
      <?php endif; ?>
    </div>

  </main>

</div>

<?php
do_action('woocommerce_after_main_content');
get_footer();
