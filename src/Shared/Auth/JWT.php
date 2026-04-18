<?php
namespace Shared\Auth;

class JWT {
    private static $secret = 'your-secret-key-change-this-in-production';

    /**
     * Encode JWT token
     */
    public static function encode($payload) {
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        $headerEncoded = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));

        $payload['iat'] = time();
        $payload['exp'] = time() + (24 * 60 * 60); // 24 hours
        $payloadEncoded = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode(json_encode($payload)));

        $signature = hash_hmac('sha256', $headerEncoded . "." . $payloadEncoded, self::$secret, true);
        $signatureEncoded = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

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
        $expectedSignatureEncoded = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($expectedSignature));

        if (!hash_equals($signature, $expectedSignatureEncoded)) {
            return false;
        }

        $payloadDecoded = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $payload)), true);

        if ($payloadDecoded['exp'] < time()) {
            return false; // Token expired
        }

        return $payloadDecoded;
    }

    /**
     * Get token from Authorization header
     */
    public static function getTokenFromHeader() {
        $headers = getallheaders();
        if (isset($headers['Authorization'])) {
            $authHeader = $headers['Authorization'];
            if (preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
                return $matches[1];
            }
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