<?php

namespace AppBundle\Command;

use AppBundle\Entity\Chapter;
use AppBundle\Entity\EmailAlert;
use AppBundle\Entity\Manga;
use AppBundle\Service\Scraper;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Validator\Constraints\Email;

class SyncChapterCommand  extends ContainerAwareCommand
{
    /**
     * Configure
     */
    protected function configure()
    {
        $this
            ->setName('sync:chapter')
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
        /** @var EntityManager $em */
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        /** @var Scraper $scraper */
        $scraper = $this->getContainer()->get('scraper');

        /** @var \DOMElement $chapters */
        $chapters = $scraper->getLastChapters();

        /** @var \DOMElement $chapter */
        foreach ($chapters->childNodes as $chapter){
            /** @var \DOMNodeList $nodes */
            $nodes = $chapter->childNodes;

            if ($nodes && $nodes->length >= 7){
                list($mangaName, $chapterNum) = explode(PHP_EOL, trim($nodes->item(1)->nodeValue));
                list($chapterName, $time) = explode(PHP_EOL, trim($nodes->item(7)->nodeValue));

                /** @var Manga $manga */
                $manga = $em->getRepository('AppBundle:Manga')->findOneBy(array("name" => $mangaName));

                if (!$manga){
                    $manga = new Manga();
                    $manga->setName($mangaName);

                    $em->persist($manga);

                    $output->writeln("<comment>Manga $mangaName added</comment>");
                }

                /** @var Chapter $chapter */
                $chapter = $em->getRepository('AppBundle:Chapter')->findOneBy(array("name" => $chapterName));

                if (!$chapter){
                    $chapter = new Chapter();
                    $chapter->setName($chapterName);
                    $chapter->setNumber($chapterNum);

                    $em->persist($chapter);

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
                    $em->flush();
                }catch (\Exception $e){
                    $output->writeln("<error>" .$e->getMessage(). "</error>");
                }
            }
        }
    }

}