<?php

namespace EditorialBundle\Pagination;

use Doctrine\Common\Persistence\ManagerRegistry;
use EditorialBundle\Entity\Review;
use EditorialBundle\Entity\User;
use EditorialBundle\Repository\ReviewRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class ReviewPaginator
{
    const LIMIT_PER_PAGE = 10;

    /** @var PaginatorInterface */
    private $paginator;
    /** @var ReviewRepository */
    private $repository;
    /** @var RequestStack */
    private $requestStack;

    public function __construct(PaginatorInterface $paginator, ManagerRegistry $doctrine, RequestStack $requestStack)
    {
        $this->paginator = $paginator;
        $this->repository = $doctrine->getRepository(Review::class);
        $this->requestStack = $requestStack;
    }

    /**
     * @param User $reviewer
     * @return PaginationInterface|Review[]
     */
    public function paginateEmptyByReviewer(User $reviewer)
    {
        $query = $this->repository->createEmptyByReviewerQuery($reviewer);
        $request = $this->requestStack->getCurrentRequest();
        $page = $request->query->getInt('strana', 1);

        return $this->paginator->paginate($query, $page, self::LIMIT_PER_PAGE);
    }
}
