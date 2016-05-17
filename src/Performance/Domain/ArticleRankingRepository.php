<?php

namespace Performance\Domain;

interface ArticleRankingRepository
{
    public function initRank(Article $an_article);

    public function incrementRanking(Article $an_article);

    public function findGlobalRankingIds();

    public function findLoggedUserRankingIds($author_id);
}
