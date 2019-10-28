<?php

namespace EditorialBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/redakce")
 */
class DashboardController extends Controller
{
    /**
     * @Route("/", name="editorial_dashboard",methods={"GET"})
     */
    public function dashboardAction()
    {
        return $this->render('@Editorial/Dashboard/dashboard.html.twig');
    }
}
