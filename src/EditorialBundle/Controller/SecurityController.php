<?php

namespace EditorialBundle\Controller;

use EditorialBundle\Entity\User;
use EditorialBundle\Form\PasswordChangeType;
use EditorialBundle\Model\PasswordChangeModel;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends Controller
{
    /**
     * @Route("/login", name="login")
     */
    public function loginAction(AuthenticationUtils $authenticationUtils)
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('@Editorial/Security/login.html.twig', [
            'lastUsername' => $lastUsername,
            'error' => $error,
        ]);
    }

    /**
     * @Route("/redakce/zmena-hesla", name="password_change")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function passwordChangeAction(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        /** @var User $user */
        $user = $this->getUser();
        $form = $this->createForm(PasswordChangeType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var PasswordChangeModel $model */
            $model = $form->getData();

            if ($passwordEncoder->isPasswordValid($user, $model->getCurrent())) {
                $newPassword = $passwordEncoder->encodePassword($user, $model->getNew());
                $user->setPassword($newPassword);

                $em = $this->getDoctrine()->getManager();
                $em->flush();

                $this->addFlash('success', 'Změna hesla proběhla úspěšně.');

                return $this->redirectToRoute('editorial_dashboard');
            }

            $form->get('current')->addError(new FormError('Nesprávné heslo'));
        }

        return $this->render('@Editorial/Security/passwordChange.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
