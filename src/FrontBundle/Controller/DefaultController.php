<?php

namespace FrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/", methods={"GET"})
     */
    public function indexAction()
    {
        return $this->render('@Front/Default/index.html.twig');
    }
}
