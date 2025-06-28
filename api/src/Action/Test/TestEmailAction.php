<?php

namespace App\Action\Test;

use App\common\Action\BaseAction;
use App\common\Service\EmailService;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class TestEmailAction extends BaseAction
{
    private EmailService $emailService;
    private array $settings;

    public function __construct(EmailService $emailService, ContainerInterface $container)
    {
        $this->emailService = $emailService;
        $this->settings = $container->get('settings');
    }

    public function __invoke(Request $request, Response $response): Response
    {
        try {
            $result = $this->emailService->sendEmailWithTemplate(
                'bethem92@gmail.com',
                'Test Email from Västerbottenbrevet API',
                'basic-email-template.php',
                [
                    'subject' => 'Test Email from Västerbottenbrevet API',
                    'content' => '<p>Detta är ett testmeddelande från Västerbottenbrevet API.</p>
                                <p>Om du ser detta meddelande fungerar e-postfunktionen korrekt!</p>
                                <p>Timestamp: ' . date('Y-m-d H:i:s') . '</p>'
                ]
            );

            $response->getBody()->write($this->json_encode_private([
                'success' => $result,
                'message' => 'Email sent successfully'
            ]));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write($this->json_encode_private([
                'error' => $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }
} 