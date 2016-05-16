<?php
/**
 * Date: 07/05/16
 * Time: 19:01
 */

namespace Performance\Domain;

use Aws\S3\S3Client;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\Config;
use League\Flysystem\Filesystem;

class AwsSThree implements AwsSThreeService
{
    const AWS_KEY           = 'AKIAJ4G6D4XXQHHNSTEA';
    const AWS_SECRET        = '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';
    const AWS_STHREE_USER   = 'requirements3region';

    private $filesystem;

    public function connectAws()
    {
        $client = new S3Client([
            'credentials' => [
                'key'    => self::AWS_KEY,
                'secret' => self::AWS_SECRET,
            ],
            'region' => 'eu-west-1',
            'version' => 'latest',
        ]);

        $aws3adapter = new AwsS3Adapter($client, self::AWS_STHREE_USER, 'optional-prefix');

        $this->filesystem = new Filesystem($aws3adapter, new Config([]));

    }

    public function fileExists($fileToCheck)
    {
        return $this->filesystem->has($fileToCheck);
    }

    public function deleteFile($fileToDelete)
    {
        $this->filesystem->delete($fileToDelete);
    }

    public function createFile($file_name, $content)
    {
        $this->filesystem->write($file_name, $content);
    }

    public function createImageFile($new_file_name, $file_entire_path)
    {
        $this->filesystem->writeStream($new_file_name, fopen($file_entire_path, 'r'));
//        $filesystem->write('example.png', file_get_contents('local_path/to/image.png'));
    }

}