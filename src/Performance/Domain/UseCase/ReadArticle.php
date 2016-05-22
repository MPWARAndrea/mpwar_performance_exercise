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
        $all_article = ['image' => $image_prompt, 'article' => $article_to_read];
        return $all_article;
    }
}
