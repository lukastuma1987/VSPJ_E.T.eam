<?php


namespace EditorialBundle\Controller;

use EditorialBundle\Entity\Article;
use EditorialBundle\Entity\Review;
use EditorialBundle\Entity\User;
use EditorialBundle\Enum\ArticleStatus;
use EditorialBundle\Factory\EmailFactory;
use EditorialBundle\Factory\ResponseFactory;
use EditorialBundle\Form\ArticleStatusType;
use EditorialBundle\Form\AssignReviewersType;
use EditorialBundle\Model\AssignReviewersModel;
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

    /**
     * @Route("/clanek-{id}/pridat-recenzenty", name="editor_article_add_reviewers", methods={"GET", "POST"})
     * @Security("is_granted('ADD_REVIEWER', article)", message="Nejste redaktorem obsluhujícím redakční řízení článku")
     */
    public function assignReviewersToArticleAction(Request $request, Article $article, EmailFactory $emailFactory)
    {
        $validationGroups = ['Default'];
        if ($article->getStatus() === ArticleStatus::STATUS_ASSIGNED) {
            $validationGroups[] = 'New';
        } else {
            $validationGroups[] = 'Existing';
        }

        $form = $this->createForm(AssignReviewersType::class, new AssignReviewersModel($article), [
            'validation_groups' => $validationGroups,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var AssignReviewersModel $model */
            $model = $form->getData();
            $deadline = $model->getDeadline();
            $article->setStatus(ArticleStatus::STATUS_REVIEWERS_ASSIGNED);

            foreach ($model->getReviewers() as $reviewer) {
                $review = new Review();
                $review->setDeadline($deadline);
                $review->setReviewer($reviewer);

                $article->addReview($review);
                $emailFactory->sendReviewRequestNotification($review);
            }

            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $this->addFlash('success', 'Článek byl předán k hodnocení.');

            $emailFactory->sendStatusChangedNotification($article);

            return $this->redirectToRoute('editor_articles_assigned_to_editor_list');
        }

        return $this->render('@Editorial/Editor/Article/assignReviewers.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/clanek-{id}/upravit-status", name="editor_article_change_status", methods={"GET", "POST"})
     * @Security("is_granted('CHANGE_STATUS', article)", message="Nejste redaktorem obsluhujícím redakční řízení článku")
     */
    public function changeStatusAction(Request $request, Article $article, EmailFactory $emailFactory)
    {
        $status = $article->getStatus();
        $form = $this->createForm(ArticleStatusType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Article $article */
            $article = $form->getData();

            if ($article->getStatus() !== $status) {
                $emailFactory->sendStatusChangedNotification($article);

                if ($article->getStatus() === ArticleStatus::STATUS_CHIEF_NEEDED) {
                    $emailFactory->sendChiefNeededNotification($article);
                }
            }

            if ($article->getStatus() === ArticleStatus::STATUS_DECLINED && !$article->getEditor()) {
                /** @var User $editor */
                $editor = $this->getUser();
                $article->setEditor($editor);
            }

            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $this->addFlash('success', 'Status článku upraven');

            if ($article->getStatus() === ArticleStatus::STATUS_NEED_INFO) {
                return $this->redirectToRoute('editor_unassigned_articles_list');
            } else {
                return $this->redirectToRoute('editor_articles_assigned_to_editor_list');
            }
        }

        return $this->render('@Editorial/Editor/Article/changeStatus.html.twig', [
            'form' => $form->createView(),
            'article' => $article,
        ]);
    }
}
