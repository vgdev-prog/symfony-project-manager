<?php

declare(strict_types=1);

namespace App\Model\User\Services;

use App\Model\User\Contracts\SignUpConfirmEmailSenderInterface;
use App\Model\User\ValueObject\Email;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Mime\Email as MimeEmail;

class SignUpConfirmEmailSender implements SignUpConfirmEmailSenderInterface
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly UrlGeneratorInterface $urlGenerator,
    ) {}

    /**
     * @throws TransportExceptionInterface
     */
    public function send(Email $email, string $token): void
    {
        $confirmUrl = $this->urlGenerator->generate(
            'auth.signup.confirm',
            ['token' => $token],
            UrlGeneratorInterface::ABSOLUTE_URL,
        );

        $message = (new MimeEmail())
            ->from('noreply@yourapp.com')
            ->to($email->getValue())
            ->subject('Подтверждение регистрации')
            ->html($this->buildHtml($confirmUrl));

        $this->mailer->send($message);
    }

    private function buildHtml(string $confirmUrl): string
    {
        return <<<HTML
              <h1>Добро пожаловать!</h1>
              <p>Для подтверждения регистрации перейдите по ссылке:</p>
              <p><a href="{$confirmUrl}">{$confirmUrl}</a></p>
              <p>Ссылка действительна 24 часа.</p>
          HTML;
    }


}
