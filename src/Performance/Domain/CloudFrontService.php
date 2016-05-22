<?php

namespace Performance\Domain;

interface CloudFrontService
{
    public function getFileUrl($image_file);
}