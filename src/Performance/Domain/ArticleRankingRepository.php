<?php

namespace Performance\Domain;

interface ArticleRankingRepository
{
    public function saveNew($article_id);

    public function incrementRanking($article_id);

    public function findGlobalRankingIds();

    public function findLoggedUserRankingIds();
}
