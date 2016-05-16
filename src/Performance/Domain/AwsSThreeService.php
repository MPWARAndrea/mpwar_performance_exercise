<?php
/**
 * Date: 07/05/16
 * Time: 19:00
 */

namespace Performance\Domain;


interface AwsSThreeService
{
    public function connectAws();
    public function createFile($file_name, $content);
    public function createImageFile($file_name, $content_image_path);
}