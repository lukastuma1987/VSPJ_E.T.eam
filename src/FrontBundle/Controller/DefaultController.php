<?php

namespace FrontBundle\Controller;

use EditorialBundle\Entity\HelpDeskMessage;
use EditorialBundle\Entity\Magazine;
use EditorialBundle\Entity\User;
use EditorialBundle\Factory\ResponseFactory;
use EditorialBundle\Form\HelpDeskMessageType;
use EditorialBundle\Repository\MagazineRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
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
        if (!$magazine->getSuffix()) {
            throw $this->createNotFoundException('Číslo časopisu není nahráno');
        }

        /** @var ResponseFactory $responseFactory */
        $responseFactory = $this->get(ResponseFactory::class);

        return $responseFactory->createMagazineFileResponse($magazine);
    }

    /**
     * @Route("/o-casopise", name="o-casopise", methods={"GET"})
     */
    public function oCasopiseAction()
    {
        return $this->render('@Front/Default/oCasopise.html.twig');
    }

    /**
     * @Route("/helpdesk", name="helpdesk", methods={"GET", "POST"})
     */
    public function helpDeskAction(Request $request)
    {
        $message = new HelpDeskMessage();

        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            /** @var User $user */
            $user = $this->getUser();
            $message->setEmail($user->getEmail());
        }

        $form = $this->createForm(HelpDeskMessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $message = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($message);
            $em->flush();

            $this->addFlash('success', 'Vaše zpráva byla úspěšně odeslána');

            return $this->redirectToRoute('helpdesk');
        }

        return $this->render('@Front/Default/helpDesk.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
