<?php

namespace App\DTOs;

class UserDTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $name,
        public readonly string $first_name,
        public readonly string $second_name,
        public readonly ?string $phone,
        public readonly ?string $birth_date,
        public readonly string $address,
        public readonly string $email,
        public readonly string $dni
    ) {
        //

    }

    public static function fromModel($model): self
    {
        return new self(
            $model->name,
            $model->first_name,
            $model->second_name,
            $model->phone,
            $model->birth_date,
            $model->address,
            $model->email,
            $model->dni
        );
    }
}
