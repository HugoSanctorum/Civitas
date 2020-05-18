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
        if($destinataire->getSubscribeToken() != null) {
            $message = (new \Swift_Message('Nouveau probleme'))
                ->setFrom('CivitasNotification@gmail.com')
                ->setTo($destinataire->getMail())
                ->addPart(
                    $this->renderView(
                        'email/notifNouveauProbleme.html.twig',
                        [
                            "probleme" => $probleme,
                            "personne" => $destinataire,
                            "subscribeToken" => $destinataire->getSubscribeToken(),
                        ]),
                    'text/html'
                );
            $this->mailer->send($message);
        }
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
        if($destinataire->getSubscribeToken() != null) {
            $message = (new \Swift_Message('Probleme affecté'))
                ->setFrom('civitasnotification@gmail.com')
                ->setTo($destinataire->getMail())
                ->addPart(
                    $this->renderView('email/notifProblemeAffecte.html.twig',
                        [
                            "probleme" => $probleme,
                            "signaleur" => $destinataire,
                            "unsubscribeToken" => $destinataire->getSubscribeToken(),
                        ]),
                    'text/html'
                );
            $this->mailer->send($message);
        }
    }

    public function sendMailActivatedAccount(Personne $personne, String $token){
        $message= (new \Swift_Message('Nouveau compte'))
            ->setFrom('civitasnotification@gmail.com')
            ->setTo($personne->getMail())
            ->addPart(
                $this->renderView('email/activatedAccount.html.twig',
                    [
                    "personne" => $personne,
                    "token" => $token
                    ]),
                'text/html'
            );
        $this->mailer->send($message);
    }
    public function sendMailResetPassword(Personne $personne, String $token){
        $message= (new \Swift_Message('Réinitialisation du mot de passe'))
            ->setFrom('civitasnotification@gmail.com')
            ->setTo($personne->getMail())
            ->addPart(
                $this->renderView('email/resetPassword.html.twig',
                    [
                        "personne" => $personne,
                        "resetPasswordToken" => $token
                    ]),
                'text/html'
            );
        $this->mailer->send($message);
    }
    public function sendMailPasswordChanged(Personne $personne){
        $message= (new \Swift_Message('Confirmation du changement de mot de passe'))
            ->setFrom('civitasnotification@gmail.com')
            ->setTo($personne->getMail())
            ->addPart(
                $this->renderView('email/changedPassword.html.twig',
                    [
                        "personne" => $personne,
                    ]),
                'text/html'
            );
        $this->mailer->send($message);
    }
}