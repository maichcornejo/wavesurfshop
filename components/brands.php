<?php
// DEBUG: confirmo que el archivo se está cargando
// echo '<!-- brands.php loaded -->';

$taxonomy = 'pa_marca'; // tu atributo slug "marca" => pa_marca

$terms = get_terms([
  'taxonomy'   => $taxonomy,
  'hide_empty' => false, // <-- importante para debug (después lo volvés a true)
  'orderby'    => 'name',
  'order'      => 'ASC',
]);

// Directorios posibles (según tu screenshot los logos están en /assets/images/brands/)
$logo_locations = [
  [
    'dir' => get_stylesheet_directory() . '/assets/images/marcas',
    'uri' => get_stylesheet_directory_uri() . '/assets/images/marcas',
  ],
  [
    'dir' => get_stylesheet_directory() . '/images/marcas',
    'uri' => get_stylesheet_directory_uri() . '/images/marcas',
  ],
];
?>

<div class="brand-carousel premium-brand-carousel" data-brand-carousel="1">
  <button class="brand-prev" type="button" aria-label="Anterior">&#10094;</button>

  <div class="brand-track-wrapper">
    <ul class="brand-track">
      <?php
      if (is_wp_error($terms)) {
        echo '<li><span style="color:#fff">ERROR get_terms: ' . esc_html($terms->get_error_message()) . '</span></li>';
      } elseif (empty($terms)) {
        echo '<li><span style="color:#fff">No hay términos en ' . esc_html($taxonomy) . '</span></li>';
      } else {

        foreach ($terms as $term) {
          $slug = $term->slug;
          $logo_url = '';

          // buscar {slug}.{ext} en ubicaciones
          foreach ($logo_locations as $loc) {
            if (!is_dir($loc['dir'])) continue;

            $matches = glob($loc['dir'] . '/' . $slug . '.{png,jpg,jpeg,webp,svg}', GLOB_BRACE);
            if (!empty($matches)) {
              $filename = basename($matches[0]);
              $logo_url = $loc['uri'] . '/' . $filename;
              break;
            }
          }

          // Si no encuentra logo, IGUAL lo muestra con texto (así ves el slug)
          $href = home_url('/tienda/?brand=' . $slug); // tu formato actual

          echo '<li>';
          echo '<a href="' . esc_url($href) . '" aria-label="' . esc_attr($term->name) . '">';

          if ($logo_url) {
            echo '<img src="' . esc_url($logo_url) . '" alt="' . esc_attr($term->name) . '">';
          } else {
            echo '<span style="color:#fff;font-weight:600;font-size:14px">' .
                  esc_html($term->name) .
                  ' <small style="opacity:.7">(' . esc_html($slug) . ' sin logo)</small>' .
                 '</span>';
          }

          echo '</a>';
          echo '</li>';
        }
      }
      ?>
    </ul>
  </div>

  <button class="brand-next" type="button" aria-label="Siguiente">&#10095;</button>
</div>
