<?php

namespace Performance\Domain\UseCase;

use Performance\Domain\ArticleRankingRepository;
use Performance\Domain\ArticleRepository;
use Performance\Domain\Article;
use Performance\Domain\AuthorRepository;
use Performance\Domain\Exception\Forbidden;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class WriteArticle
{
    /**
     * @var ArticleRepository
     */
    private $articleRepository;

    /**
     * @var AuthorRepository
     */
    private $authorRepository;

    /**
     * @var SessionInterface
     */
    private $session;

    private $article_ranking_repository;

    public function __construct(
        ArticleRepository $articleRepository,
        AuthorRepository $authorRepository,
        SessionInterface $session,
        ArticleRankingRepository $an_article_ranking_repository
    )
    {
        $this->articleRepository         = $articleRepository;
        $this->authorRepository          = $authorRepository;
        $this->session                   = $session;
        $this->article_ranking_repository = $an_article_ranking_repository;
    }

    public function execute(
        $title,
        $content
    )
    {
        $author  = $this->getAuthor();
        $article = Article::write($title, $content, $author);
        $this->articleRepository->save($article);
        $this->article_ranking_repository->initRank($article);

        return $article;
    }

    private function getAuthor()
    {
        $author_id = $this->session->get('author_id');

        if (!$author_id)
        {
            throw new Forbidden('You must be logged in in order to write an article');
        }

        return $this->authorRepository->findOneById($author_id);
    }
}
