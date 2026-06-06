<?php 

add_action('customize_register', function($wp_customize) {
	$wp_customize->add_section('theme-variables', [
		'title' => __('Theme Variables', 'txtdomain'),
		'priority' => 25
	]);
 
	$wp_customize->add_setting('theme-main', ['default' => '#594c74']);
	$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'theme-main', [
		'section' => 'theme-variables',
		'label' => __('Main theme color', 'txtdomain'),
		'priority' => 10
	]));
 
	$wp_customize->add_setting('theme-secondary', ['default' => '#555']);
	$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'theme-secondary', [
		'section' => 'theme-variables',
		'label' => __('Secondary theme color', 'txtdomain'),
		'priority' => 20
	]));
 
	$wp_customize->add_setting('theme-text-size', ['default' => '12']);
	$wp_customize->add_control('theme-text-size', [
		'section' => 'theme-variables',
		'label' => __('Text size', 'txtdomain'),
		'type' => 'number',
		'priority' => 30,
		'input_attrs' => ['min' => 8, 'max' => 20, 'step' => 1]
	]);
});