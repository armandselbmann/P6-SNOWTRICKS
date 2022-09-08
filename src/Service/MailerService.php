<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class MailerService
{
    private $mailer;
    private string $adminEmail;

    public function __construct(MailerInterface $mailer, $adminEmail)
    {
        $this->mailer = $mailer;
        $this->adminEmail = $adminEmail;
    }

    public function sendEmail(
            $to,
            $subject,
            $template,
            $context
    ): void
    {
        $email = (new TemplatedEmail())
            ->from($this->adminEmail)
            ->to($to)
            ->subject($subject)
            ->htmlTemplate("emails/$template.html.twig")
            ->context($context);

        $this->mailer->send($email);
    }
}