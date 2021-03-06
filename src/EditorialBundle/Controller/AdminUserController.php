<?php

namespace EditorialBundle\Controller;

use EditorialBundle\Entity\User;
use EditorialBundle\Form\UserType;
use EditorialBundle\Service\UserRelationFinder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/redakce/admin/uzivatel")
 * @Security("has_role('ROLE_ADMIN')")
 */
class AdminUserController extends Controller
{
    /**
     * @Route("/vypis", name="admin_user_list", methods={"GET"})
     */
    public function listAction()
    {
        $repository = $this->getDoctrine()->getRepository(User::class);
        $users = $repository->findAll();

        return $this->render('@Editorial/Admin/User/list.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/novy", name="admin_user_new", methods={"GET", "POST"})
     * @Route("/{id}/upravit", name="admin_user_edit", methods={"GET", "POST"})
     */
    public function editAction(Request $request, UserPasswordEncoderInterface $passwordEncoder, User $user = null)
    {
        $validationGroups = ['Default'];
        if (!$user) {
            $validationGroups[] = 'Create';
        }

        $form = $this->createForm(UserType::class, $user, ['validation_groups' => $validationGroups]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            if ($plaintextPassword = $user->getPlaintextPassword()) {
                $password = $passwordEncoder->encodePassword($user, $plaintextPassword);
                $user->setPassword($password);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('admin_user_list');
        }

        return $this->render('@Editorial/Admin/User/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

    /**
     * @Route("/{id}/smazat", name="admin_user_delete", methods={"DELETE"})
     * @Security("is_granted('REMOVE', userToRemove)", message="Nemáte oprávnění ke smazání tohoto uživatele")
     */
    public function deleteAction(Request $request, User $userToRemove, UserRelationFinder $userRelationFinder)
    {
        if (!$this->isCsrfTokenValid('delete_user', $request->get('_token'))) {
            $this->addFlash('danger', 'Neplatný CSRF token. Zkuste to prosím znovu');

            return $this->redirectToRoute('admin_user_list');
        }

        $userRelations = $userRelationFinder->findUserRelations($userToRemove);
        if ($userRelations->hasRelations()) {
            $message = $userRelations->getRelationMessage();
            $message .= ' Z tohoto důvodu nemůže být odstraněn';
            $this->addFlash('danger', $message);

            return $this->redirectToRoute('admin_user_list');
        }

        $username = $userToRemove->getUsername();

        $em = $this->getDoctrine()->getManager();
        $em->remove($userToRemove);
        $em->flush();

        $this->addFlash('success', sprintf('Uživatel "%s" byl úspěšně odstraněn.', $username));

        return $this->redirectToRoute('admin_user_list');
    }
}
