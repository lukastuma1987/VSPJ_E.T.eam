<?php

namespace EditorialBundle\Util;

use EditorialBundle\Entity\ArticleVersion;
use EditorialBundle\Entity\Magazine;

class FileNameUtil
{
    /**
     * @param ArticleVersion $version
     * @return string
     */
    public static function getArticleVersionFileName(ArticleVersion $version)
    {
        $article = $version->getArticle();

        return sprintf('%s-%s', $article->getId(), $version->getId());
    }

    /**
     * @param ArticleVersion $version
     * @return string
     */
    public static function getArticleVersionDisplayFileName(ArticleVersion $version)
    {
        $article = $version->getArticle();

        $created = $version->getCreated();
        $created = $created ? $created->format('Ymd') : '00000000';

        return sprintf('%s-%s.%s', $article->getName(), $created, $version->getSuffix());
    }

    /**
     * @param Magazine $magazine
     * @return string
     */
    public static function getMagazineFileName(Magazine $magazine)
    {
        return (string)$magazine->getId();
    }

    /**
     * @param Magazine $magazine
     * @return string
     */
    public static function getMagazineDisplayFileName(Magazine $magazine)
    {
        $year = $magazine->getYear();
        $number = $magazine->getNumber();
        $suffix = $magazine->getSuffix();

        return sprintf('rocnik%d-cislo%d.%s', $year, $number, $suffix);
    }
}
