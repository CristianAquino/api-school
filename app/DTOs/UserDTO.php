<?php

namespace App\DTOs;

class UserDTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $name,
        public readonly ?string $first_name,
        public readonly ?string $second_name,
        public readonly ?string $phone,
        public readonly ?string $birth_date,
        public readonly ?string $address,
        public readonly ?string $email,
        public readonly ?string $dni,
        public readonly string $code
    ) {
        //

    }

    public static function fromPartialModel($model): array
    {
        $user = self::fromBaseModel($model->user);

        return [
            'id' => $model->id,
            'names' => $user->name,
            'first_name' => $user->first_name,
            'second_name' => $user->second_name,
            'code' => $user->code
        ];
    }

    public static function fromPagination($model): array
    {
        return [
            'data' => self::fromPaginationCollection($model->items()),
            'pagination' => PaginationDTO::base($model)
        ];
    }

    public static function fromPaginationCollection($collections): array
    {
        return array_map(function ($collection) {
            return self::fromPartialModel($collection);
        }, $collections);
    }

    public static function fromBaseModel($model): self
    {
        return new self(
            $model->name,
            $model->first_name,
            $model->second_name,
            $model->phone,
            $model->birth_date,
            $model->address,
            $model->email,
            $model->dni,
            $model->code
        );
    }
}
