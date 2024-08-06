# Sale Splash

**Sale Splash** is a powerful WooCommerce plugin designed to enhance your store's promotional capabilities. With this plugin, you can easily manage and display gift products for specific categories, and offer discounted products based on the cart total. Perfect for boosting sales and engaging customers with special offers and gifts.

## Features

- **Gift Products**: Offer free gift products based on product categories and cart total.
- **Campaign Products**: Display discounted products on the cart page with special prices for purchases over a specified amount.
- **Stock Management**: Automatically update the stock quantities for gift and campaign products.

## Installation

1. **Download the plugin**: Download the plugin from the GitHub repository.
2. **Upload to WordPress**: Go to your WordPress dashboard, navigate to `Plugins > Add New > Upload Plugin`, and upload the `sale-splash.zip` file.
3. **Activate the plugin**: After the plugin is uploaded, click on `Activate Plugin`.

## Usage

### Gift Products

1. **Add Gift Products**: Go to `WooCommerce > Settings > Sale Splash` and add your gift products along with the categories they apply to.
2. **Set Minimum Price**: Set a minimum price threshold for the gift product to be available.
3. **Display in Product Page**: The gift product dropdown will automatically appear on the single product page if the product price meets the minimum threshold.

### Campaign Products

1. **Add Campaign Products**: Go to `WooCommerce > Settings > Sale Splash` and add your campaign products with special prices and minimum order total.
2. **Display on Cart Page**: The campaign products will be displayed on the cart page if the cart total meets the minimum order amount.

## Shortcodes

No shortcodes are required as the plugin automatically integrates with your WooCommerce store.

## Hooks & Filters

### Hooks

- `woocommerce_after_add_to_cart_form`: Displays the gift product dropdown on the single product page.
- `woocommerce_order_status_completed`: Updates the stock quantity for gift and campaign products upon order completion.

### Filters

- `woocommerce_add_cart_item_data`: Adds gift and campaign product data to cart items.
- `woocommerce_get_item_data`: Displays gift and campaign product data in the cart and checkout pages.
- `woocommerce_cart_item_name`: Appends gift product information to the cart item name.
- `woocommerce_before_calculate_totals`: Applies the campaign price to the cart item.

## Contributing

We welcome contributions to enhance the functionality of the Sale Splash plugin. To contribute, please fork the repository, create a feature branch, and submit a pull request. 

## License

This plugin is open-source software licensed under the [MIT License](https://opensource.org/licenses/MIT).

## Support

For support and issues, please open a ticket on the [GitHub Issues](https://github.com/muskonur/sale-splash/issues) page.

---

Thank you for using **Sale Splash**! We hope this plugin helps you boost your WooCommerce store sales with exciting gift and campaign products.
