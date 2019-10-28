<?php


namespace EditorialBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/redakce/autor")
 * @Security("has_role('ROLE_AUTHOR')")
 */
class AuthorController extends Controller
{
    /**
     * @Route("/novy", name="author_article_create", methods={"GET", "POST"})
     */
    public function createArticleAction(Request $request)
    {

    }
}
