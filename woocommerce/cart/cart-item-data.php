<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! empty( $item_data ) ) : ?>
    
    <div class="waves-cart-attributes">
        <?php foreach ( $item_data as $data ) : ?>
            <div class="waves-attr-row">
                <span class="waves-attr-label">
                    <?php echo wp_kses_post( $data['key'] ); ?>:
                </span>

                <span class="waves-attr-value">
                    <?php echo wp_kses_post( wpautop( $data['display'] ) ); ?>
                </span>
            </div>
        <?php endforeach; ?>
    </div>

<?php endif; ?>
