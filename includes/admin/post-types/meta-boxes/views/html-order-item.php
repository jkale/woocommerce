<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<tr class="item <?php if ( ! empty( $class ) ) echo $class; ?>" data-order_item_id="<?php echo $item_id; ?>">
	<td class="check-column"><input type="checkbox" /></td>
	<td class="thumb">
		<?php if ( $_product ) : ?>
			<a href="<?php echo esc_url( admin_url( 'post.php?post=' . absint( $_product->id ) . '&action=edit' ) ); ?>" class="tips" data-tip="<?php

				echo '<strong>' . __( 'Product ID:', 'woocommerce' ) . '</strong> ' . absint( $item['product_id'] );

				if ( $item['variation_id'] )
					echo '<br/><strong>' . __( 'Variation ID:', 'woocommerce' ) . '</strong> ' . absint( $item['variation_id'] );

				if ( $_product && $_product->get_sku() )
					echo '<br/><strong>' . __( 'Product SKU:', 'woocommerce' ).'</strong> ' . esc_html( $_product->get_sku() );

			?>"><?php echo $_product->get_image( 'shop_thumbnail', array( 'title' => '' ) ); ?></a>
		<?php else : ?>
			<?php echo woocommerce_placeholder_img( 'shop_thumbnail' ); ?>
		<?php endif; ?>
	</td>
	<td class="name">

		<?php if ( $_product && $_product->get_sku() ) echo esc_html( $_product->get_sku() ) . ' &ndash; '; ?>

		<?php if ( $_product ) : ?>
			<a target="_blank" href="<?php echo esc_url( admin_url( 'post.php?post='. absint( $_product->id ) .'&action=edit' ) ); ?>">
				<?php echo esc_html( $item['name'] ); ?>
			</a>
		<?php else : ?>
			<?php echo esc_html( $item['name'] ); ?>
		<?php endif; ?>

		<input type="hidden" class="order_item_id" name="order_item_id[]" value="<?php echo esc_attr( $item_id ); ?>" />

		<?php
			if ( $_product && isset( $_product->variation_data ) )
				echo '<br/>' . woocommerce_get_formatted_variation( $_product->variation_data, true );
		?>

		<div class="view">
			<?php
				if ( $metadata = $order->has_meta( $item_id ) ) {
					echo '<table cellspacing="0" class="display_meta">';
					foreach ( $metadata as $meta ) {

						// Skip hidden core fields
						if ( in_array( $meta['meta_key'], apply_filters( 'woocommerce_hidden_order_itemmeta', array(
							'_qty',
							'_tax_class',
							'_product_id',
							'_variation_id',
							'_line_subtotal',
							'_line_subtotal_tax',
							'_line_total',
							'_line_tax',
						) ) ) ) continue;

						// Skip serialised meta
						if ( is_serialized( $meta['meta_value'] ) )
							continue;

						echo '<tr><th>' . wp_kses_post( $meta['meta_key'] ) . ':</th><td>' . wp_kses_post( esc_textarea( urldecode( $meta['meta_value'] ) ) ) . '</td></tr>';
					}
					echo '</table>';
				}
			?>
		</div>
		<div class="edit" style="display:none">
			<table class="meta" cellspacing="0">
				<tfoot>
					<tr>
						<td colspan="4"><button class="add_order_item_meta button"><?php _e( 'Add&nbsp;meta', 'woocommerce' ); ?></button></td>
					</tr>
				</tfoot>
				<tbody class="meta_items">
				<?php
					if ( $metadata = $order->has_meta( $item_id )) {
						foreach ( $metadata as $meta ) {

							// Skip hidden core fields
							if ( in_array( $meta['meta_key'], apply_filters( 'woocommerce_hidden_order_itemmeta', array(
								'_qty',
								'_tax_class',
								'_product_id',
								'_variation_id',
								'_line_subtotal',
								'_line_subtotal_tax',
								'_line_total',
								'_line_tax',
							) ) ) ) continue;

							// Skip serialised meta
							if ( is_serialized( $meta['meta_value'] ) )
								continue;

							$meta['meta_key']   = esc_attr( $meta['meta_key'] );
							$meta['meta_value'] = esc_textarea( urldecode( $meta['meta_value'] ) ); // using a <textarea />
							$meta['meta_id']    = absint( $meta['meta_id'] );

							echo '<tr data-meta_id="' . $meta['meta_id'] . '">
								<td><input type="text" name="meta_key[' . $meta['meta_id'] . ']" value="' . $meta['meta_key'] . '" /></td>
								<td><input type="text" name="meta_value[' . $meta['meta_id'] . ']" value="' . $meta['meta_value'] . '" /></td>
								<td width="1%"><button class="remove_order_item_meta button">&times;</button></td>
							</tr>';
						}
					}
				?>
				</tbody>
			</table>
		</div>
	</td>

	<?php do_action( 'woocommerce_admin_order_item_values', $_product, $item, absint( $item_id ) ); ?>

	<?php if ( get_option( 'woocommerce_calc_taxes' ) == 'yes' ) : ?>

	<td class="tax_class" width="1%">
		<div class="view">
			<?php
				$item_value = isset( $item['tax_class'] ) ? sanitize_title( $item['tax_class'] ) : '';
				echo $item_value ? $item_value : __( 'Standard', 'woocommerce' );
			?>
		</div>
		<div class="edit" style="display:none">
			<select class="tax_class" name="order_item_tax_class[<?php echo absint( $item_id ); ?>]" title="<?php _e( 'Tax class', 'woocommerce' ); ?>">
				<?php
				$item_value = isset( $item['tax_class'] ) ? sanitize_title( $item['tax_class'] ) : '';

				$tax_classes = array_filter( array_map( 'trim', explode( "\n", get_option('woocommerce_tax_classes' ) ) ) );

				$classes_options = array();
				$classes_options[''] = __( 'Standard', 'woocommerce' );

				if ( $tax_classes )
					foreach ( $tax_classes as $class )
						$classes_options[ sanitize_title( $class ) ] = $class;

				foreach ( $classes_options as $value => $name )
					echo '<option value="' . esc_attr( $value ) . '" ' . selected( $value, $item_value, false ) . '>' . esc_html( $name ) . '</option>';
				?>
			</select>
		</div>
	</td>

	<?php endif; ?>

	<td class="quantity" width="1%">
		<div class="view">
			<?php if ( isset( $item['qty'] ) ) echo esc_html( $item['qty'] ); ?>
		</div>
		<div class="edit" style="display:none">
			<input type="number" step="<?php echo apply_filters( 'woocommerce_quantity_input_step', '1', $_product ); ?>" min="0" autocomplete="off" name="order_item_qty[<?php echo absint( $item_id ); ?>]" placeholder="0" value="<?php echo esc_attr( $item['qty'] ); ?>" size="4" class="quantity" />
		</div>
	</td>

	<td class="line_cost" width="1%">
		<div class="view">
			<?php
				if ( isset( $item['line_total'] ) ) {
					if ( isset( $item['line_subtotal'] ) && $item['line_subtotal'] != $item['line_total'] ) echo '<del>' . woocommerce_price( $item['line_subtotal'] ) . '</del> ';

					echo woocommerce_price( $item['line_total'] );
				}
			?>
		</div>
		<div class="edit" style="display:none">
			<span class="subtotal"><label><?php _e( 'Subtotal', 'woocommerce' ); ?>: <a class="tips" data-tip="<?php _e( 'Before pre-tax discounts.', 'woocommerce' ); ?>" href="#">[?]</a> <input type="number" step="any" min="0" name="line_subtotal[<?php echo absint( $item_id ); ?>]" placeholder="0.00" value="<?php if ( isset( $item['line_subtotal'] ) ) echo esc_attr( $item['line_subtotal'] ); ?>" class="line_subtotal" /></label></span>

			<label><?php _e( 'Total', 'woocommerce' ); ?>: <a class="tips" data-tip="<?php _e( 'After pre-tax discounts.', 'woocommerce' ); ?>" href="#">[?]</a> <input type="number" step="any" min="0" name="line_total[<?php echo absint( $item_id ); ?>]" placeholder="0.00" value="<?php if ( isset( $item['line_total'] ) ) echo esc_attr( $item['line_total'] ); ?>" class="line_total" /></label>
		</div>
	</td>

	<?php if ( get_option( 'woocommerce_calc_taxes' ) == 'yes' ) : ?>

	<td class="line_tax" width="1%">
		<div class="view">
			<?php
				if ( isset( $item['line_tax'] ) ) {
					if ( isset( $item['line_subtotal_tax'] ) && $item['line_subtotal_tax'] != $item['line_tax'] ) echo '<del>' . woocommerce_price( $item['line_subtotal_tax'] ) . '</del> ';

					echo woocommerce_price( $item['line_tax'] );
				}
			?>
		</div>
		<div class="edit" style="display:none">
			<span class="subtotal"><input type="number" step="any" min="0" name="line_subtotal_tax[<?php echo absint( $item_id ); ?>]" placeholder="0.00" value="<?php if ( isset( $item['line_subtotal_tax'] ) ) echo esc_attr( $item['line_subtotal_tax'] ); ?>" class="line_subtotal_tax" /></span>

			<input type="number" step="any" min="0" name="line_tax[<?php echo absint( $item_id ); ?>]" placeholder="0.00" value="<?php if ( isset( $item['line_tax'] ) ) echo esc_attr( $item['line_tax'] ); ?>" class="line_tax" />
		</div>
	</td>

	<?php endif; ?>

	<td>
		<a class="edit_order_item" href="#"><img src="<?php echo $woocommerce->plugin_url(); ?>/assets/images/icons/edit.png" alt="Edit" width="14" /></a>
	</td>

</tr>