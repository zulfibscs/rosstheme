<?php
/**
 * Footer Template: Business Professional
 * 4-column layout ideal for professional services, consulting firms, B2B companies
 */
return array(
    'id' => 'business-professional',
    'title' => 'Business Professional',
    'description' => 'Clean 4-column layout ideal for professional services and B2B companies',
    'icon' => '💼',
    'columns' => 4,
    'bg' => '#f8f9fb',
    'text' => '#0b2140',
    'accent' => '#0b66a6',
    'social' => '#0b66a6',
    'cols' => array(
        array(
            'title' => 'About Us',
            'items' => array(
                'We help businesses grow with finance, consulting & digital services.',
                'Trusted by small & mid-size companies.'
            )
        ),
        array(
            'title' => 'Our Services',
            'items' => array('Auditing', 'Tax & Advisory', 'Digital Transformation', 'Strategy & Planning')
        ),
        array(
            'title' => 'Insights & Resources',
            'items' => array('Blog', 'Case Studies', 'Whitepapers', 'FAQs')
        ),
        array(
            'title' => 'Contact',
            'items' => array('123 Business Ave, City', '(555) 123-4567', 'info@businesspro.com'),
            'social' => array('LinkedIn', 'Facebook')
        ),
    ),
    'cta' => array(
        'title' => 'Work with us',
        'subtitle' => 'Book a free consultation today.',
        'button_text' => 'Get Started',
        'button_url' => '#'
    )
);
