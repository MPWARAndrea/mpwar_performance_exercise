<?php

namespace Performance\Domain\UseCase;

use Performance\Domain\ArticleRankingRepository;
use Performance\Domain\ArticleRepository;
use Performance\Domain\CloudFront;

//use Aws\CloudFront\CloudFrontClient;

class ReadArticle
{
    /**
     * @var ArticleRepository
     */
    private $articleRepository;

    private $article_ranking_repository;

    /**
     * @var CloudFront
     */
    private $cloud_front;

    public function __construct(
        ArticleRepository $articleRepository,
        ArticleRankingRepository $an_article_ranking_repository,
        CloudFront $cloudFront
    )
    {
        $this->articleRepository            = $articleRepository;
        $this->article_ranking_repository   = $an_article_ranking_repository;
        $this->cloud_front                  = $cloudFront;
    }

    public function execute($article_id)
    {
        $article_to_read = $this->articleRepository->findOneById($article_id);
        if ($article_to_read) {
            $this->article_ranking_repository->incrementRanking($article_to_read);
        }

        $image_prompt = $this->cloud_front->getFileUrl($article_to_read->getAuthor()->getPicture());


//        $cloud_front = CloudFrontClient::factory(array(
//            'private_key'   => $ssh_path,
//            'key_pair_id'   => 'APKAJ3SNHGUQBWAAB3PQ',
//            'region'        => 'eu-west-1',
//            'version'       => 'latest'
//        ));
//
//        $cloudfront_files_url = "http://dphdmsup0cd6z.cloudfront.net";
//        $image = $article_to_read->getAuthor()->getPicture();
//        $image_prompt = "$cloudfront_files_url/$image";
//        $expires = time() + 3;
//
//
//        $custom_policy = <<<POLICY
//{
//  "Version": "2012-10-17",
//  "Statement": [
//    {
//      "Effect": "Allow",
//      "Action": "s3:*",
//      "Resource": "*"
//    }
//  ]
//}
//POLICY;
//
//        $signedCloudfront = $cloud_front->getSignedUrl(array(
//            'private_key'   => $ssh_path,
//            'key_pair_id'   => 'APKAJ3SNHGUQBWAAB3PQ',
//            'url'           => $image_prompt,
//            'expires'       => $expires,
//            'policy'        => $custom_policy
//        ));

        $all_article = ['image' => $image_prompt, 'article' => $article_to_read];
        return $all_article;
    }
}
