<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../config/mail_config.php';

class VerificationService {
    private $db;
    private $mailConfig;

    public function __construct() {
        $this->db = config::getConnexion();
        $this->mailConfig = new MailConfig();
    }

    public function createVerificationToken($userId) {
        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', strtotime('+24 hours'));

        $sql = "INSERT INTO email_verification_tokens (user_id, token, expires_at) VALUES (:user_id, :token, :expires_at)";
        $query = $this->db->prepare($sql);
        $query->execute([
            'user_id' => $userId,
            'token' => $token,
            'expires_at' => $expiresAt
        ]);

        return $token;
    }

    public function sendVerificationEmail($userId, $userEmail) {
        try {
            $token = $this->createVerificationToken($userId);
            $verificationLink = "http://localhost/projects/try3/views/user/front_office/verify_email.php?token=" . $token;

            if (!$this->mailConfig->sendVerificationEmail($userEmail, $verificationLink)) {
                throw new Exception('Failed to send verification email');
            }
            return true;
        } catch (Exception $e) {
            error_log('Verification email error: ' . $e->getMessage());
            throw new Exception($e->getMessage());
        }
    }

    public function verifyEmail($token) {
        try {
            // Check if token exists and is valid
            $sql = "SELECT user_id FROM email_verification_tokens WHERE token = :token AND expires_at > NOW() AND used = 0";
            $query = $this->db->prepare($sql);
            $query->execute(['token' => $token]);
            $result = $query->fetch();

            if ($result) {
                // Update user's verified status
                $updateSql = "UPDATE user SET verified = 1 WHERE user_id = :user_id";
                $updateQuery = $this->db->prepare($updateSql);
                $updateQuery->execute(['user_id' => $result['user_id']]);

                // Mark token as used
                $markUsedSql = "UPDATE email_verification_tokens SET used = 1 WHERE token = :token";
                $markUsedQuery = $this->db->prepare($markUsedSql);
                $markUsedQuery->execute(['token' => $token]);

                return true;
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }
}