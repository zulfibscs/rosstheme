<?php
/**
 * Footer Template: E-commerce
 * Modern 4-column layout optimized for online stores and retail businesses
 */
return array(
    'id' => 'ecommerce',
    'title' => 'E-commerce',
    'description' => 'Modern layout optimized for online stores with product categories and customer service links',
    'icon' => '🛒',
    'columns' => 4,
    'bg' => '#ffffff',
    'text' => '#0b2140',
    'accent' => '#b02a2a',
    'social' => '#0b2140',
    'cols' => array(
        array('title' => 'Shop', 'items' => array('All Products', 'New Arrivals', 'Sale', 'Gift Cards')),
        array('title' => 'Customer Service', 'items' => array('Shipping', 'Returns & Exchanges', 'Order Tracking', 'FAQ')),
        array('title' => 'Company', 'items' => array('About', 'Careers', 'Press', 'Affiliates')),
        array('title' => 'Subscribe', 'html' => '<p>Sign up for exclusive offers and product updates.</p>')
    ),
);
