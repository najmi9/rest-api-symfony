<?php

declare(strict_types=1);

namespace App\Services;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

/**
 * send mails.
 */
class MailService
{
    private $mailer;
    private $from;

    public function __construct(MailerInterface $mailer, string $from)
    {
        $this->mailer = $mailer;
        $this->from = $from;
    }

    /**
     * Send Email.
     *
     * @param string  $to       the recepiant
     * @param string  $subject  email ubject
     * @param string  $template email template
     * @param mixed[] $context  variables in the template
     * @param string  $replyTo
     */
    public function sendEmail(string $to, string $subject, string $template, array $context): void
    {
        $from = $this->from;

        $email = new TemplatedEmail();
        $email
            ->from($from)
            ->to($to)
            ->subject($subject)
            ->htmlTemplate($template)
            ->context($context)
        ;
      
        $this->mailer->send($email);
    }

    public function sendCode(string $to, string $subject, string $template): int
    {
        $code = random_int(111111, 999999);
        //$this->sendEmail($to, $subject, $template, ['code' => $code]);

        return $code;
    }
}
