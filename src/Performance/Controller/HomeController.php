<?php

namespace Performance\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Performance\Domain\UseCase\ListArticles;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class HomeController
{
    /**
     * @var \Twig_Environment
     */
    private $template;

    public function __construct(
        \Twig_Environment $templating,
        SessionInterface $session,
        ListArticles $useCase,
        Request $request
    )
    {
        $this->template = $templating;
        $this->session  = $session;
        $this->useCase  = $useCase;
        $this->request = $request;
    }

    public function get()
    {
        if (null !== $this->session->get('author_id'))
        {
            $author_id = $this->session->get('author_id');
            $content   = $this->useCase->execute($author_id);
        }
        else
        {
            $content = $this->useCase->execute();
        }

        $response = new Response($this->template->render('home.twig',
            ['articles'        => $content['articles'],
             'global_articles' => $content['global_articles'],
             'user_articles'   => $content['user_articles']
            ]
        )
        );
        $response = $this->setCache($response);

        return $response;
    }

    private function setCache(Response $response)
    {
        $response->setPrivate();
        $response->setMaxAge(120);

        $response->setEtag(md5($response->getContent()));
        $response->isNotModified($this->request);

        return $response;
    }
}
