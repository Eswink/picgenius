<?php

namespace PicGenius\Utils;

class TextToImage
{
    private $__width; // 生成图片的宽度
    private $__height; // 生成图片的高度
    private $__fontPath; // 字体文件路径
    private $__textColor; // 文本颜色（RGB 数组）
    private $__backgroundColor; // 背景色（RGB 数组）
    private $__backgroundImage; // 背景图片路径
    private $__fontSize; // 字体大小

    /**
     * 构造函数。用于初始化对象的属性。
     *
     * @param integer $width 生成图片的宽度
     * @param integer $height 生成图片的高度
     * @param string|null $fontPath 字体文件路径
     * @param array $textColor 文本颜色（RGB 数组）
     * @param integer $fontSize 字体大小
     * @param array $backgroundColor 背景色（RGB 数组）
     * @param string|null $image_path 背景图片路径
     */
    public function __construct($width = 500, $height = 100, $fontPath = null, $textColor = [0, 0, 0], $fontSize = 12, $backgroundColor = [255, 255, 255], $image_path = null)
    {
        $this->__width           = $width;
        $this->__height          = $height;
        $this->__fontPath        = $fontPath ?? realpath(PicGenuis_DIR_PATH . 'inc' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'fonts' . DIRECTORY_SEPARATOR . 'LXGWWenKai-Regular.ttf');
        $this->__textColor       = $textColor;
        $this->__fontSize        = $fontSize;
        $this->__backgroundColor = $backgroundColor;
        $this->__backgroundImage = $image_path;
    }

    /**
     * 根据传入的文本绘制一张图片。
     *
     * @param string $text 要绘制的文本
     * @param string|null $filename 图片保存路径（可选）。如果未指定，则直接输出到前台。
     * @param bool $overwrite 是否覆盖同名文件（可选）。默认为 false。
     * @return string|null 如果指定了文件名，则返回文件名，否则返回 null。
     * @throws \Exception 如果文件已存在且未设置 $overwrite 为 true，则抛出异常。
     */
    public function generateImage($text, $filename = null, $overwrite = false)
    {
        // 如果指定了文件名，则检查文件是否存在
        if ($filename !== null) {
            if (file_exists($filename) && !$overwrite) {
                throw new \Exception("File already exists and overwrite is not allowed: " . $filename);
            }
        }

        // 创建一个新的图片
        if ($this->__backgroundImage !== null) {
            $img = imagecreatefromstring(file_get_contents($this->__backgroundImage));
        } else {
            $img     = imagecreatetruecolor($this->__width, $this->__height);
            $bgColor = imagecolorallocate($img, $this->__backgroundColor[0], $this->__backgroundColor[1], $this->__backgroundColor[2]);
            imagefill($img, 0, 0, $bgColor);
        }

        // 分配文本颜色
        $rgb_textColor = $this->picgenius_hexToRgb($this->__textColor);

        $textColor = imagecolorallocate($img, $rgb_textColor[0], $rgb_textColor[1], $rgb_textColor[2]);

        // 计算文本的总高度
        $textHeight = $this->__fontSize * 1.5;

        // 计算间距和每行文字的高度
        $lineSpacing = $textHeight / 2;
        // 换行处理
        if (mb_strlen($text) > 10) {
            $lines = []; // 存放每一行的文本

            // 分割文本为多行
            $start = 0;
            while ($start < mb_strlen($text)) {
                $line    = mb_substr($text, $start, 10);
                $lines[] = $line;
                $start += mb_strlen($line);
            }

            // 计算间距和每行文字的高度
            $lineSpacing = $textHeight / 2;

            // 计算文本的起始 Y 坐标，使其垂直居中
            $numLines = count($lines);
            $startY   = (imagesy($img) - ($numLines * $textHeight + ($numLines - 1) * $lineSpacing)) / 2;
            // 在图片上绘制每一行的文本
            foreach ($lines as $i => $line) {
                // 获取当前行的文本框信息
                $box = imagettfbbox($this->__fontSize, 0, $this->__fontPath, $line);

                // 计算当前行的 X 坐标，使其水平居中
                $x = (imagesx($img) - abs($box[2] - $box[0])) / 2;

                // 计算当前行的 Y 坐标
                $y = $startY + ($textHeight + $lineSpacing) * $i;

                // 在图片上绘制文本
                imagettftext($img, $this->__fontSize, 0, $x, $y, $textColor, $this->__fontPath, $line);
            }

        } else {
            // 获取文本框信息
            $textbox = imagettfbbox($this->__fontSize, 0, $this->__fontPath, $text);

            // 获取文本宽度
            $textWidth = abs($textbox[4] - $textbox[0]);

            // 计算文本横向居中位置
            $x = (imagesx($img) - $textWidth) / 2;

            // 计算文本纵向居中位置
            $y = (imagesy($img) + $this->__fontSize) / 2;

            // 在图片上绘制文本
            imagettftext($img, $this->__fontSize, 0, $x, $y, $textColor, $this->__fontPath, $text);
        }

        $addon_text = $this->__copyright;

        $addon_fontSize  = 20;
        $addon_textColor = imagecolorallocate($img, $rgb_textColor[0], $rgb_textColor[1], $rgb_textColor[2]);
        $addon_box       = imagettfbbox($addon_fontSize, 0, $this->__fontPath, $addon_text);
        $addon_x         = imagesx($img) - abs($addon_box[2] - $addon_box[0]) - 40;
        $addon_y         = imagesy($img) - $addon_fontSize - 10;
        imagettftext($img, $addon_fontSize, 0, $addon_x, $addon_y, $addon_textColor, $this->__fontPath, $addon_text);

        imagepng($img, $filename);

        // 释放内存
        imagedestroy($img);

        // 返回文件名或 null
        if ($filename !== null) {
            return $filename;
        }

        return null;
    }

