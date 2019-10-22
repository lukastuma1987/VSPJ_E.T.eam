<?php

namespace EditorialBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/redakce")
 */
class DefaultController extends Controller
{
    /**
     * @Route("/", methods={"GET"})
     */
    public function indexAction()
    {
        return $this->render('@Editorial/Default/index.html.twig');
    }
}
