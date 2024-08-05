<?php

////////////////////////////////////////////////////////////////////////////////////
// Gift Products Section
////////////////////////////////////////////////////////////////////////////////////

// Add a dropdown menu to display gift products for the current product category
function sale_splash_add_gift_product_dropdown() {
    global $product;

    // Only display the dropdown if the product price is 100 or more
    if ( $product->get_price() >= 100 ) {
        // Get the gift products for the current product category
        $gift_products = get_option( 'sale_splash_gift_products', array() );
        $category_gift_products = array();
        foreach ( $gift_products as $gift_product ) {
            if ( has_term( $gift_product['category'], 'product_cat', $product->get_id() ) ) {
                $category_gift_products[] = $gift_product;
            }
        }

        // If there are gift products for the current product category, display the dropdown menu
        if ( ! empty( $category_gift_products ) ) {
            echo '<div class="sale-splash-gift-wrapper">';
            echo '<div style="margin-top: 20px;"><img src="https://www.example.com/wp-content/uploads/2024/08/gift-banner.png" alt="Gift Product Image" style="max-width:100%; height:auto;"></div>';
            echo '<select name="sale_splash_gift_product" class="sale-splash-gift-product">';
            echo '<option value="">Select a gift product</option>';
            foreach ( $category_gift_products as $gift_product ) {
                echo '<option value="' . esc_attr( $gift_product['name'] ) . '">' . esc_html( $gift_product['name'] ) . '</option>';
            }
            echo '</select>';
            echo '</div>';
        }
    } else {
        // Display a disabled dropdown if the product price is less than 100
        echo '<div class="sale-splash-gift-wrapper">';
        echo '<div style="margin-top: 20px;"><img src="https://www.example.com/wp-content/uploads/2024/08/gift-banner.png" alt="Gift Product Image" style="max-width:100%; height:auto;"></div>';
        echo '<select name="sale_splash_gift_product" class="sale-splash-gift-product" disabled>';
        echo '<option value="">Gift available only for purchases over 100 TL.</option>';
        echo '</select>';
        echo '</div>';
    }
}
add_action( 'woocommerce_after_add_to_cart_form', 'sale_splash_add_gift_product_dropdown' );

// Add gift product data to cart item when selected
function sale_splash_add_cart_item_data( $cart_item_data, $product_id, $variation_id ) {
    if ( isset( $_POST['sale_splash_gift_product'] ) ) {
        $cart_item_data['sale_splash_gift_product'] = sanitize_text_field( $_POST['sale_splash_gift_product'] );
    }
    return $cart_item_data;
}
add_filter( 'woocommerce_add_cart_item_data', 'sale_splash_add_cart_item_data', 10, 3 );

// Display gift product data in the cart and checkout pages
function sale_splash_display_cart_item_data( $item_data, $cart_item ) {
    if ( isset( $cart_item['sale_splash_gift_product'] ) ) {
        $item_data[] = array(
            'key'     => 'Gift Product',
            'value'   => wc_clean( $cart_item['sale_splash_gift_product'] ),
            'display' => '',
        );
    }
    return $item_data;
}
add_filter( 'woocommerce_get_item_data', 'sale_splash_display_cart_item_data', 10, 2 );

// Append gift product information to the cart item name
function sale_splash_display_cart_item_name( $name, $cart_item, $cart_item_key ) {
    if ( isset( $cart_item['sale_splash_gift_product'] ) ) {
        $name .= ' (with gift product: ' . wc_clean( $cart_item['sale_splash_gift_product'] ) . ')';
    }
    return $name;
}
add_filter( 'woocommerce_cart_item_name', 'sale_splash_display_cart_item_name', 10, 3 );

// Display gift product information on the admin order details page
function sale_splash_display_admin_order_data( $order ) {
    $items = $order->get_items();
    $gift_products = array();
    foreach ( $items as $item ) {
        $gift_product = wc_get_order_item_meta( $item->get_id(), 'Gift Product' );
        if ( $gift_product ) {
            $gift_products[] = $gift_product;
        }
    }
    if ( ! empty( $gift_products ) ) {
        echo '<p><strong>Gift Products:</strong> ' . implode( ', ', $gift_products ) . '</p>';
    }
}
add_action( 'woocommerce_admin_order_data_after_order_details', 'sale_splash_display_admin_order_data' );

// Add gift product data to order item meta
function sale_splash_add_order_item_meta( $item_id, $values, $cart_item_key ) {
    if ( isset( $values['sale_splash_gift_product'] ) ) {
        wc_add_order_item_meta( $item_id, 'Gift Product', $values['sale_splash_gift_product'] );
    }
}
add_action( 'woocommerce_add_order_item_meta', 'sale_splash_add_order_item_meta', 10, 3 );

