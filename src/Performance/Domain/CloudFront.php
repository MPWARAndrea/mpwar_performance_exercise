<?php

namespace Performance\Domain;

use Aws\CloudFront\CloudFrontClient;

class CloudFront implements CloudFrontService
{
    const CLOUD_FRONT_FILES_URL = 'http://dphdmsup0cd6z.cloudfront.net';
    const EXPIRES_TIME          = 300;

    private $cloud_front;
    private $custom_police;
    private $signed_cloud_front;
    private $ssh_path;

    public function __construct($ssh_path)
    {
        $this->ssh_path = $ssh_path;
        $this->cloud_front = CloudFrontClient::factory(array(
            'private_key'   => $ssh_path,
            'key_pair_id'   => 'APKAJ3SNHGUQBWAAB3PQ',
            'region'        => 'eu-west-1',
            'version'       => 'latest'
        ));

        $this->custom_police = <<<POLICY
{
  "Version": "2012-10-17",
  "Statement": [
    {
      "Effect": "Allow",
      "Action": "s3:*",
      "Resource": "*"
    }
  ]
}
POLICY;
    }

    public function getFileUrl($image_file)
    {
        $image_prompt = self::CLOUD_FRONT_FILES_URL . "/$image_file";
        $this->signed_cloud_front = $this->cloud_front->getSignedUrl(array(
            'private_key'   => $this->ssh_path,
            'key_pair_id'   => 'APKAJ3SNHGUQBWAAB3PQ',
            'url'           => $image_prompt,
            'expires'       => self::EXPIRES_TIME,
            'policy'        => $this->custom_police
        ));

        return $image_prompt;
    }

}