<?php

namespace EditorialBundle\Factory;

use EditorialBundle\Entity\Article;
use EditorialBundle\Entity\HelpDeskMessage;
use EditorialBundle\Entity\Review;
use EditorialBundle\Entity\User;
use EditorialBundle\Repository\UserRepository;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Twig\Environment;
use Twig\Error\Error;

class EmailFactory
{
    /** @var \Swift_Mailer */
    private $mailer;

    /** @var Environment */
    private $twig;

    /** @var ManagerRegistry */
    private $doctrine;

    /** @var LoggerInterface */
    private $logger;

    /** @var string */
    private $sender;

    public function __construct(
        \Swift_Mailer $mailer,
        Environment $twig,
        ManagerRegistry $doctrine,
        $mailerUser,
        LoggerInterface $logger = null
    ) {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->doctrine = $doctrine;
        $this->sender = [$mailerUser => 'Logos Polytechnikos'];
        $this->logger = $logger;
    }

    /**
     * Odesílá redaktorům email o novém článku
     *
     * @param Article $article
     */
    public function sendNewArticleNotification(Article $article)
    {
        try {
            $htmlBody = $this->twig->render('@Editorial/Email/newArticle.html.twig', ['article' => $article]);
            $textBody = $this->twig->render('@Editorial/Email/newArticle.txt.twig', ['article' => $article]);
        } catch (Error $e) {
            $this->logWarning($e->getMessage(), ['service' => EmailFactory::class]);
            return;
        }

        $message = (new \Swift_Message('Přidán nový článek'))
            ->setFrom($this->sender)
            ->setTo($this->getEditorsEmails())
            ->setBody($htmlBody, 'text/html')
            ->addPart($textBody, 'text/plain')
        ;

        $this->mailer->send($message);
    }

    /**
     * Odesílá vlastníkovi článku email o změně statusu
     *
     * @param Article $article
     */
    public function sendStatusChangedNotification(Article $article)
    {
        try {
            $htmlBody = $this->twig->render('@Editorial/Email/statusUpdated.html.twig', ['article' => $article]);
            $textBody = $this->twig->render('@Editorial/Email/statusUpdated.txt.twig', ['article' => $article]);
        } catch (Error $e) {
            $this->logWarning($e->getMessage(), ['service' => EmailFactory::class]);
            return;
        }

        $message = (new \Swift_Message('Změna statusu Vašeho článku'))
            ->setFrom($this->sender)
            ->setTo($article->getOwnerEmail())
            ->setBody($htmlBody, 'text/html')
            ->addPart($textBody, 'text/plain')
        ;

        $this->mailer->send($message);
    }

    /**
     * Odesílá recenzentům email o požadavku na recenzi
     *
     * @param Review $review
     */
    public function sendReviewRequestNotification(Review $review)
    {
        try {
            $htmlBody = $this->twig->render('@Editorial/Email/reviewRequested.html.twig', ['review' => $review]);
            $textBody = $this->twig->render('@Editorial/Email/reviewRequested.txt.twig', ['review' => $review]);
        } catch (Error $e) {
            $this->logWarning($e->getMessage(), ['service' => EmailFactory::class]);
            return;
        }

        $message = (new \Swift_Message('Požadavek na hodnocení článku'))
            ->setFrom($this->sender)
            ->setTo($review->getReviewerEmail())
            ->setBody($htmlBody, 'text/html')
            ->addPart($textBody, 'text/plain')
        ;

        $this->mailer->send($message);
    }

    /**
     * Odesílá vlastníkovi článku informaci o vložení nového hodnocení
     *
     * @param Review $review
     */
    public function sendNewReviewNotification(Review $review)
    {
        try {
            $htmlBody = $this->twig->render('@Editorial/Email/newReview.html.twig', ['review' => $review]);
            $textBody = $this->twig->render('@Editorial/Email/newReview.txt.twig', ['review' => $review]);
        } catch (Error $e) {
            $this->logWarning($e->getMessage(), ['service' => EmailFactory::class]);
            return;
        }

        $article = $review->getArticle();

        $message = (new \Swift_Message('Bylo vloženo nové hodnocení Vašeho článku'))
            ->setFrom($this->sender)
            ->setTo($article ? $article->getOwnerEmail() : '')
            ->setBody($htmlBody, 'text/html')
            ->addPart($textBody, 'text/plain')
        ;

        $this->mailer->send($message);
    }

    /**
     * Odesílá editorovi inforamci o tom, že všechna hdnocení byla vyplněna
     *
     * @param Article $article
     */
    public function sendAllReviewsFilledNotification(Article $article)
    {
        try {
            $htmlBody = $this->twig->render('@Editorial/Email/allReviewsFilled.html.twig', ['article' => $article]);
            $textBody = $this->twig->render('@Editorial/Email/allReviewsFilled.txt.twig', ['article' => $article]);
        } catch (Error $e) {
            $this->logWarning($e->getMessage(), ['service' => EmailFactory::class]);
            return;
        }

        $message = (new \Swift_Message('Všechna hodnocení byla vyplněna'))
            ->setFrom($this->sender)
            ->setTo($article->getEditorEmail())
            ->setBody($htmlBody, 'text/html')
            ->addPart($textBody, 'text/plain')
        ;

        $this->mailer->send($message);
    }

    public function sendNewArticleVersionNotification(Article $article)
    {
        $recipient = $article->getEditorEmail() ?: $this->getEditorsEmails();

        try {
            $htmlBody = $this->twig->render('@Editorial/Email/newArticleVersion.html.twig', ['article' => $article]);
            $textBody = $this->twig->render('@Editorial/Email/newArticleVersion.txt.twig', ['article' => $article]);
        } catch (Error $e) {
            $this->logWarning($e->getMessage(), ['service' => EmailFactory::class]);
            return;
        }

        $message = (new \Swift_Message('Byla vytvořena nová verze článku'))
            ->setFrom($this->sender)
            ->setTo($recipient)
            ->setBody($htmlBody, 'text/html')
            ->addPart($textBody, 'text/plain')
        ;

        $this->mailer->send($message);
    }

    public function sendHelpDeskAnswer(HelpDeskMessage $message)
    {
        try {
            $htmlBody = $this->twig->render('@Editorial/Email/helpDeskAnswer.html.twig', ['message' => $message]);
            $textBody = $this->twig->render('@Editorial/Email/helpDeskAnswer.txt.twig', ['message' => $message]);
        } catch (Error $e) {
            $this->logWarning($e->getMessage(), ['service' => EmailFactory::class]);
            return;
        }

        $message = (new \Swift_Message('Odpověď na Vaši zprávu z helpdesku'))
            ->setFrom($this->sender)
            ->setTo($message->getEmail())
            ->setBody($htmlBody, 'text/html')
            ->addPart($textBody, 'text/plain')
        ;

        $this->mailer->send($message);
    }

    // private

    private function logWarning($message, $context = [])
    {
        if ($this->logger) {
            $this->logger->warning($message, $context);
        }
    }

    private function getEditorsEmails()
    {
        /** @var UserRepository $repository */
        $repository = $this->doctrine->getRepository(User::class);
        /** @var User[] $editors */
        $editors = $repository->findEditors();
        $recipients = [];

        foreach ($editors as $editor) {
            $recipients[] = $editor->getEmail();
        }

        return $recipients;
    }
}
