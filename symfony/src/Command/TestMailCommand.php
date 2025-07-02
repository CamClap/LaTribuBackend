<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class TestMailCommand extends Command
{


    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        parent::__construct();

        $this->mailer = $mailer;
    }

    protected function configure()
    {
        $this->setName('app:test-mail');
        $this->setDescription('Envoie un mail de test');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = (new Email())
            ->from('camille.clappaz@laplateforme.io')
            ->to('camille.clappaz@laplateforme.io')
            ->subject('Test Symfony Mailer')
            ->text('Ceci est un mail de test envoyé depuis Symfony.');

        try {
            $this->mailer->send($email);
            $output->writeln('Mail envoyé avec succès !');
        } catch (\Exception $e) {
            $output->writeln('Erreur lors de l’envoi du mail : ' . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
