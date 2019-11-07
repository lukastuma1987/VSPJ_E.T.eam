<?php

namespace EditorialBundle\Controller;

use EditorialBundle\Entity\Article;
use EditorialBundle\Entity\Review;
use EditorialBundle\Entity\User;
use EditorialBundle\Factory\ResponseFactory;
use EditorialBundle\Repository\ReviewRepository;
use EditorialBundle\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
    public function unassignedArticleListAction()
    {
        /** @var User $user */
        $user = $this->getUser();
        /** @var ReviewRepository $repository */
        $repository = $this->getDoctrine()->getRepository(Review::class);
        /** @var Review[] $reviews */
        $reviews = $repository->findEmptyByReviewer($user);

        return $this->render('@Editorial/Reviewer/Review/waitingForReviewList.html.twig', [
            'reviews' => $reviews,
        ]);
    }

    /**
     * @Route("/stahnout-clanek-{id}", name="reviewer_article_download", methods={"GET"})
     */
    public function downloadArticleAction(Article $article, ResponseFactory $responseFactory)
    {
        /** @var User $user */
        $user = $this->getUser();
        /** @var UserRepository $repository */
        $repository = $this->getDoctrine()->getRepository(User::class);
        /** @var User[] $reviewers */
        $reviewers = $repository->findReviewersByArticle($article);

        if (!in_array($user, $reviewers, true)) {
            throw $this->createAccessDeniedException('Uživatel není recenzentem článku');
        }

        return $responseFactory->createArticleFileResponse($article);
    }
}
