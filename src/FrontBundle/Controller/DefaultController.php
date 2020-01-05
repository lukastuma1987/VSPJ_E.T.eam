<?php

namespace FrontBundle\Controller;

use EditorialBundle\Entity\HelpDeskMessage;
use EditorialBundle\Entity\Magazine;
use EditorialBundle\Entity\Role;
use EditorialBundle\Entity\User;
use EditorialBundle\Factory\ResponseFactory;
use EditorialBundle\Form\HelpDeskMessageType;
use EditorialBundle\Form\RegistrationType;
use EditorialBundle\Repository\MagazineRepository;
use EditorialBundle\Repository\RoleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

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

    /**
     * @Route("/registrace", name="registration", methods={"GET", "POST"})
     */
    public function registrationAction(Request $request)
    {
        $form = $this->createForm(RegistrationType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $passwordEncoder = $this->get('security.password_encoder');
            $doctrine = $this->getDoctrine();
            /** @var RoleRepository $repository */
            $repository = $doctrine->getRepository(Role::class);
            /** @var Role $roleAuthor */
            $roleAuthor = $repository->findOneByRole('ROLE_AUTHOR');
            /** @var User $user */
            $user = $form->getData();

            $password = $passwordEncoder->encodePassword($user, $user->getPlaintextPassword());
            $user->setPassword($password);
            $user->addRole($roleAuthor);

            $em = $doctrine->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Účet byl úspěšně vytvořen. Můžete se přihlásit.');

            return $this->redirectToRoute('login');
        }

        return $this->render('@Front/Default/registration.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
