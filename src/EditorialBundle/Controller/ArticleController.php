<?php

namespace EditorialBundle\Controller;

use EditorialBundle\Entity\Article;
use EditorialBundle\Entity\ArticleComment;
use EditorialBundle\Entity\User;
use EditorialBundle\Form\ArticleCommentType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/redakce/clanek")
 */
class ArticleController extends Controller
{
    /**
     * @Route("/{id}/detail", name="article_detail", methods={"GET"})
     * @Security("is_granted('DETAIL', article)", message="Nemáte oprávnění na náhled článku")
     */
    public function detailAction(Article $article)
    {
        return $this->render('@Editorial/Article/detail.html.twig', [
            'article' => $article,
        ]);
    }

    /**
     * @Route("/{id}/pridat-komentar", name="article_comment_add", methods={"GET", "POST"})
     * @Security("is_granted('COMMENT', article)", message="Nemáte oprávnění pro přidávání komentářů")
     */
    public function addCommentAction(Request $request, Article $article)
    {
        /** @var User $user */
        $user = $this->getUser();
        $form = $this->createForm(ArticleCommentType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var ArticleComment $comment */
            $comment = $form->getData();
            $comment->setUser($user);
            $comment->setArticle($article);

            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();

            return $this->redirectToRoute('article_detail', ['id' => $article->getId(), '_fragment' => 'diskuse']);
        }

        return $this->render('@Editorial/Article/addComment.html.twig', [
            'form' => $form->createView(),
            'article' => $article,
        ]);
    }
}
