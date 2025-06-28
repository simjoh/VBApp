<?php

namespace App\common\Service;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

class EmailService extends ServiceAbstract
{
    private PHPMailer $mail;
    private array $config;

    public function __construct($container)
    {
        $this->config = $container->get('settings')['mail'] ?? [];
        $this->initializeMailer();
    }

    public function getPermissions($user_uid): array
    {
        // Email service doesn't require specific permissions
        return [];
    }

    private function initializeMailer(): void
    {
        $this->mail = new PHPMailer(true);
        
        // Server settings
        $this->mail->isSMTP();
        $this->mail->Host = $this->config['mail_host'] ?? 'mailhog';
        $this->mail->Port = $this->config['mail_port'] ?? 1025;
        
        // If you need authentication (for production)
        if (isset($this->config['mail_username']) && $this->config['mail_host'] !== 'mailhog') {
            $this->mail->SMTPAuth = true;
            $this->mail->Username = $this->config['mail_username'];
            $this->mail->Password = $this->config['mail_password'];
            $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        }
        
        // Default sender
        $this->mail->setFrom(
            $this->config['mail_from_address'] ?? 'noreply@vasterbottenbrevet.se',
            $this->config['mail_from_name'] ?? 'Västerbottenbrevet'
        );
    }

    /**
     * Send an email
     * 
     * @param string $to Recipient email address
     * @param string $subject Email subject
     * @param string $body Email body
     * @param bool $isHtml Whether the body is HTML (default true)
     * @param array $attachments Optional array of attachments [['path' => 'path/to/file', 'name' => 'filename']]
     * @return bool Whether the email was sent successfully
     * @throws \Exception If the email could not be sent
     */
    public function sendEmail(
        string $to,
        string $subject,
        string $body,
        bool $isHtml = true,
        array $attachments = []
    ): bool {
        try {
            $this->mail->clearAddresses();
            $this->mail->clearAttachments();
            
            $this->mail->addAddress($to);
            $this->mail->Subject = $subject;
            $this->mail->Body = $body;
            $this->mail->isHTML($isHtml);

            // Add attachments if any
            foreach ($attachments as $attachment) {
                if (isset($attachment['path'])) {
                    $this->mail->addAttachment(
                        $attachment['path'],
                        $attachment['name'] ?? ''
                    );
                }
            }

            return $this->mail->send();
        } catch (Exception $e) {
            throw new \Exception("Email could not be sent. Mailer Error: {$this->mail->ErrorInfo}");
        }
    }

    /**
     * Send an email with template
     * 
     * @param string $to Recipient email address
     * @param string $subject Email subject
     * @param string $template Template file name from templates directory
     * @param array $data Data to be passed to the template
     * @param array $attachments Optional array of attachments
     * @return bool Whether the email was sent successfully
     * @throws \Exception If the template doesn't exist or email could not be sent
     */
    public function sendEmailWithTemplate(
        string $to,
        string $subject,
        string $template,
        array $data = [],
        array $attachments = []
    ): bool {
        // Try Docker path first, then fall back to relative path
        $templatePath = '/var/www/html/api/templates/' . $template;
        if (!file_exists($templatePath)) {
            $templatePath = __DIR__ . '/../../../../templates/' . $template;
        }
        
        if (!file_exists($templatePath)) {
            throw new \Exception("Email template not found: {$template} (tried Docker path and relative path)");
        }

        // Extract data to make variables available in template
        extract($data);
        
        // Start output buffering
        ob_start();
        include $templatePath;
        $body = ob_get_clean();

        return $this->sendEmail($to, $subject, $body, true, $attachments);
    }
} 