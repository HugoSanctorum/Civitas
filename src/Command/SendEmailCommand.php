<?php

namespace App\Command;

use App\Repository\PersonneRepository;
use App\Repository\ProblemeRepository;
use App\Services\Mailer\MailerService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SendEmailCommand extends Command
{
    protected static $defaultName = 'app:send-email';
    private $mailerService;
    private $problemeRepository;
    private $personneRepository;

    /**
     * SendEmailCommand constructor.
     */
    public function __construct(MailerService $mailerService, ProblemeRepository $problemeRepository, PersonneRepository $personneRepository)
    {
        parent::__construct();
        $this->mailerService = $mailerService;
        $this->problemeRepository = $problemeRepository;
        $this->personneRepository = $personneRepository;
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $probleme = $this->problemeRepository->findOneBy(['id' => 1]);
        $personne = $this->personneRepository->findOneBy(['id' => 1]);
        $this->mailerService->sendMailToSignaleurNewProbleme($personne,$probleme);
        return 0;
    }
}
