<?php

namespace App\Services\Mailer;

use App\Entity\Personne;
use Swift_Mailer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MailerService extends AbstractController
{
    private $mailer;

    public function __construct(Swift_Mailer $mailer )
    {
        $this->mailer = $mailer;
    }

    public function sendMailToSignaleurNewProbleme(Personne $destinataire, $probleme){
        $message = (new \Swift_Message('Nouveau probleme'))
            ->setFrom('CivitasNotification@gmail.com')
            ->setTo($destinataire->getMail())
            ->addPart(
                $this->renderView(
                    'email/notifNouveauProbleme.html.twig',
                    [
                        "probleme"=> $probleme,
                        "personne"=>$destinataire,
                    ]),
                'text/html'
            );
        $this->mailer->send($message);
    }
    public function sendMailToTechnicienAffectedProbleme(Personne $destinataire,$probleme){
        $message = (new \Swift_Message('Probleme affecté'))
            ->setFrom('civitasnotification@gmail.com')
            ->setTo($destinataire->getMail())
            ->addPart(
                $this->renderView(
                    'email/notifNouvelleIntervention.html.twig',
                    [
                        "probleme" => $probleme,
                        "technicien" => $destinataire
                    ]),
                'text/html'
            );
        $this->mailer->send($message);

    }
    public function sendMailToSignaleurAffectedProbleme(Personne $destinataire, $probleme)
    {
        $message = (new \Swift_Message('Probleme affecté'))
            ->setFrom('civitasnotification@gmail.com')
            ->setTo($destinataire->getMail())
            ->addPart(
                $this->renderView('email/notifProblemeAffecte.html.twig',
                    [
                        "probleme" => $probleme,
                        "signaleur" => $destinataire
                    ]),
                'text/html'
            );
        $this->mailer->send($message);
    }
}