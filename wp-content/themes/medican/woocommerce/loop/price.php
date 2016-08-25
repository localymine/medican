<?php
/**
 * Loop Price
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     10.0.0
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $product;
$options = get_option(AZEXO_THEME_NAME);
?>

<?php if ($price_html = $product->get_price_html()) : ?>
    <span class="price"><?php print isset($options['loop_price_prefix']) ? '<span class="prefix">' . $options['loop_price_prefix'] . '</span> ' : ''; ?><?php print $price_html; ?></span>
<?php endif; ?>
