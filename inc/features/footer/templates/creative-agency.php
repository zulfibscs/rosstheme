<?php
/**
 * Footer Template: Creative Agency
 * Bold 4-column layout for design studios, agencies, and creative professionals
 */
return array(
    'id' => 'creative-agency',
    'title' => 'Creative Agency',
    'description' => 'Bold dark design perfect for creative studios, agencies, and portfolio websites',
    'icon' => '🎨',
    'columns' => 4,
    'bg' => '#0c0c0d',
    'text' => '#f3f4f6',
    'accent' => '#E5C902',
    'social' => '#f3f4f6',
    'cols' => array(
        array('title' => 'Who We Are', 'html' => '<p class="muted">Design-led agency crafting beautiful experiences.</p>'),
        array('title' => 'Work', 'items' => array('Case Studies', 'Featured Projects', 'Clients')),
        array('title' => 'Services', 'items' => array('Branding', 'UX / UI', 'Product Design', 'Design Systems')),
        array('title' => 'Contact', 'items' => array('hello@agency.example', 'New York, USA')),
    ),
);
