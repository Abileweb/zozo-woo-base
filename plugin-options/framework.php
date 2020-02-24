<?php 
class zozo_woo_base_framework_options {
	
	public static $opt_name;
	
	public static $zozo_woo_addon_options = array();
	
	private static $pages;

	private static $sidebars;
	
	public static function zozo_woo_get_select( $get ) {
		$get_res = '';
		switch( $get ){
			case "pages":
				if( empty( self::$pages ) ){
					$pages = get_pages();
					$page_arr = array();
					foreach ( $pages as $page ) {
						$page_arr[$page->ID] = $page->post_title;
					}
					self::$pages = $page_arr;
				}
				$get_res = self::$pages;
			break;
			case "sidebars":
				if( empty( self::$sidebars ) ){
					global $wp_registered_sidebars;
					$sidebars_arr = array();
					foreach($wp_registered_sidebars as $sidebar_id => $sidebar) {
						$sidebars_arr[$sidebar_id] = $sidebar['name'];
					}
					self::$sidebars = $sidebars_arr;
				}
				$get_res = self::$sidebars;
			break;
		}
		return $get_res;			
	}

	public static function zozo_woo_set_section( $settings ){
	?>
		<div class="zwb-admin-section meta-box-sortables ui-sortable">
			<div class="postbox">
				<button type="button" class="handlediv"><span class="toggle-indicator" aria-hidden="true"></span></button>
				<h2 class="hndle ui-sortable-handle"><span><?php echo isset( $settings['title'] ) ? esc_html( $settings['title'] ) : esc_html__( 'Settings', 'zozo-woo-addon' ); ?></span></h2>
				<div class="inside">
					<div class="zwb-section-fields">
						<?php echo self::zozo_woo_set_filed( $settings['id'], $settings['fields'] ); ?>
					</div>
				</div>
			</div>
		</div><!-- .zwb-admin-section -->
	<?php
	}
	
