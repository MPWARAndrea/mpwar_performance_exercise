<?php

namespace Performance\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Performance\Domain\UseCase\ReadArticle;

class ArticleController
{
    /**
     * @var \Twig_Environment
     */
    private $template;
    /**
     * @var ReadArticle
     */
    private $useCase;

    private $request;

    public function __construct(
        \Twig_Environment $templating,
        ReadArticle $useCase,
        Request $request
    )
    {
        $this->template = $templating;
        $this->useCase  = $useCase;
        $this->request  = $request;
    }

    public function get($article_id)
    {
        $article = $this->useCase->execute($article_id);

        if (!$article)
        {
            throw new HttpException(404, "Article $article_id does not exist.");
        }
        $response = new Response($this->template->render('article.twig', ['article' => $article]));
        $response = $this->setCache($response);

        return $response;
    }

    private function setCache(Response $response)
    {
        $response->setPublic();
        $response->setMaxAge(120);
        $response->setSharedMaxAge(120);

        $response->setEtag(md5($response->getContent()));
        $response->isNotModified($this->request);

        return $response;
    }
}
