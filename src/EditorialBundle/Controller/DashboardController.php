<?php

namespace EditorialBundle\Controller;

use EditorialBundle\Entity\Magazine;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/redakce")
 */
class DashboardController extends Controller
{
    /**
     * @Route("/", name="editorial_dashboard", methods={"GET"})
     */
    public function dashboardAction()
    {
        $repository = $this->getDoctrine()->getRepository(Magazine::class);
        /** @var Magazine[] $magazines */
        $magazines = $repository->findUpcoming();

        return $this->render('@Editorial/Dashboard/dashboard.html.twig', [
            'magazines' => $magazines,
        ]);
    }
}
