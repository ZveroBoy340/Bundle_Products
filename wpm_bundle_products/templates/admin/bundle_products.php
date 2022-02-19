<div id="wpm_bundle_products_tab_content" class="panel woocommerce_options_panel hidden">
    <div class="section_data">
        <div class="title">Select product which will be added as bundle for free</div>
        <div class="head_items" style="<?php if ( isset( $settings['product'] ) && is_array($settings['product']) && count( $settings['product'] ) > 0 ) {
        } else {
            echo esc_attr( "display: none" );
        } ?>">
            <div class="number_element">#</div>
            <div class="item-table">Product</div>
        </div>
        <div class="items-list">
            <?php if(isset($bundle_products) && is_array($bundle_products) && count($bundle_products) > 0) {
                $i = 1;
                foreach ($bundle_products as $product_id) { ?>
                    <div class="item-content">
                        <div class="number_element"><?php echo esc_html($i); ?></div>
                        <div class="item-table">
                            <select name="wpm_bundle_products[]" class="select" data-search="true">
                                <option value="0">No Product</option>
                                <?php foreach ($products as $product) { ?>
                                    <option value="<?php echo esc_html($product->ID) ?>" <?php if ($product->ID == $product_id) {
                                        echo esc_attr('selected');
                                    } ?>><?php echo esc_html($product->post_title) ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="delete_item_bundle"><i class="fas fa-trash"></i></div>
                    </div>
                    <?php $i ++;
                }
            } else { ?>
                <div class="item-content" style="display: none">
                    <div class="number_element">1</div>
                    <div class="item-table">
                        <select name="wpm_bundle_products[]" class="select" data-search="true">
                            <option value="0">No Product</option>
                            <?php foreach($products as $product) { ?>
                                <option value="<?php echo esc_attr($product->ID) ?>"><?php echo esc_html($product->post_title) ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="delete_item_bundle"><i class="fas fa-trash"></i></div>
                </div>
            <?php } ?>
            <button class="button button-primary button-large add-item-bundle" type="button"><i class="fas fa-plus-square"></i> Add new Item</button>
        </div>
    </div>
</div>