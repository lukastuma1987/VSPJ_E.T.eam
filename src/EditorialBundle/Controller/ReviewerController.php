<?php

namespace EditorialBundle\Controller;

use EditorialBundle\Entity\Review;
use EditorialBundle\Entity\User;
use EditorialBundle\Repository\ReviewRepository;
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
}
