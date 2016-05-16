<?php

namespace Performance\Infrastructure\Database;

use Performance\Domain\Article;
use Performance\Domain\ArticleRankingRepository;
use Performance\Domain\ArticleRepository;
use Predis\Client;

final class RedisArticleRepository implements ArticleRankingRepository, ArticleRepository
{
    const NUMBER_OF_ARTICLES_OFFSET = 5;

    const CACHE_PREFIX    = 'query_';
    const LIST_CACHE_NAME = 'articles_list';
    const ENTITY_NAME     = 'article';
    const GLOBAL_NAME_SET = 'articles_ranking';

    const ALL_ARTICLES_CACHE_TTL       = 60;
    const INDIVIDUAL_ARTICLE_CACHE_TTL = 300;

    private $redis_client;

    public function __construct(Client $a_redis_client)
    {
        $this->redis_client = $a_redis_client;
    }

    public function initRank($article_id)
    {
        $this->redis_client->zadd(self::GLOBAL_NAME_SET, [self::ENTITY_NAME . ':' . $article_id => 0]);
    }

    public function incrementRanking($article_id)
    {
        $this->redis_client->zincrby(self::GLOBAL_NAME_SET, 1, self::ENTITY_NAME . ':' . $article_id);
    }

    public function findGlobalRankingIds()
    {
        return $this->redis_client->zrevrange(self::GLOBAL_NAME_SET, 0, self::NUMBER_OF_ARTICLES_OFFSET);
    }

    public function findLoggedUserRankingIds()
    {

    }

    public function save(Article $article)
    {
        $article_id         = $article->getId();
        $serialized_article = serialize($article);
        $this->redis_client->set(self::CACHE_PREFIX . self::ENTITY_NAME . ':' . $article_id,
            $serialized_article,
            'ex',
            self::INDIVIDUAL_ARTICLE_CACHE_TTL
        );
    }

    public function saveAll(array $articles)
    {
        $all_serialized_articles = serialize($articles);
        $this->redis_client->set(self::CACHE_PREFIX . self::LIST_CACHE_NAME,
            $all_serialized_articles,
            'ex',
            self::ALL_ARTICLES_CACHE_TTL
        );
    }

    public function findOneById($article_id)
    {
        if ($this->redis_client->exists(self::CACHE_PREFIX . self::ENTITY_NAME . ':' . $article_id))
        {
            return unserialize($this->redis_client->get(self::CACHE_PREFIX . self::ENTITY_NAME . ':' . $article_id));
        }

        return false;
    }

    public function findAll()
    {
        if ($this->redis_client->exists(self::CACHE_PREFIX . self::LIST_CACHE_NAME))
        {
            return unserialize($this->redis_client->get(self::CACHE_PREFIX . self::LIST_CACHE_NAME));
        }

        return false;
    }
}
