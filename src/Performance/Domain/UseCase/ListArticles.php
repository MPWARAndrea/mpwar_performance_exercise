<?php

namespace Performance\Domain\UseCase;

use Performance\Domain\ArticleRankingRepository;
use Performance\Domain\ArticleRepository;

class ListArticles
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

    public function execute($author_id = null)
    {
        $content['articles']        = $this->articleRepository->findAll();
        $content['global_articles'] = $this->retrieveGlobalArticleList();
        if (null !== $author_id)
        {
            $content['user_articles'] = $this->retrieveUserArticleList($author_id);
        }
        else
        {
            $content['user_articles'] = [];
        }

        return $content;
    }

    private function retrieveGlobalArticleList()
    {
        $all_articles_identifiers_to_parse = $this->article_ranking_repository->findGlobalRankingIds();
        $articles_to_retrieve              = [];
        foreach ($all_articles_identifiers_to_parse as $article_identifier_to_parse)
        {
            $article_id             = substr($article_identifier_to_parse,
                strpos($article_identifier_to_parse, ":") + 1
            );
            $articles_to_retrieve[] = $this->articleRepository->findOneById($article_id);
        }

        return $articles_to_retrieve;
    }

    private function retrieveUserArticleList($author_id)
    {
        $all_articles_identifiers_to_parse = $this->article_ranking_repository->findLoggedUserRankingIds($author_id);
        $articles_to_retrieve              = [];
        foreach ($all_articles_identifiers_to_parse as $article_identifier_to_parse)
        {
            $article_id             = substr($article_identifier_to_parse,
                strpos($article_identifier_to_parse, ":") + 1
            );
            $articles_to_retrieve[] = $this->articleRepository->findOneById($article_id);
        }

        return $articles_to_retrieve;
    }
}
