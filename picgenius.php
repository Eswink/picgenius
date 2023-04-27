<?php

/**
 * @package       PicGenius
 * @author        Eswlnk
 *
 * @wordpress-plugin
 * Plugin Name: PicGenius
 * Plugin URI: https://blog.eswlnk.com
 * Description: "PicGenius" is an AI-powered image generation plugin. By inputting text content, it can automatically create high-quality and highly customized images, helping users quickly generate visual content that meets their needs. No design or programming skills are required to easily create stunning visual works.
 * Author: Eswlnk(HotSpot AI)
 * Author URI: https://blog.eswlnk.com
 * Version: 1.0
 * License: GPL2+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: picgenius
 * Domain Path: /languages
 *
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!defined('PicGenuis_URL_PATH')) {
    define('PicGenuis_URL_PATH', plugin_dir_url(__FILE__));
}
if (!defined('PicGenuis_DIR_PATH')) {
    define('PicGenuis_DIR_PATH', plugin_dir_path(__FILE__));
}

if (!defined('PicGenuis_SOURCE')) {
    define('PicGenuis_SOURCE', plugin_basename(__FILE__));
}

// 初始化
require_once PicGenuis_DIR_PATH . 'inc\init-picgenius-acf.php';

require_once PicGenuis_DIR_PATH . 'inc\functions-picgenius.php';