	public static function zozo_woo_set_filed( $id, $fields ){
	
		$zozo_woo_addon_options = self::$zozo_woo_addon_options;
	
		$field_element = '';
		$field_title = '';
		$field_out = '';
		foreach( $fields as $field ){
		
			$description = isset( $field['desc'] ) ? $field['desc'] : '';
		
			switch( $field['type'] ){
				case "switch":
					$default = isset( $field['default'] ) ? $field['default'] : '';
					$saved_val = isset( $zozo_woo_addon_options[$field['id']] ) ? $zozo_woo_addon_options[$field['id']] : $default;
					$checked_stat = $saved_val == 1 ? 'checked="checked"' : '';
					$field_element = '
					<label class="switch">
						<input type="hidden" name="'. esc_attr( self::$opt_name ) .'['. esc_attr( $field['id'] ) .']" value="'. esc_attr( $saved_val ) .'">
						<input class="switch-checkbox" type="checkbox" '. $checked_stat .'>
						<span class="slider round"></span>
					</label>';
					$field_element .= '<p class="zwb-field-desc">'. $description .'</p>';
				break;
				
				case "text":
					$default = isset( $field['default'] ) ? $field['default'] : '';
					$saved_val = isset( $zozo_woo_addon_options[$field['id']] ) ? $zozo_woo_addon_options[$field['id']] : $default;
					$field_element = '<input class="text-box" type="text" name="'. esc_attr( self::$opt_name ) .'['. esc_attr( $field['id'] ) .']" value="'. esc_attr( $saved_val ) .'">';
					$field_element .= '<p class="zwb-field-desc">'. $description .'</p>';
				break;
				
				case "textarea":
					$default = isset( $field['default'] ) ? $field['default'] : '';
					$saved_val = isset( $zozo_woo_addon_options[$field['id']] ) ? $zozo_woo_addon_options[$field['id']] : $default;
					$field_element = '<textarea class="text-box" name="'. esc_attr( self::$opt_name ) .'['. esc_attr( $field['id'] ) .']">'. ( $saved_val ) .'</textarea>';
					$field_element .= '<p class="zwb-field-desc">'. $description .'</p>';
				break;
				
				case "color":
					$default = isset( $field['default'] ) ? $field['default'] : '';
					$saved_val = isset( $zozo_woo_addon_options[$field['id']] ) ? $zozo_woo_addon_options[$field['id']] : $default;
					$field_element = '<input class="zwb-color-field" type="text" name="'. esc_attr( self::$opt_name ) .'['. esc_attr( $field['id'] ) .']" value="'. esc_attr( $saved_val ) .'">';
					$field_element .= '<p class="zwb-field-desc">'. $description .'</p>';
				break;
				
				case "image":
					$saved_val = isset( $zozo_woo_addon_options[$field['id']] ) ? $zozo_woo_addon_options[$field['id']] : '';
					$field_element = '';
					$field_element .= $saved_val ? '<img src="'. esc_url( $saved_val ) .'" alt="'. esc_attr__( 'Image', 'zozo-woo-addon' ) .'" class="zwb-uploaded-image" />' : '';
					$field_element .= '<input class="zwb-upload-image-val" type="hidden" name="'. esc_attr( self::$opt_name ) .'['. esc_attr( $field['id'] ) .']" value="'. esc_attr( $saved_val ) .'">';
					$field_element .= '<button class="zwb-upload-image-button">'. esc_html__( 'Upload', 'zozo-woo-addon' ) .'</button>';
					$field_element .= '<button class="zwb-remove-image-button">'. esc_html__( 'Remove', 'zozo-woo-addon' ) .'</button>';
					$field_element .= '<p class="zwb-field-desc">'. $description .'</p>';
				break;
				
				case "select":
					$default = isset( $field['default'] ) ? $field['default'] : '';
					$get = isset( $field['get'] ) ? self::zozo_woo_get_select( $field['get'] ) : '';
					$saved_val = isset( $zozo_woo_addon_options[$field['id']] ) ? $zozo_woo_addon_options[$field['id']] : $default;
					$options = $get ? $get : ( isset( $field['options'] ) ? $field['options'] : '' );
					$select_out = '<select class="select-option" name="'. esc_attr( self::$opt_name ) .'['. esc_attr( $field['id'] ) .']">';
					if( !empty( $options ) ){
						foreach( $options as $key => $value ){
							$select_out .= '<option value="'. esc_attr( $key ) .'" '. ( $saved_val == $key ? 'selected="selected"' : '' ) .'>'. esc_html( $value ) .'</option>';
						}
					}
					$select_out .= '</select>';	
					$field_element = $select_out;
					$field_element .= '<p class="zwb-field-desc">'. $description .'</p>';
				break;
				
				case 'dragdrop':
					$meta = isset( $zozo_woo_addon_options[$field['id']] ) && !empty( $zozo_woo_addon_options[$field['id']] ) ? $zozo_woo_addon_options[$field['id']] : "";
					$zwb_fields = isset( $field['options'] ) && empty( $meta ) ? $field['options'] : $meta;
		
					if( !is_array( $zwb_fields ) ){
						$zwb_fields = stripslashes( $zwb_fields );
						$zwb_json = $meta = $zwb_fields;
					}else{
						$zwb_json = $meta = json_encode( $zwb_fields );
					}
					
					$part_array = json_decode( $zwb_json, true );
					$t_part_array = array();
					$f_part_array = array();
		
					foreach( $part_array as $key => $value ){
						$t_part_array[$key] = $value != '' ? dd_post_option_drag_drop_multi( $key, $value ) : '';
					}
		
					$field_element = '<div class="meta-drag-drop-multi-field">';
					foreach( $t_part_array as $key => $value ){
							$field_element .= '<h4>'. esc_html( $key ) .'</h4>';
							$field_element .= $value;
					}
					
					$zwb_fields = !is_array( $zwb_fields ) ? $zwb_fields : json_encode( $zwb_fields );
					
					$field_element .= '<input class="meta-drag-drop-multi-value" type="hidden" name="'. esc_attr( self::$opt_name ) .'['. esc_attr( $field['id'] ) .']" value="" data-params="'. htmlspecialchars( $zwb_fields, ENT_QUOTES, 'UTF-8' ) .'">';
					$field_element .= '</div>';
					$field_element .= '<p class="zwb-field-desc">'. $description .'</p>';
		
				break;
			}

			$required_attr = '';
			if( isset( $field['required'] ) ){
				$required = $field['required'];
				$required_attr .= isset( $required[0] ) ? ' data-required="'. $required[0] .'"' : '';
				$required_attr .= isset( $required[1] ) ? ' data-required-condition="'. $required[1] .'"' : '';
				$required_attr .= isset( $required[2] ) ? ' data-required-value="'. $required[2] .'"' : '';
			}
			
			$field_out .= '
			<div class="field-set"'. $required_attr .' id="'. esc_attr( $field['id'] ) .'">
				<div class="field-set-left">
					<h5>'. esc_html( $field['title'] ) .'</h5>
				</div><!-- .field-set-left -->
				<div class="field-set-right">
					'. $field_element .'
				</div><!-- .field-set-right -->
			</div><!-- .field-set -->';
			
		}
	
		return $field_out;
	}
	
}
