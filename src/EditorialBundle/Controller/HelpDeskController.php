<?php


namespace EditorialBundle\Controller;

use EditorialBundle\Entity\HelpDeskMessage;
use EditorialBundle\Entity\User;
use EditorialBundle\Factory\EmailFactory;
use EditorialBundle\Form\HelpDeskAnswerType;
use EditorialBundle\Repository\HelpDeskMessageRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/redakce/helpdesk")
 * @Security("has_role('ROLE_HELP_DESK')")
 */
class HelpDeskController extends Controller
{
    /**
     * @Route("/nezodpovezene", name="help_desk_unanswered", methods={"GET"})
     */
    public function unansweredAction()
    {
        /** @var HelpDeskMessageRepository $repository */
        $repository = $this->getDoctrine()->getRepository(HelpDeskMessage::class);
        /** @var HelpDeskMessage[] $messages */
        $messages = $repository->findUnanswered();

        return $this->render('@Editorial/HelpDesk/unansweredList.html.twig', [
            'messages' => $messages,
        ]);
    }

    /**
     * @Route("/zodpovezene", name="help_desk_answered", methods={"GET"})
     */
    public function answeredAction()
    {
        /** @var HelpDeskMessageRepository $repository */
        $repository = $this->getDoctrine()->getRepository(HelpDeskMessage::class);
        /** @var HelpDeskMessage[] $messages */
        $messages = $repository->findAnswered();

        return $this->render('@Editorial/HelpDesk/answeredList.html.twig', [
            'messages' => $messages,
        ]);
    }

    /**
     * @Route("/odpovedet/{id}", name="help_desk_add_answer", methods={"GET", "POST"})
     * @Security("is_granted('ANSWER', message)", message="Zpráva již byla zodpovězena nebo ignorována")
     */
    public function addAnswerAction(Request $request, HelpDeskMessage $message, EmailFactory $emailFactory)
    {
        /** @var User $user */
        $user = $this->getUser();
        $form = $this->createForm(HelpDeskAnswerType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var HelpDeskMessage $message */
            $message = $form->getData();
            $message->setAnswered(new \DateTime());
            $message->setManager($user);

            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $emailFactory->sendHelpDeskAnswer($message);
            $this->addFlash('success', 'Email s odpovědí byl odeslán');

            return $this->redirectToRoute('help_desk_answered');
        }

        return $this->render('@Editorial/HelpDesk/addAnswer.html.twig', [
            'form' => $form->createView(),
            'message' => $message,
        ]);
    }

    /**
     * @Route("/ingorovat/{id}", name="help_desk_ignore", methods={"POST"})
     * @Security("is_granted('IGNORE', message)", message="Zpráva již byla zodpovězena nebo ignorována")
     */
    public function ignoreAction(Request $request, HelpDeskMessage $message)
    {
        if (!$this->isCsrfTokenValid('help_desk_ignore', $request->get('_token'))) {
            $this->addFlash('danger', 'Neplatný CSRF token. Zkuste to prosím znovu');

            return $this->redirectToRoute('help_desk_unanswered');
        }

        /** @var User $user */
        $user = $this->getUser();
        $message->setManager($user);

        $em = $this->getDoctrine()->getManager();
        $em->flush();

        $this->addFlash('success', 'Zpráva byla zařazena mezi ignorované');

        return $this->redirectToRoute('help_desk_unanswered');
    }

    /**
     * @Route("/detail/{id}", name="help_desk_detail", methods={"GET", "POST"})
     */
    public function detailAction(HelpDeskMessage $message)
    {
        return $this->render('@Editorial/HelpDesk/detail.html.twig', [
            'message' => $message,
        ]);
    }
}
