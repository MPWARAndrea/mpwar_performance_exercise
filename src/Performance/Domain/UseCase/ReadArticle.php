<?php

namespace Performance\Domain\UseCase;

use Performance\Domain\ArticleRankingRepository;
use Performance\Domain\ArticleRepository;

class ReadArticle
{
    /**
     * @var ArticleRepository
     */
    private $articleRepository;

    private $article_ranking_repository;

    public function __construct(
        ArticleRepository $articleRepository,
        ArticleRankingRepository $an_article_ranking_repository
    )
    {
        $this->articleRepository          = $articleRepository;
        $this->article_ranking_repository = $an_article_ranking_repository;
    }

    public function execute($article_id)
    {
        $article_to_read = $this->articleRepository->findOneById($article_id);
        if ($article_to_read)
        {
            $this->article_ranking_repository->incrementRanking($article_to_read);
        }

        return $article_to_read;
    }
}
