<?php

namespace EditorialBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use EditorialBundle\Entity\Role;
use EditorialBundle\Entity\User;
use EditorialBundle\Repository\RoleRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateAdminUserCommand extends Command
{
    protected static $defaultName = 'et:create-admin-user';

    private $validator;
    private $em;
    private $passwordEncoder;

    public function __construct(
        ValidatorInterface $validator,
        EntityManagerInterface $em,
        UserPasswordEncoderInterface $passwordEncoder
    ) {
        $this->validator = $validator;
        $this->em = $em;
        $this->passwordEncoder = $passwordEncoder;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Vytvoří nového uživatele s administrátorskými právy.')
            ->setHelp('Použití: php bin/console ' . self::$defaultName . ' username email')
            ->addArgument('username', InputArgument::REQUIRED, 'Uživatelské jméno.')
            ->addArgument('email', InputArgument::REQUIRED, 'E-mail uživatele.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Vytváření uživatele');
        $output->writeln('============');

        $helper = $this->getHelper('question');
        $question = new Question('Zadejte heslo: ');
        $question->setHidden(true);
        $question->setHiddenFallback(false);

        $password = $helper->ask($input, $output, $question);
        $username = $input->getArgument('username');
        $email = $input->getArgument('email');

        $user = new User();
        $user->setUsername($username)
            ->setEmail($email)
            ->setPlaintextPassword($password)
        ;

        $errors = $this->validator->validate($user, null, ['Default', 'Create']);

        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $output->writeln($error->getMessage());
            }

            return;
        }

        /** @var RoleRepository $repository */
        $repository = $this->em->getRepository(Role::class);
        $adminRole = $repository->findOneByRole('ROLE_ADMIN');

        $password = $this->passwordEncoder->encodePassword($user, $user->getPlaintextPassword());
        $user->setPassword($password);
        $user->addRole($adminRole);

        $this->em->persist($user);
        $this->em->flush();

        $output->writeln(sprintf('Uživatel "%s" s rolí ROLE_ADMIN byl vytvořen', $user->getUsername()));
    }
}
