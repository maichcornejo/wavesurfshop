<?php

  $shoes_store_elementor_theme_custom_setting_css = '';

	// Global Color
	$shoes_store_elementor_theme_color = get_theme_mod('shoes_store_elementor_theme_color', '#ff0101');

	$shoes_store_elementor_theme_custom_setting_css .=':root {';
		$shoes_store_elementor_theme_custom_setting_css .='--primary-theme-color: '.esc_attr($shoes_store_elementor_theme_color ).'!important;';
	$shoes_store_elementor_theme_custom_setting_css .='}';

	// Scroll to top alignment
	$shoes_store_elementor_scroll_alignment = get_theme_mod('shoes_store_elementor_scroll_alignment', 'right');

	if($shoes_store_elementor_scroll_alignment == 'right'){
		$shoes_store_elementor_theme_custom_setting_css .='.scroll-up{';
			$shoes_store_elementor_theme_custom_setting_css .='right: 30px;!important;';
			$shoes_store_elementor_theme_custom_setting_css .='left: auto;!important;';
		$shoes_store_elementor_theme_custom_setting_css .='}';
	}else if($shoes_store_elementor_scroll_alignment == 'center'){
		$shoes_store_elementor_theme_custom_setting_css .='.scroll-up{';
			$shoes_store_elementor_theme_custom_setting_css .='left: calc(50% - 10px) !important;';
		$shoes_store_elementor_theme_custom_setting_css .='}';
	}else if($shoes_store_elementor_scroll_alignment == 'left'){
		$shoes_store_elementor_theme_custom_setting_css .='.scroll-up{';
			$shoes_store_elementor_theme_custom_setting_css .='left: 30px;!important;';
			$shoes_store_elementor_theme_custom_setting_css .='right: auto;!important;';
		$shoes_store_elementor_theme_custom_setting_css .='}';
	}

	// Related Product

	$shoes_store_elementor_show_related_product = get_theme_mod('shoes_store_elementor_show_related_product', true );

	if($shoes_store_elementor_show_related_product != true){
		$shoes_store_elementor_theme_custom_setting_css .='.related.products{';
			$shoes_store_elementor_theme_custom_setting_css .='display: none;';
		$shoes_store_elementor_theme_custom_setting_css .='}';
	}		