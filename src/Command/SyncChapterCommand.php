<?php

namespace App\Command;

use App\Entity\Chapter;
use App\Entity\EmailAlert;
use App\Entity\Manga;
use App\Service\Scraper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SyncChapterCommand extends Command
{
    private $em;
    private $scraper;

    public function __construct(EntityManagerInterface $em, Scraper $scraper)
    {
        parent::__construct();

        $this->em = $em;
        $this->scraper = $scraper;
    }

    protected function configure()
    {
        $this
            ->setName('app:sync:chapter')
            ->setDescription('Sync chapter')
            ->setHelp('This command allows you to sync last chapter from scantrad and create manga if it is not already in the database.')
        ;
    }

    /**
     * Execute command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var \DOMNodeList $chapters */
        $chapters = $this->scraper->getLastChapters();

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
                    $manga = new Manga();
                    $manga->setName($mangaName);

                    $this->em->persist($manga);

                    $output->writeln("<comment>Manga $mangaName added</comment>");
                }

                /** @var Chapter $chapter */
                $chapter = $this->em->getRepository('App:Chapter')->findOneBy(array("name" => $chapterName));

                if (!$chapter){
                    $chapter = new Chapter();
                    $chapter->setName($chapterName);
                    $chapter->setNumber($chapterNum);

                    $this->em->persist($chapter);

                    $output->writeln("<comment>Chapter $chapterName added</comment>");

                    /** @var EmailAlert $emailAlert */
                    foreach ($manga->getEmailAlerts() as $emailAlert){
                        $email = $emailAlert->getEmail();

                        $url = strtolower($manga->getName());
                        $url = str_replace(' ', '-', $url);
                        $url = str_replace('.', '', $url);

                        $message = (new \Swift_Message("[$mangaName] Chapitre $chapterNum : $chapterName"))
                            ->setFrom('no-reply@scantrad_manga_alert.fr')
                            ->setTo($email)
                            ->setBody(
                                $this->getContainer()->get('twig')->render(
                                    'email/new_chapter.html.twig',
                                    array(
                                        "manga" => $manga,
                                        "chapter" => $chapter,
                                        "url" => $url
                                    )
                                ),
                                'text/html'
                            )
                        ;

                        $res = $this->getContainer()->get('mailer')->send($message);

                        if ($res)
                            $output->writeln("<comment>Email sended to $email</comment>");
                        else
                            $output->writeln("<error>Email not sended to $email</error>");
                    }
                }

                if (!$manga->getChapters()->contains($chapter)){
                    $manga->addChapter($chapter);

                    $output->writeln("<comment>Chapter $chapterName added to manga $mangaName</comment>");
                }

                try {
                    $this->em->flush();
                }catch (\Exception $e){
                    $output->writeln("<error>" .$e->getMessage(). "</error>");
                }
            }
        }

        return 0;
    }
}