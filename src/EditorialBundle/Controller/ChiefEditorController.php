<?php


namespace EditorialBundle\Controller;

use EditorialBundle\Entity\Magazine;
use EditorialBundle\Form\MagazineType;
use EditorialBundle\Repository\MagazineRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/redakce/sefredaktor")
 * @Security("has_role('ROLE_CHIEF_EDITOR')")
 */
class ChiefEditorController extends Controller
{
    /**
     * @Route("/vypis-cisel-casopisu", name="chief_editor_magazine_list", methods={"GET"})
     */
    public function magazineListAction()
    {
        /** @var MagazineRepository $repository */
        $repository = $this->getDoctrine()->getRepository(Magazine::class);
        /** @var Magazine[] $magazines */
        $magazines = $repository->findAll();

        return $this->render('@Editorial/ChiefEditor/Magazine/list.html.twig', [
            'magazines' => $magazines,
        ]);
    }

    /**
     * @Route("/nove-cislo-casopisu", name="chief_editor_magazine_new", methods={"GET", "POST"})
     * @Route("/{id}/upravit-cislo-casopisu", name="chief_editor_magazine_edit", methods={"GET", "POST"})
     */
    public function editAction(Request $request, Magazine $magazine = null)
    {
        $magazine = $magazine ?: new Magazine();
        $form = $this->createForm(MagazineType::class, $magazine);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $magazine = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($magazine);
            $em->flush();

            return $this->redirectToRoute('chief_editor_magazine_list');
        }

        return $this->render('@Editorial/ChiefEditor/Magazine/edit.html.twig', [
            'form' => $form->createView(),
            'magazine' => $magazine,
        ]);
    }

    /**
     * @Route("/{id}/smazat", name="chief_editor_magazine_delete", methods={"DELETE"})
     */
    public function deleteAction(Request $request, Magazine $magazine)
    {
        if (!$this->isCsrfTokenValid('delete_magazine', $request->get('_token'))) {
            $this->addFlash('danger', 'Neplatný CSRF token. Zkuste to prosím znovu');

            return $this->redirectToRoute('chief_editor_magazine_list');
        }

        if (count($magazine->getArticles())) {
            $this->addFlash('warning', 'Číslo časopisu již obsahuje články v recenzním řízení.');
        }

        $id = $magazine->getId();
        $year = $magazine->getYear();
        $number = $magazine->getNumber();

        $em = $this->getDoctrine()->getManager();
        $em->remove($magazine);
        $em->flush();

        $this->addFlash('success', sprintf(
            'Číslo časopisu (ročník %d, číslo %d, id %d) bylo úspěšně odstraněno.',
            $year,
            $number,
            $id
        ));

        return $this->redirectToRoute('chief_editor_magazine_list');
    }
}
