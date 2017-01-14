<?php

namespace Slackbot\utility;

class SecurityUtility
{
    const DEFAULT_HASH_ALGORITHM = 'sha1';

    private $hashAlgorithm;

    /**
     * generate a token.
     *
     * @return string
     */
    public function generateToken()
    {
        return hash($this->getHashAlgorithm(), uniqid(mt_rand(), true));
    }

    /**
     * @param $hashAlgorithm
     *
     * @throws \Exception
     */
    public function setHashAlgorithm($hashAlgorithm)
    {
        /*
         * check if hashing algorithm is valid
         */
        if (!in_array($hashAlgorithm, hash_algos(), true)) {
            throw new \Exception('Hash algorithm is not valid');
        }

        $this->hashAlgorithm = $hashAlgorithm;
    }

    /**
     * @return string
     */
    public function getHashAlgorithm()
    {
        if (empty($this->hashAlgorithm)) {
            $this->setHashAlgorithm(self::DEFAULT_HASH_ALGORITHM);
        }

        return $this->hashAlgorithm;
    }
}
