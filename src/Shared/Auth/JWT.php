<?php
namespace Shared\Auth;

class JWT {
    private static $secret = 'your-secret-key-change-this-in-production';

    private static function base64UrlEncode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private static function base64UrlDecode($data) {
        $remainder = strlen($data) % 4;
        if ($remainder > 0) {
            $data .= str_repeat('=', 4 - $remainder);
        }

        return base64_decode(strtr($data, '-_', '+/'));
    }

    /**
     * Encode JWT token
     */
    public static function encode($payload) {
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        $headerEncoded = self::base64UrlEncode($header);

        $payload['iat'] = time();
        $payload['exp'] = time() + (24 * 60 * 60); // 24 hours
        $payloadEncoded = self::base64UrlEncode(json_encode($payload));

        $signature = hash_hmac('sha256', $headerEncoded . "." . $payloadEncoded, self::$secret, true);
        $signatureEncoded = self::base64UrlEncode($signature);

        return $headerEncoded . "." . $payloadEncoded . "." . $signatureEncoded;
    }

    /**
     * Decode JWT token
     */
    public static function decode($token) {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return false;
        }

        $header = $parts[0];
        $payload = $parts[1];
        $signature = $parts[2];

        $expectedSignature = hash_hmac('sha256', $header . "." . $payload, self::$secret, true);
        $expectedSignatureEncoded = self::base64UrlEncode($expectedSignature);

        if (!hash_equals($signature, $expectedSignatureEncoded)) {
            return false;
        }

        $payloadDecoded = json_decode(self::base64UrlDecode($payload), true);
        if (!is_array($payloadDecoded) || !isset($payloadDecoded['exp']) || !is_numeric($payloadDecoded['exp'])) {
            return false;
        }

        if ((int)$payloadDecoded['exp'] < time()) {
            return false; // Token expired
        }

        return $payloadDecoded;
    }

    /**
     * Get token from Authorization header
     */
    public static function getTokenFromHeader() {
        $authHeader = null;

        if (function_exists('getallheaders')) {
            $headers = getallheaders();
            foreach ($headers as $key => $value) {
                if (strtolower($key) === 'authorization') {
                    $authHeader = trim($value);
                    break;
                }
            }
        }

        if (!$authHeader && !empty($_SERVER['HTTP_AUTHORIZATION'])) {
            $authHeader = trim($_SERVER['HTTP_AUTHORIZATION']);
        }

        if (!$authHeader && !empty($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            $authHeader = trim($_SERVER['REDIRECT_HTTP_AUTHORIZATION']);
        }

        if (!$authHeader && !empty($_SERVER['Authorization'])) {
            $authHeader = trim($_SERVER['Authorization']);
        }

        if ($authHeader && preg_match('/Bearer\s+(.+)$/i', $authHeader, $matches)) {
            return trim($matches[1]);
        }

        return null;
    }

    /**
     * Validate token
     */
    public static function validateToken() {
        $token = self::getTokenFromHeader();
        if (!$token) {
            return false;
        }
        return self::decode($token);
    }
}
