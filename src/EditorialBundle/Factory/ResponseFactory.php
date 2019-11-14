<?php

namespace EditorialBundle\Factory;

use EditorialBundle\Entity\Article;
use EditorialBundle\Entity\ArticleVersion;
use EditorialBundle\Entity\Magazine;
use EditorialBundle\Util\FileNameUtil;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class ResponseFactory
{
    /** @var string */
    private $articleDirectory;

    /** @var string */
    private $magazineDirectory;

    public function __construct($articleDirectory, $magazineDirectory)
    {
        $this->articleDirectory = $articleDirectory;
        $this->magazineDirectory = $magazineDirectory;
    }

    public function createMagazineFileResponse(Magazine $magazine)
    {
        $fileName = FileNameUtil::getMagazineDisplayFileName($magazine);
        $filePath = $this->magazineDirectory . '/' . FileNameUtil::getMagazineFileName($magazine);

        return new BinaryFileResponse($filePath, 200, [
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }

    public function createArticleVersionFileResponse(ArticleVersion $version)
    {
        $fileName = FileNameUtil::getArticleVersionDisplayFileName($version);
        $filePath = $this->articleDirectory . '/' . FileNameUtil::getArticleVersionFileName($version);

        return new BinaryFileResponse($filePath, 200, [
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }
}
