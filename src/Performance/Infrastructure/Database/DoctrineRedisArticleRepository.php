<?php

namespace Performance\Infrastructure\Database;

use Performance\Domain\Article;
use Performance\Domain\ArticleRepository;

final class DoctrineRedisArticleRepository implements ArticleRepository
{
    private $persistence_repository;
    private $cache_repository;

    public function __construct(
        DoctrineArticleRepository $a_doctrine_article_repository,
        RedisArticleRepository $a_redis_article_repository
    )
    {
        $this->persistence_repository = $a_doctrine_article_repository;
        $this->cache_repository       = $a_redis_article_repository;
    }

    public function save(Article $article)
    {
        $this->persistence_repository->save($article);
        $this->cache_repository->initRank($article->getId());
    }

    public function findOneById($article_id)
    {
        $this->cache_repository->incrementRanking($article_id);
        $article_in_cache = $this->cache_repository->findOneById($article_id);
        if ($article_in_cache)
        {
            return $article_in_cache;
        }

        $article_to_retrieve = $this->persistence_repository->findOneById($article_id);
        $this->cache_repository->save($article_to_retrieve);

        return $article_to_retrieve;
    }

    public function findAll()
    {
        $all_articles_in_cache = $this->cache_repository->findAll();
        if ($all_articles_in_cache)
        {
            return $all_articles_in_cache;
        }

        $array_of_articles_to_retrieve = $this->persistence_repository->findAll();
        $this->cache_repository->saveAll($array_of_articles_to_retrieve);

        return $array_of_articles_to_retrieve;
    }
}
