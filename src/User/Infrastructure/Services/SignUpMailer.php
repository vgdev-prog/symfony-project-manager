<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Services;

use App\Shared\Domain\ValueObject\Email;
use App\User\Domain\Contract\UserMailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

readonly class SignUpMailer implements UserMailerInterface
{
    public function __construct(
        private MailerInterface       $mailer,
        private UrlGeneratorInterface $urlGenerator,
        private string                $senderMail,
    )
    {
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function sendConfirmation(Email $email, string $token): void
    {
        $confirmUrl = $this->urlGenerator->generate(
            'auth.signup.confirm',
            ['token' => $token],
            UrlGeneratorInterface::ABSOLUTE_URL,
        );

        $message = (new TemplatedEmail())
            ->from($this->senderMail)
            ->to($email->getValue())
            ->subject('Подтверждение регистрации')
            ->htmlTemplate('@User/email/signup_confirmation.html.twig')
            ->context([
                'confirmUrl' => $confirmUrl
            ]);

        $this->mailer->send($message);
    }

    public function resetToken(Email $email):void
    {

    }
}
