<?php

namespace EditorialBundle\Pagination;

use Doctrine\Common\Persistence\ManagerRegistry;
use EditorialBundle\Entity\Article;
use EditorialBundle\Entity\User;
use EditorialBundle\Model\ArticleFilterModel;
use EditorialBundle\Model\EditorArticlesFilterModel;
use EditorialBundle\Model\OwnerArticlesFilterModel;
use EditorialBundle\Model\ReviewerFilterModel;
use EditorialBundle\Repository\ArticleRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class ArticlePaginator
{
    const LIMIT_PER_PAGE = 10;

    /** @var PaginatorInterface */
    private $paginator;
    /** @var ArticleRepository */
    private $repository;
    /** @var RequestStack */
    private $requestStack;

    public function __construct(PaginatorInterface $paginator, ManagerRegistry $doctrine, RequestStack $requestStack)
    {
        $this->paginator = $paginator;
        $this->repository = $doctrine->getRepository(Article::class);
        $this->requestStack = $requestStack;
    }

    /**
     * @param User $owner
     * @param OwnerArticlesFilterModel|null $model
     * @return PaginationInterface|Article[]
     */
    public function paginateOwnerArticles(User $owner, OwnerArticlesFilterModel $model = null)
    {
        $model = $model ?: new OwnerArticlesFilterModel();
        $request = $this->requestStack->getCurrentRequest();
        $page = $request->query->getInt('strana', 1);
        $query = $this->repository->createOwnerFilteredQuery($owner, $model);

        return $this->paginator->paginate($query, $page, self::LIMIT_PER_PAGE);
    }

    /**
     * @param User $reviewer
     * @param ReviewerFilterModel|null $model
     * @return PaginationInterface|Article[]
     */
    public function paginateByReviewer(User $reviewer, ReviewerFilterModel $model = null)
    {
        $model = $model ?: new ReviewerFilterModel();
        $request = $this->requestStack->getCurrentRequest();
        $page = $request->query->getInt('strana', 1);
        $query = $this->repository->createReviewerFilteredQuery($reviewer, $model);

        return $this->paginator->paginate($query, $page, self::LIMIT_PER_PAGE);
    }

    /**
     * @param ArticleFilterModel|null $model
     * @return PaginationInterface|Article[]
     */
    public function paginateAllArticles(ArticleFilterModel $model = null)
    {
        $model = $model ?: new ArticleFilterModel();
        $request = $this->requestStack->getCurrentRequest();
        $page = $request->query->getInt('strana', 1);
        $query = $this->repository->createFilteredQuery($model);

        return $this->paginator->paginate($query, $page, self::LIMIT_PER_PAGE);
    }

    /**
     * @param EditorArticlesFilterModel|null $model
     * @return PaginationInterface|Article[]
     */
    public function paginateUnassignedArticles(EditorArticlesFilterModel $model = null)
    {
        $model = $model ?: new EditorArticlesFilterModel();
        $request = $this->requestStack->getCurrentRequest();
        $page = $request->query->getInt('strana', 1);
        $query = $this->repository->createEditorFilteredQuery($model);

        return $this->paginator->paginate($query, $page, self::LIMIT_PER_PAGE);
    }

    /**
     * @param User $editor
     * @param EditorArticlesFilterModel|null $model
     * @return PaginationInterface|Article[]
     */
    public function paginateByEditor(User $editor, EditorArticlesFilterModel $model = null)
    {
        $model = $model ?: new EditorArticlesFilterModel();
        $request = $this->requestStack->getCurrentRequest();
        $page = $request->query->getInt('strana', 1);
        $query = $this->repository->createEditorFilteredQuery($model, $editor);

        return $this->paginator->paginate($query, $page, self::LIMIT_PER_PAGE);
    }
}
