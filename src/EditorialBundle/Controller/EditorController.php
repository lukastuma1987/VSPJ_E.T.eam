<?php


namespace EditorialBundle\Controller;

use EditorialBundle\Entity\Article;
use EditorialBundle\Entity\User;
use EditorialBundle\Enum\ArticleStatus;
use EditorialBundle\Factory\EmailFactory;
use EditorialBundle\Factory\ResponseFactory;
use EditorialBundle\Repository\ArticleRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/redakce/redaktor")
 * @Security("has_role('ROLE_EDITOR')")
 */
class EditorController extends Controller
{
    /**
     * @Route("/vypis-neprirazenych-clanku", name="editor_unassigned_articles_list", methods={"GET"})
     */
    public function unassignedArticleListAction()
    {
        /** @var ArticleRepository $repository */
        $repository = $this->getDoctrine()->getRepository(Article::class);
        /** @var Article[] $articles */
        $articles = $repository->findUnassigned();

        return $this->render('@Editorial/Editor/Article/unassignedList.html.twig', [
            'articles' => $articles,
        ]);
    }

    /**
     * @Route("/vypis-clanku-prirazenych-mne", name="editor_articles_assigned_to_editor_list", methods={"GET"})
     */
    public function assignedToEditorArticleListAction()
    {
        /** @var User $user */
        $user = $this->getUser();
        /** @var ArticleRepository $repository */
        $repository = $this->getDoctrine()->getRepository(Article::class);
        /** @var Article[] $articles */
        $articles = $repository->findByEditor($user);

        return $this->render('@Editorial/Editor/Article/assignedToEditorList.html.twig', [
            'articles' => $articles,
        ]);
    }

    /**
     * @Route("/clanek-{id}/stahnout", name="editor_article_download", methods={"GET"})
     */
    public function downloadArticleAction(Article $article, ResponseFactory $responseFactory)
    {
        if (!$article->getLastVersion()) {
            throw $this->createNotFoundException('Článek neobsahuje verzi');
        }

        return $responseFactory->createArticleFileResponse($article);
    }

    /**
     * @Route("/clanek-{id}/priradit", name="editor_article_assign", methods={"POST"})
     */
    public function assignToEditor(Request $request, Article $article, EmailFactory $emailFactory)
    {
        /** @var User $user */
        $user = $this->getUser();
        $editor = $article->getEditor();
        $owner = $article->getOwner();

        if (!$this->isCsrfTokenValid('assign_article', $request->get('_token'))) {
            $this->addFlash('danger', 'Neplatný CSRF token. Zkuste to prosím znovu.');

            return $this->redirectToRoute('editor_unassigned_articles_list');
        }

        if ($editor) {
            if ($user !== $editor) {
                $this->addFlash('warning', 'Článek je již přiřazen jinému editorovi.');
            }

            return $this->redirectToRoute('editor_unassigned_articles_list');
        }

        if ($owner === $user) {
            $this->addFlash('warning', 'Nemůžete dělat recenzní řízení pro své články.');

            return $this->redirectToRoute('editor_unassigned_articles_list');
        }

        $article->setEditor($user);
        $article->setStatus(ArticleStatus::STATUS_ASSIGNED);

        $em = $this->getDoctrine()->getManager();
        $em->flush();

        $this->addFlash('success', 'Článek Vám byl úspěšně přiřazen. Recenzní řízení začalo.');

        $emailFactory->sendStatusChangedNotification($article);

        return $this->redirectToRoute('editor_articles_assigned_to_editor_list');
    }
}
