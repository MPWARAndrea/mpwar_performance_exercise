<?php

namespace Performance;


use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Symfony\Component\HttpFoundation\Request;

class DomainServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['services.awsSThree'] = function () use ($app) {
                return new \Performance\Domain\AwsSThree();
        };

        $app['redis_article_repository'] = function () use ($app){
            return new \Performance\Infrastructure\Database\RedisArticleRepository($app['predis']['client']);
        };

        $app['article_ranking_repository'] = function () use ($app){
            return new \Performance\Infrastructure\Database\RedisArticleRankingRepository($app['predis']['client']);
        };

        $app['articles_repository_with_cache'] = function() use($app){
            return new \Performance\Infrastructure\Database\DoctrineRedisArticleRepository($app['orm.em']->getRepository('Performance\Domain\Article'), $app['redis_article_repository']);
        };

        $app['useCases.signUp'] = function () use ($app) {
            return new \Performance\Domain\UseCase\SignUp($app['orm.em']->getRepository('Performance\Domain\Author'), $app['services.awsSThree']);
        };

        $app['useCases.login'] = function () use ($app) {
            return new \Performance\Domain\UseCase\Login($app['orm.em']->getRepository('Performance\Domain\Author'), $app['session']);
        };

        $app['useCases.writeArticle'] = function () use ($app) {
            return new \Performance\Domain\UseCase\WriteArticle($app['orm.em']->getRepository('Performance\Domain\Article'), $app['orm.em']->getRepository('Performance\Domain\Author'), $app['session'], $app['article_ranking_repository']);
        };

        $app['useCases.editArticle'] = function () use ($app) {
            return new \Performance\Domain\UseCase\EditArticle($app['orm.em']->getRepository('Performance\Domain\Article'), $app['orm.em']->getRepository('Performance\Domain\Author'), $app['session']);
        };

        $app['useCases.readArticle'] = function () use ($app) {
            return new \Performance\Domain\UseCase\ReadArticle($app['articles_repository_with_cache'], $app['article_ranking_repository']);
        };

        $app['useCases.listArticles'] = function () use ($app) {
            return new \Performance\Domain\UseCase\ListArticles($app['articles_repository_with_cache'], $app['article_ranking_repository']);
        };

        /** Controllers **/

        $app['controllers.readArticle'] = function () use ($app) {
            return new \Performance\Controller\ArticleController($app['twig'], $app['useCases.readArticle'], $app['request_stack']->getCurrentRequest());
        };

        $app['controllers.writeArticle'] = function () use ($app) {
            return new \Performance\Controller\WriteArticleController($app['twig'], $app['url_generator'], $app['useCases.writeArticle'], $app['session']);
        };

        $app['controllers.editArticle'] = function () use ($app) {
            return new \Performance\Controller\EditArticleController($app['twig'], $app['url_generator'], $app['useCases.editArticle'], $app['useCases.readArticle'], $app['session']);
        };

        $app['controllers.login'] = function () use ($app) {
            return new \Performance\Controller\LoginController($app['twig'], $app['url_generator'], $app['useCases.login'], $app['session']);
        };

        $app['controllers.signUp'] = function () use ($app) {
            return new \Performance\Controller\RegisterController($app['twig'], $app['url_generator'], $app['useCases.signUp']);
        };

        $app['controllers.home'] = function () use ($app) {
            return new \Performance\Controller\HomeController($app['twig'], $app['session'], $app['useCases.listArticles']);
        };
    }
}
