<?php

namespace App\Application\ServiceOrders\Data;

final readonly class ServiceOrderData
{
    public function __construct(
        public string $title,
        public ?string $location,
        public int $categoryId,
        public ?string $dueDate,
        public bool $isUrgent,
        public ?string $observation,
    ) {
    }

    /**
     * @param  array{
     *     title:string,
     *     location:string|null,
     *     category_id:int|string,
     *     due_date:string|null,
     *     is_urgent:bool,
     *     observation:string|null
     * }  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            title: $data['title'],
            location: self::normalizeNullableString($data['location'] ?? null),
            categoryId: (int) $data['category_id'],
            dueDate: self::normalizeNullableString($data['due_date'] ?? null),
            isUrgent: (bool) $data['is_urgent'],
            observation: self::normalizeNullableString($data['observation'] ?? null),
        );
    }

    /**
     * @return array{
     *     title:string,
     *     location:string|null,
     *     category_id:int,
     *     due_date:string|null,
     *     is_urgent:bool,
     *     observation:string|null
     * }
     */
    public function toPersistenceArray(): array
    {
        return [
            'title' => $this->title,
            'location' => $this->location,
            'category_id' => $this->categoryId,
            'due_date' => $this->dueDate,
            'is_urgent' => $this->isUrgent,
            'observation' => $this->observation,
        ];
    }

    private static function normalizeNullableString(?string $value): ?string
    {
        $trimmed = $value === null ? null : trim($value);

        return $trimmed === '' ? null : $trimmed;
    }
}
