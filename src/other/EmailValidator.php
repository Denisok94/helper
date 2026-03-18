<?php

namespace denisok94\helper\other;

/**
 * Class EmailValidator
 * @package denisok94\helper\other
 * 
 * ```php
 * $validator = new EmailValidator();
 * $errorsEmail = $validator->validate($user->email);
 * if (empty($errorsEmail)) {
 *   // ok
 * }
 * ```
 */
class EmailValidator
{
    /**
     * Валидация почты
     * @param string $email
     * @return string[]
     */
    public function validate(string $email): array
    {
        $errors = [];

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Please enter a valid email address.";
            return $errors;
        }

        $domain = substr(strrchr($email, "@"), 1);
        if (!$this->domainExists($domain)) {
            $errors[] = "The domain of this email does not exist. Check the spelling.";
            return $errors;
        }

        if (!$this->hasMxRecord($domain)) {
            $errors[] = "This email domain does not accept emails (no mail server).";
        }

        return $errors;
    }

    private function domainExists($domain): bool
    {
        return checkdnsrr($domain, 'A') || checkdnsrr($domain, 'AAAA');
    }

    private function hasMxRecord($domain): bool
    {
        return checkdnsrr($domain, 'MX');
    }
}
