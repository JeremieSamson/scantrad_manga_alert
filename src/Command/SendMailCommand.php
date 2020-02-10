<?php

namespace App\Command;

use App\Entity\Chapter;
use App\Entity\EmailAlert;
use App\Entity\Manga;
use App\Service\Scraper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class SendMailCommand extends Command
{
    private $mailer;
    private $sender;
    private $generator;

    public function __construct(MailerInterface $mailer, ParameterBagInterface $bag, UrlGeneratorInterface $generator)
    {
        parent::__construct();

        $this->mailer = $mailer;
        $this->sender = $bag->get('MAILER_FROM');
        $this->generator = $generator;
    }

    protected function configure()
    {
        $this
            ->setName('app:mail:send')
            ->setDescription('Send email')
            ->addArgument('to', InputArgument::REQUIRED,  'To')
            ->addOption('subject', 's', InputArgument::OPTIONAL, 'Subject')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        print_r($_ENV);

        $manga = new Manga();
        $manga->setName("[Manga name]");

        $chapter = new Chapter();
        $chapter->setName("[Chapter name]");
        $chapter->setNumber(42);

        $url = "scantrad.org";

        $email = (new TemplatedEmail())
            ->from('no-reply@scantrad_manga_alert.fr')
            ->subject($input->getOption('subject') ?? "[Chapter name] Chapitre 42 : [Chapter name]")
            ->to($input->getArgument('to'))
            ->htmlTemplate('email/new_chapter.html.twig')
            ->context([
                "manga" => $manga,
                "chapter" => $chapter,
                "url" => $url,
                'unsubscribe_url' => $this->generator->generate(
                    'unsubscribe',
                    [
                        'mangaId' => 1,
                        'emailId' => 1
                    ],
                    UrlGeneratorInterface::ABSOLUTE_URL
                )
            ])
        ;

        /** @var SentMessage $sentEmail */
        $this->mailer->send($email);

        $io->success("Mail sended");

        return 0;
    }
}