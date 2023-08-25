<?php
class CustomCouponAddon {
    private $couponCode = 'coudouble';
    
    public function __construct() {
        add_action('init', array($this, 'custom_addon_init'));
    }
    
    public function custom_addon_init() {
        if (!is_plugin_active('woocommerce/woocommerce.php')) {
            deactivate_plugins(plugin_basename(__FILE__));
            if (isset($_GET['activate'])) {
                unset($_GET['activate']);
            }
            add_action('admin_notices', array($this, 'it_addon_woocommerce_notice'));
        }
        
        add_action('woocommerce_after_cart_item_quantity_update', array($this, 'update_custom_checkbox_state_on_quantity_change'), 10, 4);
        add_filter('woocommerce_coupon_get_discount_amount', array($this, 'it_wc_discount_added'), 10, 5);
    }
    
    public function it_addon_woocommerce_notice() {
        ?>
        <div class="notice notice-error is-dismissible">
            <p><?php esc_html_e('The IT Particular Coupon Addon requires WooCommerce to be active.', 'it-particular-coupon'); ?></p>
        </div>
        <?php
    }
    
    public function update_custom_checkbox_state_on_quantity_change($cart_item_key, $quantity, $old_quantity, $cart) {
        $cart_item = $cart->cart_contents[$cart_item_key];
        $cart->cart_contents[$cart_item_key]['old_quantity'] = $old_quantity;
    }
    
    public function it_wc_discount_added($discount, $discounting_amount, $cart_item, $single, $coupon) {
        $old_quantity = (isset($cart_item['old_quantity']) ? $cart_item['old_quantity'] : 0);

        if ($old_quantity > $cart_item['quantity']) {
            $old_quantity = $old_quantity - $cart_item['quantity'];
        }

        if ($coupon->code === $this->couponCode) {
            $discount_type = $coupon->get_discount_type();
            $coupon_amount = $coupon->get_amount();
            $discount = 0;
            $count = 2;
            if ($count < $cart_item['quantity'] && $old_quantity != '0') {
                $proid = $cart_item['product_id'];
                $product = wc_get_product($proid);
                $product_price = $product->get_price();
                if ($discount_type == 'percent') {
                    for ($i = $count; $i < $cart_item['quantity']; $i++) {
                        $discount += ($product_price * $coupon_amount) / 100;
                    }
                }
            }else{
                $discount = 0;
            }
        }

        return $discount;
    }
}