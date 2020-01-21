<?php

namespace App\Controller;

use App\Entity\EmailAlert;
use App\Entity\Manga;
use App\Repository\MangaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $mangaId = $request->get('manga');
        $email   = $request->get('email');

        /** @var MangaRepository $mangaRepository */
        $mangaRepository = $this->getDoctrine()->getRepository('App:Manga');

        /** @var Manga $manga */
        $manga = $mangaId ? $mangaRepository->find($mangaId) : null;

        if ($request->get('manga') != null && !$manga)
            $this->addFlash('danger', 'Le manga choisit n\'est pas correct');

        if ($email != null){
            if (!filter_var($email, FILTER_VALIDATE_EMAIL))
                $this->addFlash('danger', 'Le format de l\'email n\'est pas correct');
            elseif($manga){
                /** @var EmailAlert $emailAlert */
                $emailAlert = $this->getDoctrine()->getRepository('App:EmailAlert')->findOneBy(array("email" => $email));

                if (!$emailAlert){
                    $emailAlert = new EmailAlert();
                    $emailAlert->setEmail($email);
                }

                if ($manga->getEmailAlerts()->contains($emailAlert)){
                    $this->addFlash('danger', "L'email $email est déjà abonné au manga " . $manga->getName());
                } else {
                    $this->getDoctrine()->getManager()->persist($emailAlert);

                    $manga->addEmailAlert($emailAlert);

                    $this->getDoctrine()->getManager()->flush();

                    $this->addFlash('success', $email . ' sera bien alerté par email des nouveaux chapitres du manga ' . $manga->getName());
                }
            }
        } elseif ($request->query->has('email') && empty($email)) {
            $this->addFlash('danger', "L'email ne peut pas être vide");
        }

        return $this->render('default/index.html.twig', [
            'mangas' => $this->getDoctrine()->getRepository('App:Manga')->findBy(array(), array("name" => "ASC")),
            'mangaSelected' => $manga
        ]);
    }

    /**
     * @Route("/unsubscribe/{mangaId}/{emailId}", name="unsubscribe")
     */
    public function unsubscribeAction(Request $request, int $mangaId, int $emailId)
    {
        $emailAlert = $this->getDoctrine()->getRepository(EmailAlert::class)->find($emailId);

        if (!$emailAlert) {
            $this->createNotFoundException();
        }

        $manga = $this->getDoctrine()->getRepository(Manga::class)->find($mangaId);

        if (!$manga) {
            $this->createNotFoundException();
        }

        if (!$emailAlert->getMangas()->c)

        $this->addFlash('success', sprintf('Vous vous êtes bien désabonne des alertes sur le manga %s', $manga->getName()));

        return $this->redirectToRoute('homepage');
    }
}
