<?php

namespace ArmelWanes\Crudify\Helper;

use Symfony\Component\Templating\EngineInterface;
use Twig\Environment;
class Mailer
{
    private $engine;
    private $mailer;

    public function __construct(\Swift_Mailer $mailer, Environment $engine)
    {
        $this->engine = $engine;
        $this->mailer = $mailer;
    }

    public function sendMessage($from, $to, $subject, $body, $attachement = null,$priority=null)
    {
        $mail = (new \Swift_Message($subject))
            ->setFrom($from)
            ->setTo($to)
            ->setPriority($priority)
            ->setSubject($subject)
            ->setBody($body)
            ->setReplyTo($from)
            ->setContentType('text/html');

        $this->mailer->send($mail);
    }

    public function createBodyMail($view, array $parameters)
    {
        return $this->engine->render($view, $parameters);
    }
}
