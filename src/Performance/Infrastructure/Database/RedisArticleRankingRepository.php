<?php

namespace Performance\Infrastructure\Database;

use Performance\Domain\Article;
use Performance\Domain\ArticleRankingRepository;
use Predis\Client;

final class RedisArticleRankingRepository implements ArticleRankingRepository
{
    const NUMBER_OF_ARTICLES_OFFSET = 5;
    const GLOBAL_NAME_SET           = 'global_articles_ranking';
    const AUTHOR_NAME_SET           = 'author_articles_ranking';
    const ENTITY_NAME               = 'article';

    private $redis_client;

    public function __construct(Client $a_redis_client)
    {
        $this->redis_client = $a_redis_client;
    }

    public function initRank(Article $an_article)
    {
        $article_id = $an_article->getId();
        $author_id  = $an_article->getAuthor()->getId();
        $this->redis_client->zadd(self::GLOBAL_NAME_SET, [self::ENTITY_NAME . ':' . $article_id => 0]);
        $this->redis_client->zadd(self::AUTHOR_NAME_SET . ':' . $author_id,
            [self::ENTITY_NAME . ':' . $article_id => 0]
        );
    }

    public function incrementRanking(Article $an_article)
    {
        $article_id = $an_article->getId();
        $author_id  = $an_article->getAuthor()->getId();
        $this->redis_client->zincrby(self::GLOBAL_NAME_SET, 1, self::ENTITY_NAME . ':' . $article_id);
        $this->redis_client->zincrby(self::AUTHOR_NAME_SET . ':' . $author_id,
            1,
            self::ENTITY_NAME . ':' . $article_id
        );
    }

    public function findGlobalRankingIds()
    {
        return $this->redis_client->zrevrange(self::GLOBAL_NAME_SET, 0, self::NUMBER_OF_ARTICLES_OFFSET);
    }

    public function findLoggedUserRankingIds($author_id)
    {
        return $this->redis_client->zrevrange(self::AUTHOR_NAME_SET . ':' . $author_id,
            0,
            self::NUMBER_OF_ARTICLES_OFFSET
        );
    }
}
