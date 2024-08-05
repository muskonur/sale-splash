<?php
function sale_splash_admin_page() {
    add_menu_page(
        'Sale Splash',
        'Sale Splash',
        'manage_options',
        'sale-splash',
        'sale_splash_dashboard_callback'
    );

    add_submenu_page(
        'sale-splash',
        'Sale Splash Gift Products',
        'Gift Products',
        'manage_options',
        'sale-splash-gift-products',
        'sale_splash_gift_products_callback'
    );

    add_submenu_page(
        'sale-splash',
        'Sale Splash Campaign Products',
        'Campaign Products',
        'manage_options',
        'sale-splash-campaign-products',
        'sale_splash_campaign_products_callback'
    );
}
add_action( 'admin_menu', 'sale_splash_admin_page' );

function sale_splash_admin_enqueue_scripts( $hook ) {
    if ( 'toplevel_page_sale-splash' != $hook ) {
        return;
    }

    wp_enqueue_script( 'sale-splash-admin-script', SALE_SPLASH_PLUGIN_URL . 'includes/admin/script.js', array( 'jquery' ), '1.0.0', true );
    wp_enqueue_style( 'sale-splash-admin-style', SALE_SPLASH_PLUGIN_URL . 'includes/admin/style.css', array(), '1.0.0', 'all' );
}
add_action( 'admin_enqueue_scripts', 'sale_splash_admin_enqueue_scripts' );

function sale_splash_dashboard_callback() {
}

// Callback function for the gift products page
function sale_splash_gift_products_callback() {
    // Get the list of WooCommerce categories
    $categories = get_terms( array(
        'taxonomy' => 'product_cat',
        'hide_empty' => false,
    ));

    // Handle form submission
    if ( isset( $_POST['sale_splash_gift_product_submit'] ) ) {
        // Sanitize and validate the form data
        $product_name = sanitize_text_field( $_POST['sale_splash_gift_product_name'] );
        $product_category = absint( $_POST['sale_splash_gift_product_category'] );
        $product_stock = absint( $_POST['sale_splash_gift_product_stock'] );

        // Save the form data to the database
        $gift_products = get_option( 'sale_splash_gift_products', array() );
        $gift_products[] = array(
            'name' => $product_name,
            'category' => $product_category,
            'stock' => $product_stock,
        );
        update_option( 'sale_splash_gift_products', $gift_products );

        // Display a success message
        echo '<div class="notice notice-success"><p>Gift product added successfully.</p></div>';
    }

    // Output the form HTML
    ?>
    <div class="wrap">
        <h1>Sale Splash Gift Products</h1>
        <form method="post" action="">
            <table class="form-table">
                <tr valign="top">
                    <td><input type="text" name="sale_splash_gift_product_name" value="" placeholder="Product Name" /></td>
                    <td>
                        <select name="sale_splash_gift_product_category">
                            <?php foreach ( $categories as $category ) : ?>
                                <option value="<?php echo esc_attr( $category->term_id ); ?>"><?php echo esc_html( $category->name ); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td><input type="number" name="sale_splash_gift_product_stock" value="" placeholder="Stock Quantity" /></td>
                </tr>
            </table>
            <input type="submit" name="sale_splash_gift_product_submit" value="Add Product" class="button button-primary" />
        </form>
    </div>
    <?php

    // Display the list of gift products
    $gift_products = get_option( 'sale_splash_gift_products', array() );
    if ( ! empty( $gift_products ) ) {
        echo '<h2>Gift Products</h2>';
        echo '<table class="widefat">';
        echo '<thead><tr><th>Name</th><th>Category</th><th>Stock</th></thead>';
        echo '<tbody>';
        foreach ( $gift_products as $product ) {
            $category = get_term( $product['category'], 'product_cat' );
            echo '<tr>';
            echo '<td>' . esc_html( $product['name'] ) . '</td>';
            echo '<td>' . esc_html( $category->name ) . '</td>';
            echo '<td>' . esc_html( $product['stock'] ) . '</td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
    }
}

// Callback function for the campaign products page
function sale_splash_campaign_products_callback() {
    // Handle form submission
    if ( isset( $_POST['sale_splash_campaign_product_submit'] ) ) {
        // Sanitize and validate the form data
        $product_id = sanitize_text_field( $_POST['sale_splash_campaign_product_id'] );
        $min_order_total = sanitize_text_field( $_POST['sale_splash_min_order_total'] );
        $special_price = sanitize_text_field( $_POST['sale_splash_special_price'] );

        // Save the form data to the database
        $campaign_products = get_option( 'sale_splash_campaign_products', array() );
        $campaign_products[] = array(
            'id' => $product_id,
            'min_order_total' => $min_order_total,
            'price' => $special_price,
        );
        update_option( 'sale_splash_campaign_products', $campaign_products );

        // Display a success message
        echo '<div class="notice notice-success"><p>Campaign product added successfully.</p></div>';
    }

    // Handle product deletion
    if ( isset( $_POST['sale_splash_delete_campaign_product'] ) ) {
        $product_index = absint( $_POST['sale_splash_delete_campaign_product'] );
        $campaign_products = get_option( 'sale_splash_campaign_products', array() );
        if ( isset( $campaign_products[$product_index] ) ) {
            unset( $campaign_products[$product_index] );
            update_option( 'sale_splash_campaign_products', array_values( $campaign_products ) );
            echo '<div class="notice notice-success"><p>Campaign product deleted successfully.</p></div>';
        }
    }

    // Output the form HTML
    ?>
    <div class="wrap">
        <h1>Sale Splash Campaign Products</h1>
        <form method="post" action="">
            <table class="form-table">
                <tr valign="top">
                    <td><input type="text" name="sale_splash_campaign_product_id" value="" placeholder="Product ID" required /></td>
                    <td><input type="text" name="sale_splash_min_order_total" value="" placeholder="Minimum Order Total" required /></td>
                    <td><input type="text" name="sale_splash_special_price" value="" placeholder="Special Price" required /></td>
                </tr>
            </table>
            <input type="submit" name="sale_splash_campaign_product_submit" value="Add Product" class="button button-primary" />
        </form>
    </div>
    <?php

    // Display the list of campaign products
    $campaign_products = get_option( 'sale_splash_campaign_products', array() );
    if ( ! empty( $campaign_products ) ) {
        echo '<h2>Campaign Products</h2>';
        echo '<table class="widefat">';
        echo '<thead><tr><th>Product ID</th><th>Product Name</th><th>Minimum Order Total</th><th>Special Price</th><th>Actions</th></tr></thead>';
        echo '<tbody>';
        foreach ( $campaign_products as $index => $product ) {
            $product_name = get_the_title( $product['id'] );
            echo '<tr>';
            echo '<td>' . esc_html( $product['id'] ) . '</td>';
            echo '<td>' . esc_html( $product_name ) . '</td>';
            echo '<td>' . esc_html( $product['min_order_total'] ) . '</td>';
            echo '<td>' . esc_html( $product['price'] ) . '</td>';
            echo '<td>
                    <form method="post" action="">
                        <input type="hidden" name="sale_splash_delete_campaign_product" value="' . esc_attr( $index ) . '" />
                        <input type="submit" class="button button-secondary" value="Delete" />
                    </form>
                  </td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
    }
}

