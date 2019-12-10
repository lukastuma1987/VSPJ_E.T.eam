<?php

namespace EditorialBundle\Controller;

use EditorialBundle\Entity\Review;
use EditorialBundle\Entity\User;
use EditorialBundle\Enum\ArticleStatus;
use EditorialBundle\Factory\EmailFactory;
use EditorialBundle\Form\Filter\ReviewerFilterType;
use EditorialBundle\Form\ReviewType;
use EditorialBundle\Pagination\ArticlePaginator;
use EditorialBundle\Pagination\ReviewPaginator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/redakce/recenzent")
 * @Security("has_role('ROLE_REVIEWER')")
 */
class ReviewerController extends Controller
{
    /**
     * @Route("/vypis-clanku", name="reviewer_reviews_assigned_to_me", methods={"GET"})
     */
    public function waitingForReviewListAction(ReviewPaginator $reviewPaginator)
    {
        /** @var User $reviewer */
        $reviewer = $this->getUser();

        try {
            $pagination = $reviewPaginator->paginateEmptyByReviewer($reviewer);
        } catch (\Exception $exception) {
            return $this->redirectToRoute('reviewer_reviews_assigned_to_me');
        }

        return $this->render('@Editorial/Reviewer/Review/waitingForReviewList.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("/hodnoceni-{id}/vyplnit", name="reviewer_review_fill", methods={"GET", "POST"})
     * @Security("is_granted('ADD', review)", message="Nemáte oprávnění na vložení hodnocení")
     */
    public function fillAction(Request $request, Review $review, EmailFactory $emailFactory)
    {
        $form = $this->createForm(ReviewType::class, $review);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Review $review */
            $review = $form->getData();
            $review->setFilled(new \DateTime());

            $article = $review->getArticle();
            if ($article && $article->hasAllReviewsFilled()) {
                $article->setStatus(ArticleStatus::STATUS_REVIEWS_FILLED);
                $emailFactory->sendStatusChangedNotification($article);
                $emailFactory->sendAllReviewsFilledNotification($article);
            }

            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $emailFactory->sendNewReviewNotification($review);
            $this->addFlash('success', 'Hodnocení bylo úspěšně vloženo.');

            return $this->redirectToRoute('reviewer_reviews_assigned_to_me');
        }

        return $this->render('@Editorial/Reviewer/Review/fill.html.twig', [
            'form' => $form->createView(),
            'review' => $review,
        ]);
    }

    /**
     * @Route("/vypis-recenzovanych-clanku", name="reviewer_reviews_reviewed", methods={"GET"})
     */
    public function reviewedListAction(Request $request, ArticlePaginator $articlePaginator)
    {
        /** @var User $reviewer */
        $reviewer = $this->getUser();
        $form = $this->createForm(ReviewerFilterType::class, null, ['method' => 'GET']);
        $form->handleRequest($request);

        try {
            $pagination = $articlePaginator->paginateByReviewer($reviewer, $form->getData());
        } catch (\Exception $exception) {
            return $this->redirectToRoute('reviewer_reviews_reviewed');
        }

        return $this->render('@Editorial/Reviewer/Review/reviewedList.html.twig', [
            'pagination' => $pagination,
            'form' => $form->createView(),
        ]);
    }
}
