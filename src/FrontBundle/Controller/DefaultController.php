<?php

namespace FrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage", methods={"GET"})
     */
    public function indexAction()
    {
        return $this->render('@Front/Default/index.html.twig');
    }

    /**
     * @Route("/o-casopise", name="o-casopise", methods={"GET"})
     */
    public function oCasopiseAction()
    {
        return $this->render('@Front/Default/oCasopise.html.twig');
    }
}
