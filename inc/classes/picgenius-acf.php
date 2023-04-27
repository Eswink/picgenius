<?php

require_once 'acf/acf.php';

class PicGenius_ACF
{
    public function __construct()
    {
        // 在插件加载时，调用init方法
        add_action('plugins_loaded', array($this, 'init'));
    }

    public function init()
    {
        // Register ACF settings page
        if (function_exists('acf_add_options_page')) {
            acf_add_options_page(array(
                'page_title' => __('PicGenius Settings', 'picgenius'),
                'menu_title' => __('PicGenius', 'picgenius'),
                'menu_slug'  => 'picgenius-settings',
                'capability' => 'manage_options',
                'redirect'   => false,
            ));
        }

        // Add ACF field group
        if (function_exists('acf_add_local_field_group')) {
            acf_add_local_field_group(array(
                'key'             => 'picgenius_settings',
                'title'           => __('Image Settings', 'picgenius'),
                'fields'          => array(

                    // Add a description
                    array(
                        'key'               => 'picgenius_description',
                        'label'             => __('Description', 'picgenius'),
                        'name'              => 'picgenius_description',
                        'type'              => 'message',
                        'message'           => __('Configure the following settings to generate images with specified text and styles. After setting up the plug-in, you only need to click to publish the article, and the corresponding picture will be automatically generated for the article.', 'picgenius'),
                        'esc_html'          => 1,
                        'new_lines'         => 'wpautop',
                        'conditional_logic' => 0,
                    ),

                    // Add a switch to enable or disable PicGenius
                    array(
                        'key'           => 'picgenius_enabled',
                        'label'         => __('Enable PicGenius', 'picgenius'),
                        'name'          => 'picgenius_enabled',
                        'type'          => 'true_false',
                        'instructions'  => __('Enable this option to turn on PicGenius functionality.', 'picgenius'),
                        'default_value' => 1,
                        'ui'            => true,
                    ),
                    // Copyright
                    array(
                        'key'          => 'picgenius_copyright',
                        'label'        => __('Copyright', 'picgenius'),
                        'name'         => 'picgenius_copyright',
                        'type'         => 'text',
                        'instructions' => __('Enter the Copyright.', 'picgenius'),
                    ),
                    // Font selector field
                    array(
                        'key'          => 'picgenius_font',
                        'label'        => __('File Selector', 'picgenius'),
                        'name'         => 'picgenius_font',
                        'type'         => 'select',
                        'instructions' => __('Select a font for the image.', 'picgenius'),
                        'choices'      => $this->get_font_file_options(PicGenuis_DIR_PATH . 'inc/assets/fonts'),
                    ),
                    // Post type filter field
                    array(
                        'key'          => 'picgenius_post_type',
                        'label'        => __('Post Type', 'picgenius'),
                        'name'         => 'postType',
                        'type'         => 'select',
                        'instructions' => __('Select the post types to include in the image generation.', 'picgenius'),
                        'choices'      => get_post_types(array('public' => true)),
                        'multiple'     => true,
                        'ui'           => true,
                    ),
                    // Text color setting field
                    array(
                        'key'          => 'picgenius_text_color',
                        'label'        => __('Text Color', 'picgenius'),
                        'name'         => 'picgenius_text_color',
                        'type'         => 'color_picker',
                        'instructions' => __('Select the text color for the image.', 'picgenius'),
                    ),
                    // Background images field
                    array(
                        'key'          => 'picgenius_bg_images',
                        'label'        => __('Background Images', 'picgenius'),
                        'name'         => 'picgenius_bg_images',
                        'type'         => 'repeater',
                        'instructions' => __('Add background images', 'picgenius'),
                        'sub_fields'   => array(
                            array(
                                'key'          => 'picgenius_bg_image',
                                'label'        => __('Background Image', 'picgenius'),
                                'name'         => 'picgenius_bg_image',
                                'type'         => 'image',
                                'instructions' => __('Select an image for image generation.', 'picgenius'),
                                'preview_size' => 'thumbnail',
                            ),
                        ),
                    ),
                ),
                'location'        => array(
                    array(
                        array(
                            'param'    => 'options_page',
                            'operator' => '==',
                            'value'    => 'picgenius-settings',
                        ),
                    ),
                ),
                'menu_order'      => 0,
                'position'        => 'normal',
                'style'           => 'default',
                'label_placement' => 'top',
            ));
        }
    }

    /**
     * Get file options for ACF field.
     *
     * @param string $pattern The glob pattern.
     * @return array The file options.
     */
    public function get_font_file_options($dir)
    {
        // Get font files matching the pattern
        $files = glob("$dir/*.{ttf,otf,woff}", GLOB_BRACE);

        // Convert files to ACF field options
        $options = array();
        foreach ($files as $file) {
            $options[$file] = basename($file);
        }

        return $options;
    }
}