    public function setWidth($width)
    {
        $this->__width = $width;
    }

    public function setHeight($height)
    {
        $this->__height = $height;
    }

    public function setFontPath($fontPath)
    {
        $this->__fontPath = $fontPath;
    }

    public function setTextColor($textColor)
    {
        $this->__textColor = $textColor;
    }

    public function setBackgroundColor($backgroundColor)
    {
        $this->__backgroundColor = $backgroundColor;
    }

    public function setBackgroundImage($backgroundImage)
    {
        $this->__backgroundImage = $backgroundImage;
    }

    public function setFontSize($fontSize)
    {
        $this->__fontSize = $fontSize;
    }

    public function setCopyright($copyright)
    {
        $this->__copyright = $copyright;
    }

    /**
     * 将 16 进制颜色值转换为 RGB 数组
     *
     * @param string $color 16 进制颜色值，例如 #ff0000
     * @return array 包含红、绿、蓝三个部分的 RGB 数组，例如 array(255, 0, 0)
     */
    public function picgenius_hexToRgb($color)
    {
        // 去掉首尾的 # 号
        $color = trim($color, '#');

        // 如果颜色字符串不是 3 或 6 个十六进制字符，则返回空数组
        if (!preg_match('/^(?:[0-9a-fA-F]{3}){1,2}$/', $color)) {
            return array();
        }

        // 如果颜色字符串是缩写形式，则将其扩展为完整形式
        if (strlen($color) == 3) {
            $color = str_repeat(substr($color, 0, 1), 2) .
            str_repeat(substr($color, 1, 1), 2) .
            str_repeat(substr($color, 2, 1), 2);
        }

        // 分离红、绿、蓝三个部分并转换为十进制数
        $red   = hexdec(substr($color, 0, 2));
        $green = hexdec(substr($color, 2, 2));
        $blue  = hexdec(substr($color, 4, 2));

        // 返回包含红、绿、蓝三个部分的 RGB 数组
        return array($red, $green, $blue);
    }
}