// Update the stock quantity of the gift product
function sale_splash_update_gift_product_stock( $order_id ) {
    $order = wc_get_order( $order_id );
    $items = $order->get_items();

    foreach ( $items as $item ) {
        $gift_product_name = wc_get_order_item_meta( $item->get_id(), 'Gift Product', true );
        
        if ( $gift_product_name ) {
            // Find the gift product
            $gift_products = get_option( 'sale_splash_gift_products', array() );
            foreach ( $gift_products as &$gift_product ) {
                if ( $gift_product['name'] == $gift_product_name ) {
                    // Decrease the stock quantity
                    $gift_product['stock'] -= 1;
                    if ( $gift_product['stock'] < 0 ) {
                        $gift_product['stock'] = 0;
                    }
                }
            }
            update_option( 'sale_splash_gift_products', $gift_products );
        }
    }
}
add_action( 'woocommerce_order_status_completed', 'sale_splash_update_gift_product_stock' );

////////////////////////////////////////////////////////////////////////////////////
// Discounted Products in Cart Section
////////////////////////////////////////////////////////////////////////////////////

// Display the campaign products on the cart page
function sale_splash_display_campaign_products() {
    $campaign_products = get_option( 'sale_splash_campaign_products', array() );
    if ( empty( $campaign_products ) ) {
        return;
    }

    $cart_total = WC()->cart->total;

    echo '<div class="sale-splash-campaign-products">';
    echo '<h2>Special Offers</h2>';
    echo '<div class="campaign-products-grid">';
    foreach ( $campaign_products as $product ) {
        $product_name = get_the_title( $product['id'] );
        $product_url = get_permalink( $product['id'] );
        $product_regular_price = wc_get_product( $product['id'] )->get_regular_price();
        $min_order_total = $product['min_order_total'];
        $button_disabled = $cart_total >= $min_order_total ? '' : 'disabled';

        echo '<div class="campaign-product">';
        echo '<a href="' . esc_url( $product_url ) . '">';
        echo '<p class="min-order-total">For purchases over ' . esc_html( $min_order_total ) . ' TL</p>';
        echo '<h3>' . esc_html( $product_name ) . '</h3>';
        echo '</a>';
        echo '<p class="campaign-regular-price">Regular Price: ' . esc_html( $product_regular_price ) . ' ₺</p>';
        echo '<p class="campaign-special-price">Your Special Price: <span class="special-price">' . esc_html( $product['price'] ) . ' ₺</span></p>';
        echo '<a href="' . esc_url( wc_get_cart_url() . '?add-to-cart=' . $product['id'] ) . '&special_price=' . esc_attr( $product['price'] ) . '" class="button add-to-cart">Add to Cart</a>';
        echo '</div>';
    }
    echo '</div>';
    echo '</div>';
}
add_action( 'woocommerce_before_cart', 'sale_splash_display_campaign_products' );

// Add campaign product to the cart with special price
function sale_splash_add_campaign_product_to_cart( $cart_item_data, $product_id ) {
    if ( isset( $_GET['special_price'] ) ) {
        $cart_item_data['campaign_price'] = sanitize_text_field( $_GET['special_price'] );
        $cart_item_data['unique_key'] = md5( microtime().rand() ); // Ensure unique key
    }
    return $cart_item_data;
}
add_filter( 'woocommerce_add_cart_item_data', 'sale_splash_add_campaign_product_to_cart', 10, 2 );

// Apply the campaign price to the cart item
function sale_splash_apply_campaign_price( $cart_object ) {
    if ( !WC()->session->__isset( "reload_checkout" ) ) {
        foreach ( $cart_object->get_cart() as $key => $value ) {
            if ( isset( $value['campaign_price'] ) ) {
                $value['data']->set_price( $value['campaign_price'] );
            }
        }
    }
}
add_action( 'woocommerce_before_calculate_totals', 'sale_splash_apply_campaign_price', 10, 1 );

// Display the campaign price in the cart item
function sale_splash_display_campaign_cart_item_data( $item_data, $cart_item ) {
    if ( isset( $cart_item['campaign_price'] ) ) {
        $item_data[] = array(
            'key'     => 'Campaign Price',
            'value'   => wc_price( $cart_item['campaign_price'] ),
            'display' => '',
        );
    }
    return $item_data;
}
add_filter( 'woocommerce_get_item_data', 'sale_splash_display_campaign_cart_item_data', 10, 2 );

// Update stock quantity for campaign products
function sale_splash_update_campaign_stock( $order_id ) {
    $order = wc_get_order( $order_id );
    $items = $order->get_items();

    foreach ( $items as $item ) {
        if ( isset( $item['campaign_price'] ) ) {
            $product_id = $item->get_product_id();
            $product = wc_get_product( $product_id );
            $current_stock = $product->get_stock_quantity();
            $new_stock = $current_stock - $item->get_quantity();
            $product->set_stock_quantity( $new_stock );
            $product->save();
        }
    }
}
add_action( 'woocommerce_order_status_completed', 'sale_splash_update_campaign_stock' );

?>