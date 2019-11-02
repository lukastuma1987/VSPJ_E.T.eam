<?php

namespace EditorialBundle\Factory;

use EditorialBundle\Entity\Article;
use Symfony\Component\HttpFoundation\Response;

abstract class ResponseFactory
{
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
