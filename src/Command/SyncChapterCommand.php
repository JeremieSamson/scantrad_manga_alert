<?php

namespace App\Command;

use App\Entity\Chapter;
use App\Entity\EmailAlert;
use App\Entity\Manga;
use App\Service\Scraper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\SentMessage;

class SyncChapterCommand extends Command
{
    private $em;
    private $scraper;
    private $mailer;

    public function __construct(EntityManagerInterface $em, Scraper $scraper, MailerInterface $mailer)
    {
        parent::__construct();

        $this->em = $em;
        $this->scraper = $scraper;
        $this->mailer = $mailer;
    }

    protected function configure()
    {
        $this
            ->setName('app:sync:chapter')
            ->setDescription('Sync chapter')
            ->setHelp('This command allows you to sync last chapter from scantrad and create manga if it is not already in the database.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        /** @var \DOMNodeList $chapters */
        $chapters = $this->scraper->getLastChapters();

        $io->note(sprintf('Starting syncing %d chapter(s)', $chapters->count()));

        /** @var \DOMNode $chapter */
        foreach ($chapters as $chapter){
            /** @var \DOMNodeList $nodes */
            $nodes = $chapter->childNodes;

            if ($nodes && $nodes->length >= 7){
                list($mangaName, $chapterName) = explode(PHP_EOL, trim($nodes->item(3)->nodeValue));
                $chapterName = trim($chapterName);
                $chapterNum = trim(substr($mangaName, strrpos($mangaName, ' ', -1), strlen($chapterName)));
                $mangaName = substr($mangaName, 0, strrpos($mangaName, ' ', -1));

                /** @var Manga $manga */
                $manga = $this->em->getRepository('App:Manga')->findOneBy(array("name" => $mangaName));

                if (!$manga){
                    $io->note("Manga not found in database");

                    $manga = new Manga();
                    $manga->setName($mangaName);

                    $this->em->persist($manga);

                    $io->success("Manga $mangaName added");
                } else {
                    $io->note("Manga $mangaName already exists");
                }

                /** @var Chapter $chapter */
                $chapter = $this->em->getRepository('App:Chapter')->findOneBy(array("name" => $chapterName));

                if (!$chapter){
                    $io->note("Chapter not found in database");

                    $chapter = new Chapter();
                    $chapter->setName($chapterName);
                    $chapter->setNumber($chapterNum);

                    $this->em->persist($chapter);

                    $io->success("Chapter $chapterName n°$chapterNum added");

                    /** @var EmailAlert $emailAlert */
                    foreach ($manga->getEmailAlerts() as $emailAlert){
                        $to = $emailAlert->getEmail();

                        $url = strtolower($manga->getName());
                        $url = str_replace(' ', '-', $url);
                        $url = str_replace('.', '', $url);

                        $email = (new TemplatedEmail())
                            ->from('no-reply@scantrad_manga_alert.fr')
                            ->subject("[$mangaName] Chapitre $chapterNum : $chapterName")
                            ->to($to)
                            ->htmlTemplate('email/new_chapter.html.twig')
                            ->context([
                                "manga" => $manga,
                                "chapter" => $chapter,
                                "url" => $url
                            ])
                        ;

                        /** @var SentMessage $sentEmail */
                        $this->mailer->send($email);

                        $io->success("Email sended to $email");
                    }
                }

                if (!$manga->getChapters()->contains($chapter)){
                    $manga->addChapter($chapter);

                    $io->success("Chapter $chapterName added to manga $mangaName");
                }

                try {
                    $this->em->flush();
                }catch (\Exception $e){
                    $io->error($e->getMessage());
                }
            }
        }

        return 0;
    }
}