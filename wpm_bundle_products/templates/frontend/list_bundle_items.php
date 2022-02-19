<div class="bundles-include">
    <?php
        if($bundle_products && !empty($bundle_products)) {
            foreach($bundle_products as $item => $product_id) {
                if($item == 0 && $product_id != 0) {
                    echo '<h3>This bundles includes:</h3>';
                } elseif($product_id == 0) {
                    break;
                }
                $product = wc_get_product($product_id);
                ?>
                <div class="bundle-item">
                    <div class="image-bundle">
                        <img src="<?php if(wp_get_attachment_url($product->get_image_id())) { echo esc_attr(wp_get_attachment_url($product->get_image_id())); } else {echo esc_attr(WPM_BUNDLE_DIR.'/templates/assets/img/placeholder.jpg');} ?>" alt="">
                    </div>
                    <div class="content-bundle">
                        <div class="product-bundle-name">
                            <a href="<?php echo esc_attr(get_permalink($product_id)); ?>" target="_blank"><?php echo esc_html($product->get_title()); ?></a>
                        </div>
                        <div class="product-bundle-price">
                            <?php echo esc_html($product->get_price().get_woocommerce_currency_symbol()); ?>
                        </div>
                    </div>
                </div>
            <?php }
        }
    ?>
</div>