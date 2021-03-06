<?php


namespace EditorialBundle\Controller;

use EditorialBundle\Entity\Article;
use EditorialBundle\Entity\Magazine;
use EditorialBundle\Factory\EmailFactory;
use EditorialBundle\Form\ChiefEditorEditArticleType;
use EditorialBundle\Form\Filter\ArticleFilterType;
use EditorialBundle\Form\MagazineType;
use EditorialBundle\Form\MagazineUploadType;
use EditorialBundle\Pagination\ArticlePaginator;
use EditorialBundle\Repository\MagazineRepository;
use EditorialBundle\Util\FileNameUtil;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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
     * @Route("/cislo-casopisu-{id}/upravit", name="chief_editor_magazine_edit", methods={"GET", "POST"})
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
     * @Route("/cislo-casopisu-{id}/smazat", name="chief_editor_magazine_delete", methods={"DELETE"})
     */
    public function deleteAction(Request $request, Magazine $magazine, Filesystem $filesystem)
    {
        if (!$this->isCsrfTokenValid('delete_magazine', $request->get('_token'))) {
            $this->addFlash('danger', 'Neplatný CSRF token. Zkuste to prosím znovu');

            return $this->redirectToRoute('chief_editor_magazine_list');
        }

        if (count($magazine->getArticles())) {
            $this->addFlash('warning', 'Číslo časopisu již obsahuje články v recenzním řízení.');
            
            return $this->redirectToRoute('chief_editor_magazine_list');
        }

        $id = $magazine->getId();
        $year = $magazine->getYear();
        $number = $magazine->getNumber();
        $fileName = FileNameUtil::getMagazineFileName($magazine);

        $filesystem->remove($this->getParameter('magazine_directory') . '/' . $fileName);

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

    /**
     * @Route("/cislo-casopisu-{id}/nahrat", name="chief_editor_magazine_upload", methods={"GET", "POST"})
     */
    public function uploadMagazineAction(Request $request, Magazine $magazine)
    {
        $form = $this->createForm(MagazineUploadType::class, $magazine);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $file */
            $file = $form['file']->getData();;

            if ($file && $file->isValid()) {
                $magazine->setSuffix($file->getClientOriginalExtension());

                $em = $this->getDoctrine()->getManager();
                $em->flush();

                try {
                    $fileName = FileNameUtil::getMagazineFileName($magazine);
                    $file->move($this->getParameter('magazine_directory'), $fileName);
                } catch (FileException $exception) {
                    $this->addFlash('danger', $exception->getMessage());
                    return $this->redirectToRoute('chief_editor_magazine_list');
                }

                return $this->redirectToRoute('chief_editor_magazine_list');
            }

            $this->addFlash('danger', $file->getErrorMessage());
        }

        return $this->render('@Editorial/ChiefEditor/Magazine/upload.html.twig', [
            'form' => $form->createView(),
            'magazine' => $magazine,
        ]);
    }

    /**
     * @Route("/clanky/vse", name="chief_editor_articles_list", methods={"GET"})
     */
    public function listArticles(Request $request, ArticlePaginator $articlePaginator)
    {
        $form = $this->createForm(ArticleFilterType::class, null, ['method' => 'GET']);
        $form->handleRequest($request);

        try {
            $pagination = $articlePaginator->paginateAllArticles($form->getData());
        } catch (\Exception $exception) {
            return $this->redirectToRoute('chief_editor_articles_list');
        }

        return $this->render('@Editorial/ChiefEditor/Article/list.html.twig', [
            'pagination' => $pagination,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/clanek-{id}/upravit", name="chief_editor_article_edit", methods={"GET", "POST"})
     */
    public function editArticleAction(Request $request, Article $article, EmailFactory $emailFactory)
    {
        $status = $article->getStatus();
        $form = $this->createForm(ChiefEditorEditArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Article $article */
            $article = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $this->addFlash('success', 'Vlastnosti článku upraveny');

            if ($article->getStatus() !== $status) {
                $emailFactory->sendStatusChangedNotification($article);
            }

            return $this->redirectToRoute('chief_editor_articles_list');
        }

        return $this->render('@Editorial/ChiefEditor/Article/editArticle.html.twig', [
            'form' => $form->createView(),
            'article' => $article,
        ]);
    }
}
