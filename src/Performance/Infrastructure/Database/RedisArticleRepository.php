<?php

namespace Performance\Infrastructure\Database;

use Performance\Domain\ArticleRankingRepository;
use Predis\Client;

final class RedisArticleRepository implements ArticleRankingRepository
{
    const ENTITY_NAME               = 'article';
    const GLOBAL_NAME_SET           = 'articles';
    const NUMBER_OF_ARTICLES_OFFSET = 5;

    private $redis_client;

    public function __construct(Client $a_redis_client)
    {
        $this->redis_client = $a_redis_client;
    }

    public function saveNew($article_id)
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
}
