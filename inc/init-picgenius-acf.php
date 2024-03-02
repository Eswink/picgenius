<?php

require_once 'classes/picgenius-acf.php';

// Initialize the ACF class
add_action('plugins_loaded', 'picgenius_init_acf');
function picgenius_init_acf()
{
    picgenius_load_textdomain();
    if (class_exists('picgenius_ACF')) {
        $picgenius_acf = new PicGenius_ACF();
        $picgenius_acf->init();
    }

}

add_action('admin_menu', 'picgenius_remove_acf_menu');
function picgenius_remove_acf_menu()
{
    // Remove ACF menu page
    remove_menu_page('edit.php?post_type=acf-field-group');
}

function picgenius_load_textdomain($domain = 'picgenius')
{
    // 获取当前语言环境
    $locale = get_locale();

    // 组合插件名称和语言环境，获取相应的 .mo 文件。
    $mofile = $domain . '-' . $locale . '.mo';

    // 按照 WordPress 规范将语言文件存放在 /languages 目录下
    $lang_dir = PicGenuis_DIR_PATH . '/languages/';

    load_textdomain($domain, $lang_dir . $mofile);

}
