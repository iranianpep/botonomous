<?php

namespace Botonomous\utility;

use Botonomous\BotonomousException;

/**
 * Class SecurityUtility.
 */
class SecurityUtility
{
    const DEFAULT_HASH_ALGORITHM = 'sha1';

    private $hashAlgorithm;

    /**
     * generate a token.
     *
     * @return string
     * @throws \Exception
     */
    public function generateToken(): string
    {
        return hash($this->getHashAlgorithm(), uniqid(mt_rand(), true));
    }

    /**
     * @param string $hashAlgorithm
     *
     * @throws \Exception
     */
    public function setHashAlgorithm(string $hashAlgorithm)
    {
        /*
         * check if hashing algorithm is valid
         */
        if (!in_array($hashAlgorithm, hash_algos(), true)) {
            throw new BotonomousException('Hash algorithm is not valid');
        }

        $this->hashAlgorithm = $hashAlgorithm;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getHashAlgorithm(): string
    {
        if (empty($this->hashAlgorithm)) {
            $this->setHashAlgorithm(self::DEFAULT_HASH_ALGORITHM);
        }

        return $this->hashAlgorithm;
    }
}
