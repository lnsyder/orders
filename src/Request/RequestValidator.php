<?php

namespace App\Request;

use Symfony\Component\Validator\Validator\ValidatorInterface;

readonly class RequestValidator
{
    /**
     * @param ValidatorInterface $validator
     */
    public function __construct(
        private ValidatorInterface $validator
    ) {}

    /**
     * @param object $request
     * @return void
     * @throws \JsonException
     */
    public function validate(object $request): void
    {
        $violations = $this->validator->validate($request);

        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[] = [
                    'property' => $violation->getPropertyPath(),
                    'message' => $violation->getMessage(),
                ];
            }
            throw new \InvalidArgumentException(json_encode($errors, JSON_THROW_ON_ERROR));
        }
    }
}