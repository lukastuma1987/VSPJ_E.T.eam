<?php

namespace FrontBundle\Controller;

use EditorialBundle\Entity\Magazine;
use EditorialBundle\Factory\ResponseFactory;
use EditorialBundle\Repository\MagazineRepository;
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
     * @Route("/cisla-casopisu", name="cisla_casopisu", methods={"GET"})
     */
    public function magazinesAction()
    {
        /** @var MagazineRepository $repository */
        $repository = $this->getDoctrine()->getRepository(Magazine::class);
        /** @var Magazine[] $magazines */
        $magazines = $repository->findWithFile();

        return $this->render('@Front/Default/magazines.html.twig', [
            'magazines' => $magazines,
        ]);
    }

    /**
     * @Route("/stahnout/{id}", name="stahnout_casopis", methods={"GET"})
     */
    public function magazineDownload(Magazine $magazine)
    {
        if (!$magazine->getFile()) {
            throw $this->createNotFoundException('Číslo časopisu není nahráno');
        }

        return ResponseFactory::createMagazineFileResponse($magazine);
    }

    /**
     * @Route("/o-casopise", name="o-casopise", methods={"GET"})
     */
    public function oCasopiseAction()
    {
        return $this->render('@Front/Default/oCasopise.html.twig');
    }
}
