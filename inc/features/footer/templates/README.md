This folder stores individual footer template definitions for the Ross theme admin Footer Options.

- Each PHP file should return an associative array describing the template, e.g. `template1.php`:

```
<?php
return array(
    'title' => 'Business Professional',
    'columns' => 4, // optional meta, inferred from cols if missing
    'bg' => '#f0f0f0',
    'text' => '#0b2140',
    'accent' => '#0b66a6',
    'cols' => array(
        array('title' => 'About Us', 'items' => array('Some description here', 'Another line')), // preferred representation
        array('title' => 'Services', 'items' => array('Service A', 'Service B')),
        array('title' => 'Contact', 'items' => array('Address', 'Phone', 'email@example.com'))
    ),
    'cta' => array('title' => 'Call to action', 'subtitle' => 'Short line', 'button_text' => 'Contact', 'button_url' => '#')
);
```

- File name becomes the template ID (without .php) and will be used where the theme previously used keys like `template1`.
 - File name becomes the template ID (without .php) and will be used where the theme previously used keys like `template1`.
- If the folder contains valid template files, the theme will load them automatically and use them as the default templates in the admin UI (unless overridden by saved options in `ross_theme_footer_templates`).
 - IMPORTANT: Folder-based templates now override stored templates. If a template file exists in `templates/` with the same id as a stored template, the file-based template will override the stored variant automatically and the theme will sync the stored configuration to match the file.
 - IMPORTANT: Folder-based templates now override stored templates. If a template file exists in `templates/` with the same id as a stored template, the file-based template will override the stored variant automatically and the theme will sync the stored configuration to match the file.
 - You can opt out of this forced override behavior by returning false from the filter `ross_theme_force_template_files_override` in your child theme or plugin:

```php
add_filter('ross_theme_force_template_files_override', '__return_false');
```
- When creating a new template, ensure the array follows the same structure so the preview and apply features work.
 - When creating a new template, prefer the structured format shown above. The admin preview and apply features support both the legacy string 'Title|content' format and this new structured array format.
