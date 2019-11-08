<?php


namespace EditorialBundle\Controller;

use EditorialBundle\Entity\Article;
use EditorialBundle\Entity\ArticleAuthor;
use EditorialBundle\Entity\ArticleVersion;
use EditorialBundle\Entity\User;
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
        $article->addAuthor(new ArticleAuthor());
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

                return $this->redirectToRoute('editorial_dashboard');
            }

            $this->addFlash('danger', $file->getErrorMessage());
        }

        return $this->render('@Editorial/Author/Article/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
