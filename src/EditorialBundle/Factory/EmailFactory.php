<?php

namespace EditorialBundle\Factory;

use EditorialBundle\Entity\Article;
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
        /** @var UserRepository $repository */
        $repository = $this->doctrine->getRepository(User::class);
        /** @var User[] $editors */
        $editors = $repository->findEditors();
        $recipients = [];

        foreach ($editors as $editor) {
            $recipients[] = $editor->getEmail();
        }

        try {
            $htmlBody = $this->twig->render('@Editorial/Email/newArticle.html.twig', ['article' => $article]);
            $textBody = $this->twig->render('@Editorial/Email/newArticle.txt.twig', ['article' => $article]);
        } catch (Error $e) {
            $this->logWarning($e->getMessage(), ['service' => EmailFactory::class]);
            return;
        }

        $message = (new \Swift_Message('Přidán nový článek'))
            ->setFrom($this->sender)
            ->setTo($recipients)
            ->setBody($htmlBody, 'text/html')
            ->addPart($textBody, 'text/plain')
        ;

        $this->mailer->send($message);
    }

    /**
     * Odesílá clastníkovi článku email o změně statusu
     *
     * @param Article $article
     */
    public function sendStatusChangedNotification(Article $article)
    {
        try {
            $htmlBody = $this->twig->render('@Editorial/Email/statusUpdated.html.twig', ['article' => $article]);
            $textBody = $this->twig->render('@Editorial/Email/statusUpdated.txt.twig', ['article' => $article]);
        } catch (Error $e) {
            $this->logWarning('Rendering email failed', ['service' => EmailFactory::class]);
            return;
        }

        $message = (new \Swift_Message('Změna statusu vašeho článku'))
            ->setFrom($this->sender)
            ->setTo($article->getOwnerEmail())
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
}
