<?php
namespace App\Service;

class JWTService
{
    /**
     * Génération du token JWT / JWT token generation
     *
     * @param array $header
     * @param array $payload
     * @param string $secret
     * @param int $validity
     * @return string
     */
    public function generate(array $header, array $payload, string $secret, int $validity = 3600): string
    {
        if($validity > 0) {
            $now = new \DateTimeImmutable();
            $expiration = $now->getTimestamp() + $validity;

            $payload['iat'] = $now->getTimestamp();
            $payload['exp'] = $expiration;
        }

        // Encodage en Base64 / Base64 encoding
        $base64Header = base64_encode(json_encode($header));
        $base64Payload = base64_encode(json_encode($payload));

        // Nettoyage des valeurs encodées / Cleanup of encoded values
        $base64Header = $this->cleanEncodeValues($base64Header);
        $base64Payload = $this->cleanEncodeValues($base64Payload);

        // Génération de la signature / Signature generation (secret stored in .env.local)
        $secret = base64_encode($secret);
        $signature = hash_hmac('sha256', $base64Header . '.' . $base64Payload, $secret, true);

        $base64Signature = base64_encode($signature);
        $base64Signature = $this->cleanEncodeValues($base64Signature);

        // Création du token / Token creation
        $jwt = $base64Header . '.' . $base64Payload . '.' . $base64Signature;

        return $jwt;
    }

    /**
     * Vérification du token (correctement monté) / Token verification (properly constructed)
     *
     * @param string $token
     * @return bool
     */
    public function isValid(string $token): bool
    {
        return preg_match(
        '/^[a-zA-Z0-9\-\_\=]+\.[a-zA-Z0-9\-\_\=]+\.[a-zA-Z0-9\-\_\=]+$/',
                $token
        ) === 1;
    }

    /**
     * Récupération du Header / Header recovery
     *
     * @param string $token
     * @return array
     */
    public function getHeader(string $token): array
    {
        // Démontage du token / Disassembly of the token
        $tokenArray = $this->disassemblyToken($token);
        // Décodage du Header / Header decoding
        $header = json_decode(base64_decode($tokenArray[0]), true);
        return $header;
    }

    /**
     * Récupération du Payload / Payload recovery
     *
     * @param string $token
     * @return array
     */
    public function getPayload(string $token): array
    {
        // Démontage du token / Disassembly of the token
        $tokenArray = $this->disassemblyToken($token);
        // Décodage du Payload / Payload decoding
        $payload = json_decode(base64_decode($tokenArray[1]), true);
        return $payload;
    }

    /**
     * Vérification de l'expiration du token / Checking token expiration
     *
     * @param string $token
     * @return bool
     */
    public function isExpired(string $token): bool
    {
        $payload = $this->getPayload($token);
        $now = new \DateTimeImmutable();
        return $payload['exp'] < $now->getTimestamp();
    }

    /**
     * Vérification de la signature du token / Verification of the token signature
     *
     * @param string $token
     * @param string $secret
     * @return bool
     */
    public function checkSignature(string $token, string $secret): bool
    {
        // Récupération du Header et du Payload / Header and Payload recovery
        $header = $this->getHeader($token);
        $payload = $this->getPayload($token);

        // Vérification de la signature en regénérant un nouveau token / Verification of the signature by regenereting a new token
        $verifToken = $this->generate($header, $payload, $secret, 0);

        // Comparaison des tokens / Token comparison
        return $token === $verifToken;
    }

    /**
     * Nettoyage des valeurs encodées / Cleanup of encoded values
     *
     * @param $codetoclean
     * @return array|string|string[]
     */
    public function cleanEncodeValues($codetoclean)
    {
        $cleancode = str_replace(['+', '/', '='], ['-', '_', ''], $codetoclean);
        return $cleancode;
    }

    /**
     * Démontage du token / Disassembly of the token
     *
     * @param $token
     * @return string[]
     */
    public function disassemblyToken($token)
    {
        return explode('.', $token);
    }
}

