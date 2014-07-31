<?php
/**
 * Plugin Name: WooCommerce Payment Installments
 * Plugin URI: http://claudiosmweb.com/
 * Description: Added the price with 12 installments without interest.
 * Author: claudiosanches
 * Author URI: http://www.claudiosmweb.com/
 * Version: 1.0
 * License: GPLv2 or later
 */
 
/**
 * Calculates the price in 12 installments without interest.
 *
 * @return string Price in 12 installments.
 */
function cs_product_value() {
$product = get_product();
 
   $product = get_product();
    if ( $product->get_price() ) {
      $price = $product->get_price();
    }
        return $price;
}

function vezes(){
$preco = html_entity_decode(cs_product_value());
$vl_venda = str_replace(array('.',',','R$'),'', $preco);
$vl_min_parc = 10; #mínimo de $10 por parcela
$qt_max_parc = 12; #quantidade máxima de parcelas
      
$qt_parc = floor(($vl_venda/100) / $vl_min_parc);

if($qt_parc > $qt_max_parc):
	$qt_parc = $qt_max_parc;
else:
	$qt_parc = $qt_parc;
endif;
    
return $qt_parc;
}


function cs_product_parceled() {
    $product = get_product();
 
   $value = cs_product_value() / vezes();
        
    $value = number_format($value,2, ',', '.');
    
    return $value;
}
 
/**
 * Displays the Installments on product loop.
 * 
 * @return string Price in 12 installments.
 */
function cs_product_parceled_loop() {
    echo '<br /><span style="color: #666; font-size: 100%" class="price">' . __( 'ou '.vezes().' de' ) . ' ' . cs_product_parceled() . '</span>';
}
 
/**
 * Displays the Installments on the product page.
 *
 * @return string Price in 12 installments.
 */
function cs_product_parceled_single() {
    $product = get_product();
?>
    <div itemprop="offers" itemscope itemtype="http://schema.org/Offer">
 
        <p style="margin: 0;" itemprop="price" class="price">
            <?php echo $product->get_price_html(); ?>
        <span style="color: #666; font-size: 100%" class="price"><?php echo _e( 'ou '.vezes().'x de' ) ?> <?php echo 'R$ '.cs_product_parceled(); ?></span>
        </p>
 
        <meta itemprop="priceCurrency" content="<?php echo get_woocommerce_currency(); ?>" />
        <link itemprop="availability" href="http://schema.org/<?php echo $product->is_in_stock() ? 'InStock' : 'OutOfStock'; ?>" />
    </div>
<?php
}


function wc_parcela(){
$preco = html_entity_decode(cs_product_value());
$preco = str_replace(array('.',',','R$'),'', $preco);
$vl_venda = $preco/100;
$vl_min_parc = 10;

$vl_parc = $vl_venda / vezes();

    ?>
<div class="parcelas-esquerda">
    <span class="parcelas">Parcelas</span><span class="valor">Valor</span>
	<?php
	for ($i = 1; $i <= 6; $i++ )
	{

        $parcela = ( $vl_venda / $i );

	   if($parcela > $vl_min_parc):
		 if ( $parcela < vezes())
			break;
                        echo '<span class="parcelas">'.$i.'</span>';
                        echo '<span class="valor">R$ '.number_format($parcela,2,",",".").'</span>';
			
	   endif;
    }
    ?>
</div>
<div class="parcelas-direita">
    <span class="parcelas">Parcelas</span><span class="valor">Valor</span>
    <?php
    for ($i = 7; $i <= vezes(); $i++ )
    {
        $parcela = ( $vl_venda / $i );

		if ( $parcela < vezes() )
			break;
			echo '<span class="parcelas">'.$i.'</span>';
                        echo '<span class="valor">R$ '.number_format($parcela,2,",",".").'</span>';
	}
        ?>
        </div>
        <?php
}
	?>
<?php
function enqueue_styles() {
		wp_enqueue_style( '2-plugin-styles', plugins_url( 'assets/css/installment.css', __FILE__ ), array() );
}
function price() {

remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
add_action( 'woocommerce_single_product_summary', 'cs_product_parceled_single', 10 );
add_action( 'woocommerce_single_product_summary', 'wc_parcela', 11);
add_action( 'woocommerce_after_shop_loop_item_title', 'cs_product_parceled_loop', 20 );
add_action( 'wp_enqueue_scripts', 'enqueue_styles' );

add_shortcode( 'parcela' , 'vezes' );
add_shortcode( 'valor' , 'cs_product_parceled' );
}
add_action('plugins_loaded','price');
