<?php

require_once 'classes/TextToImage.php';

use PicGenius\Utils\TextToImage;

function picgenius_update_post_meta($post_id)
{
    if (get_post_status($post_id) != 'publish') {
        return;
    }
    // 只监听文章和页面类型
    $post = get_post($post_id);
    if (!in_array($post->post_type, array('post', 'page'))) {
        return;
    }

    $post_title = $post->post_title;
    $filename   = PicGenuis_DIR_PATH . 'inc/media/' . time() . rand(1000, 9999) . '.png';

    $generated_image_url = generate_picgenius_image($post_title, $filename);

    // 保存图片地址
    if ($generated_image_url) {
        update_post_meta($post_id, '_picgenius_generated_image', $generated_image_url);
    }
}

function picgenius_display_generated_image($content)
{
    global $post;
    if (get_post_status($post->ID) != 'publish') {
        return $content;
    }
    // 只监听文章和页面类型
    if (!in_array($post->post_type, array('post', 'page'))) {
        return $content;
    }

    // 获取之前保存的生成图片地址
    $generated_image_url = get_post_meta($post->ID, '_picgenius_generated_image', true);
    if (!$generated_image_url) {
        return $content;
    }
    $generated_image_html = '<div><img src="' . esc_attr($generated_image_url) . '"  width="960" height="640" style="margin-bottom:20px"/></div>';
    return $generated_image_html . $content;
}

// 在发布或更新文章时插入图片
add_action('save_post', 'picgenius_update_post_meta', 10, 1);

// 在文章的开头显示生成的图片
add_filter('the_content', 'picgenius_display_generated_image', 1);

function generate_picgenius_image($post_title, $filename)
{

    // 初始化 TextToImage 类

    $picgenius_settings = get_fields('option');

    $picgenius_enabled = $picgenius_settings['picgenius_enabled'];

    if (!$picgenius_enabled) {
        return;
    }

    $picgenius_text_color     = $picgenius_settings['picgenius_text_color'];
    $picgenius_font_path      = $picgenius_settings['picgenius_font_path'];
    $picgenius_text_font_size = $picgenius_settings['picgenius_font_size'];
    $picgenius_copyright      = $picgenius_settings['picgenius_copyright'];

    $picgenius_font = $picgenius_settings['picgenius_font'];

    $picgenius_images_file_array = array();
    foreach ($picgenius_settings['picgenius_bg_images'] as $bg_image) {
        $info            = $bg_image['picgenius_bg_image'];
        $image_file_path = get_attached_file($info['ID']);
        array_push($picgenius_images_file_array, $image_file_path);
    }

    $serial_number = rand(0, count($picgenius_images_file_array) - 1);
    // 随机图片

    $text_to_image = new TextToImage(null, null, $picgenius_font, $picgenius_text_color, 60, null, $picgenius_images_file_array[$serial_number]);

    $text_to_image->setCopyright($picgenius_copyright);
    // 生成图片并保存到本地
    if ($text_to_image->generateImage($post_title, $filename)) {
        // 获取刚刚生成的图片的 URL
        $generated_image_url = str_replace(PicGenuis_DIR_PATH, PicGenuis_URL_PATH, $filename);
        return $generated_image_url;
    }

    return '';
}

// Enable webp upload
function enable_webp_upload($result, $path, $file, $extension, $mime_type)
{
    if ($extension === 'webp' && $mime_type === 'image/webp') {
        $result['ext']  = 'jpg';
        $result['type'] = 'image/jpeg';
    }
    return $result;
}
add_filter('wp_check_filetype_and_ext', 'enable_webp_upload', 10, 5);
