<?php

namespace EditorialBundle\Service;

use Doctrine\Common\Persistence\ManagerRegistry;
use EditorialBundle\Entity\Article;
use EditorialBundle\Entity\ArticleComment;
use EditorialBundle\Entity\HelpDeskMessage;
use EditorialBundle\Entity\Review;
use EditorialBundle\Entity\User;
use EditorialBundle\Model\UserRelations;
use EditorialBundle\Repository\ArticleCommentRepository;
use EditorialBundle\Repository\ArticleRepository;
use EditorialBundle\Repository\HelpDeskMessageRepository;
use EditorialBundle\Repository\ReviewRepository;

class UserRelationFinder
{
    /** @var ManagerRegistry */
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @param User $user
     * @return UserRelations
     */
    public function findUserRelations(User $user)
    {
        /** @var ArticleRepository $articleRepository */
        $articleRepository = $this->doctrine->getRepository(Article::class);
        /** @var ReviewRepository $reviewRepository */
        $reviewRepository = $this->doctrine->getRepository(Review::class);
        /** @var ArticleCommentRepository $articleCommentRepository */
        $articleCommentRepository = $this->doctrine->getRepository(ArticleComment::class);
        /** @var HelpDeskMessageRepository $helpDeskMessageRepository */
        $helpDeskMessageRepository = $this->doctrine->getRepository(HelpDeskMessage::class);

        $userRelations = new UserRelations($user);
        $userRelations
            ->setArticlesCount($articleRepository->countByAuthor($user))
            ->setReviewsCount($reviewRepository->countByReviewer($user))
            ->setAssignedCount($articleRepository->countByEditor($user))
            ->setCommentCount($articleCommentRepository->countByUser($user))
            ->setHelpDeskMessagesCount($helpDeskMessageRepository->countByManager($user))
        ;

        return $userRelations;
    }
}
