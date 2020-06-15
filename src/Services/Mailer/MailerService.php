<?php

namespace App\Services\Mailer;

use App\Entity\Personne;
use App\Entity\Probleme;
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
            $message = (new \Swift_Message('Votre problème a été signalé !'))
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
            $message = (new \Swift_Message('Vous avez été affecté à un problème !'))
                ->setFrom('civitasnotification@gmail.com')
                ->setTo($destinataire->getMail())
                ->addPart(
                    $this->renderView(
                        'email/notifNouvelleIntervention.html.twig',
                        [
                            "probleme" => $probleme,
                            "technicien" => $destinataire,
                            "subscribeToken" => $destinataire->getSubscribeToken()
                        ]),
                    'text/html'
                );
            $this->mailer->send($message);
    }
    public function sendMailToSignaleurAffectedProbleme(Personne $destinataire, $probleme)
    {
        if($destinataire->getSubscribeToken() != null) {
            $message = (new \Swift_Message('Votre problème a été affecté à un technicien'))
                ->setFrom('civitasnotification@gmail.com')
                ->setTo($destinataire->getMail())
                ->addPart(
                    $this->renderView('email/notifProblemeAffecte.html.twig',
                        [
                            "probleme" => $probleme,
                            "signaleur" => $destinataire,
                            "subscribeToken" => $destinataire->getSubscribeToken(),
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
    public function sendMailSignaleurProblemeOuvert(Personne $signaleur, Probleme $probleme){
        if($signaleur->getSubscribeToken() != null) {
            $message = (new \Swift_Message('Votre probleme a été validé !'))
                ->setFrom('civitasnotification@gmail.com')
                ->setTo($signaleur->getMail())
                ->addPart(
                    $this->renderView('email/notifProblemeOuvert.html.twig',
                        [
                            "probleme" => $probleme,
                            "signaleur" => $signaleur,
                            "subscribeToken" => $signaleur->getSubscribeToken(),
                        ]),
                    'text/html'
                );
            $this->mailer->send($message);
        }
    }
}