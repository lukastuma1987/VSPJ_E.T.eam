<?php


namespace EditorialBundle\Controller;

use EditorialBundle\Entity\Article;
use EditorialBundle\Entity\ArticleVersion;
use EditorialBundle\Entity\Magazine;
use EditorialBundle\Factory\ResponseFactory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/redakce/stahnout")
 */
class DownloadController extends Controller
{
    /**
     * @Route("/cislo-casopisu/{id}", name="download_magazine", methods={"GET"})
     * @Security("has_role('ROLE_CHIEF_EDITOR')")
     */
    public function downloadMagazineAction(Magazine $magazine, ResponseFactory $responseFactory)
    {
        if (!$magazine->getSuffix()) {
            throw $this->createNotFoundException('Číslo časopisu není nahráno');
        }

        return $responseFactory->createMagazineFileResponse($magazine);
    }

    /**
     * @Route("/clanek/{id}", name="download_article", methods={"GET"})
     * @Security("is_granted('DOWNLOAD', article)", message="Nemáte oprávnění na stažení článku")
     */
    public function downloadArticleAction(Article $article, ResponseFactory $responseFactory)
    {
        if (!$article->getLastVersion()) {
            throw $this->createNotFoundException('Článek neobsahuje verzi');
        }

        return $responseFactory->createArticleVersionFileResponse($article->getLastVersion());
    }

    /**
     * @Route("/clanek/{articleId}/verze/{versionId}", name="download_article_version", methods={"GET"})
     * @Entity("version", expr="repository.findByArticleAndVersionId(articleId, versionId)")
     * @Security("is_granted('DOWNLOAD', version)", message="Nemáte oprávnění na stažení verze článku")
     */
    public function downloadArticleVersionAction(ArticleVersion $version, ResponseFactory $responseFactory)
    {
        return $responseFactory->createArticleVersionFileResponse($version);
    }
}
