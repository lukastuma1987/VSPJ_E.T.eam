<?php


namespace EditorialBundle\Controller;

use EditorialBundle\Entity\Article;
use EditorialBundle\Entity\ArticleAuthor;
use EditorialBundle\Entity\ArticleVersion;
use EditorialBundle\Entity\User;
use EditorialBundle\Enum\ArticleStatus;
use EditorialBundle\Factory\EmailFactory;
use EditorialBundle\Form\ArticleType;
use EditorialBundle\Util\FileNameUtil;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/redakce/autor")
 * @Security("has_role('ROLE_AUTHOR')")
 */
class AuthorController extends Controller
{
    /**
     * @Route("/novy-clanek", name="author_article_create", methods={"GET", "POST"})
     */
    public function createArticleAction(Request $request, EmailFactory $emailFactory)
    {
        /** @var User $user */
        $user = $this->getUser();
        $article = new Article();
        $article->addAuthor(new ArticleAuthor($user));
        $article->setOwner($user);

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Article $article */
            $article = $form->getData();
            /** @var UploadedFile $file */
            $file = $form['file']->getData();;

            if ($file && $file->isValid()) {
                $version = new ArticleVersion();
                $version->setSuffix($file->getClientOriginalExtension());

                $article->addVersion($version);

                $em = $this->getDoctrine()->getManager();
                $em->persist($article);
                $em->flush();

                try {
                    $fileName = FileNameUtil::getArticleVersionFileName($version);
                    $file->move($this->getParameter('article_directory'), $fileName);
                } catch (FileException $exception) {
                    $this->addFlash('danger', $exception->getMessage());
                    return $this->redirectToRoute('editorial_dashboard');
                }

                $emailFactory->sendNewArticleNotification($article);

                $this->addFlash('success', 'Článek byl úspěšně vytvořen.');

                return $this->redirectToRoute('author_articles_list');
            }

            $this->addFlash('danger', $file->getErrorMessage());
        }

        return $this->render('@Editorial/Author/Article/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/vase-clanky", name="author_articles_list", methods={"GET"})
     */
    public function authorArticlesList()
    {
        /** @var User $author */
        $author = $this->getUser();

        return $this->render('@Editorial/Author/Article/list.html.twig', [
            'articles' => $author->getAuthorArticles(),
        ]);
    }

    /**
     * @Route("/clanek-{id}/upravit", name="author_article_update", methods={"GET", "POST"})
     * @Security("is_granted('UPDATE', article)", message="Nemáte oprávnění na úpravu článku")
     */
    public function editArticleAction(Request $request, Article $article, EmailFactory $emailFactory)
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Article $article */
            $article = $form->getData();
            /** @var UploadedFile $file */
            $file = $form['file']->getData();;

            if ($file && $file->isValid()) {
                $version = new ArticleVersion();
                $version->setSuffix($file->getClientOriginalExtension());

                $article->addVersion($version);
                $article->setStatus(ArticleStatus::STATUS_NEW_VERSION);

                $em = $this->getDoctrine()->getManager();
                $em->flush();

                try {
                    $fileName = FileNameUtil::getArticleVersionFileName($version);
                    $file->move($this->getParameter('article_directory'), $fileName);
                } catch (FileException $exception) {
                    $this->addFlash('danger', $exception->getMessage());
                    return $this->redirectToRoute('editorial_dashboard');
                }

                $emailFactory->sendNewArticleVersionNotification($article);

                $this->addFlash('success', 'Článek byl úspěšně upraven.');

                return $this->redirectToRoute('author_articles_list');
            }

            $this->addFlash('danger', $file->getErrorMessage());
        }

        return $this->render('@Editorial/Author/Article/update.html.twig', [
            'form' => $form->createView(),
            'article' => $article,
        ]);
    }
}
