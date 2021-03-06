<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    /**
     * @Route("/contact", name="contact")
     */
    public function contactAction(Request $request)
    {
        $email = $request->get('email');
        $subject = $request->get('subject');
        $message = $request->get('message');

        if ($request->query->has('email') && $request->query->has('subject') && $request->query->has('message')){
            if (empty($email) || empty($subject)){
                $this->addFlash('danger', 'Les trois champs sont obligatoire');
            } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
                $this->addFlash('danger', 'Le format de l\'email n\'est pas correct');
            } else{
                $message = (new \Swift_Message("[ScantradAlert] $subject"))
                    ->setFrom($email)
                    ->setTo($this->getParameter('admin_email'))
                    ->setBody(
                        $this->render(
                            'email/new_contact.html.twig',
                            array(
                                "email" => $email,
                                "message" => $message
                            )
                        ),
                        'text/html'
                    )
                ;

                $res = $this->get('mailer')->send($message);

                if ($res)
                    $this->addFlash('success', 'Votre message à bien été envoyé');
                else
                    $this->addFlash('danger', 'Une erreur est survenue pendant l\'envoie de votre message');
            }
        }

        return $this->render('contact/index.html.twig', array());
    }
}
