<?php
if(!function_exists('base64URLEncode')) {
    function base64URLEncode($str): string
    {
        return rtrim(strtr(base64_encode($str), '+/', '-_'), '=');
    }
}

if(!function_exists('encodeJWT')) {
    function encodeJWT(array $payload, string $secret, array $header = ['typ' => 'JWT', 'alg' => 'HS256']): string
    {
        if (isset($payload['sub']) && isset($payload['exp'])) {
            $headerEncoded = base64URLEncode(json_encode($header));
            $payloadEncoded = base64URLEncode(json_encode($payload));
            $signature = hash_hmac('SHA256', "$headerEncoded.$payloadEncoded", $secret, true);
            $signatureEncoded = base64URLEncode($signature);
            return "$headerEncoded.$payloadEncoded.$signatureEncoded";
        } else {
            throw new Exception('jwt error: empty payload!', 204);
        }
    }
}

if(!function_exists('validateJWT')) {
    /**
     * @throws Exception
     */
    function validateJWT($jwt, $secret): string
    {
        $tokenParts = explode('.', $jwt);
        $header = base64_decode($tokenParts[0]);
        $payload = base64_decode($tokenParts[1]);
        $signatureProvided = $tokenParts[2];

        $expiration = json_decode($payload)->exp;
        $isTokenExpired = ($expiration - time()) < 0;

        $base64URLHeader = base64URLEncode($header);
        $base64URLPayload = base64URLEncode($payload);
        $signature = hash_hmac('SHA256', $base64URLHeader . "." . $base64URLPayload, $secret, true);
        $base64URLSignature = base64URLEncode($signature);
        $isSignatureNotValid = !($base64URLSignature === $signatureProvided);

        if ($isTokenExpired || $isSignatureNotValid)
            throw new Exception('jwt error: invalid token!', 401);

        return $payload;
    }
}