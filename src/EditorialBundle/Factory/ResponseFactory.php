<?php

namespace EditorialBundle\Factory;

use EditorialBundle\Entity\Article;
use EditorialBundle\Entity\Magazine;
use Symfony\Component\HttpFoundation\Response;

abstract class ResponseFactory
{
    public static function createMagazineFileResponse(Magazine $magazine)
    {
        $file = $magazine->getFile();
        $fileName = sprintf('rocnik%d-cislo%d.%s', $magazine->getYear(), $magazine->getNumber(), $magazine->getSuffix());

        return new Response(stream_get_contents($file), 200, [
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }

    public static function createArticleFileResponse(Article $article)
    {
        $version = $article->getLastVersion();
        $file = $version->getFile();

        $created = $version->getCreated();
        $created = $created ? $created->format('Ymd') : '00000000';

        $fileName = sprintf('%s-%s.%s', $article->getName(), $created, $version->getSuffix());

        return new Response(stream_get_contents($file), 200, [
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }
}
