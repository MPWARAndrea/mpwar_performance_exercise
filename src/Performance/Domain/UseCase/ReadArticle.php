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
    /**
     * @var ArticleRankingRepository
     */
    private $article_raking_repository;

    public function __construct(
        ArticleRepository $articleRepository,
        ArticleRankingRepository $an_article_raking_repository
    )
    {
        $this->articleRepository         = $articleRepository;
        $this->article_raking_repository = $an_article_raking_repository;
    }

    public function execute($article_id)
    {
        $this->article_raking_repository->incrementRanking($article_id);

        return $this->articleRepository->findOneById($article_id);
    }
}
