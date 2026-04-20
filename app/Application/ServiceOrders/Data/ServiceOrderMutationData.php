<?php

namespace App\Application\ServiceOrders\Data;

use App\Models\ServiceOrder;

abstract readonly class ServiceOrderMutationData
{
    /**
     * @param  list<ChecklistItemData>  $checklistItems
     */
    public function __construct(
        public string $title,
        public ?string $location,
        public int $categoryId,
        public ?string $dueDate,
        public bool $isUrgent,
        public ?string $observation,
        public array $checklistItems = [],
    ) {}

    /**
     * @param  array{
     *     title:string,
     *     location:string|null,
     *     category_id:int|string,
     *     due_date:string|null,
     *     is_urgent:bool,
     *     observation:string|null,
     *     checklist_items?:array<int, array{label?:string|null,is_completed?:bool,sort_order?:int}>
     * }  $data
     */
    public static function fromArray(array $data): static
    {
        return new static(
            title: trim($data['title']),
            location: self::normalizeNullableString($data['location'] ?? null),
            categoryId: (int) $data['category_id'],
            dueDate: self::normalizeNullableString($data['due_date'] ?? null),
            isUrgent: (bool) $data['is_urgent'],
            observation: self::normalizeNullableString($data['observation'] ?? null),
            checklistItems: self::normalizeChecklistItems($data['checklist_items'] ?? []),
        );
    }

    public static function fromServiceOrder(ServiceOrder $serviceOrder): static
    {
        return new static(
            title: $serviceOrder->title,
            location: self::normalizeNullableString($serviceOrder->location),
            categoryId: $serviceOrder->category_id,
            dueDate: $serviceOrder->due_date?->format('Y-m-d'),
            isUrgent: (bool) $serviceOrder->is_urgent,
            observation: self::normalizeNullableString($serviceOrder->observation),
            checklistItems: array_values(
                $serviceOrder->checklistItems
                    ->map(fn ($item) => ChecklistItemData::fromModel($item))
                    ->all(),
            ),
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

    /**
     * @return list<array{label:string,is_completed:bool,sort_order:int}>
     */
    public function checklistItemsForPersistence(): array
    {
        return array_map(
            fn (ChecklistItemData $item) => $item->toPersistenceArray(),
            $this->checklistItems,
        );
    }

    /**
     * @return array{
     *     title:string,
     *     location:string,
     *     categoryId:int,
     *     dueDate:string,
     *     isUrgent:bool,
     *     observation:string,
     *     checklistItems:list<array{label:string,is_completed:bool}>
     * }
     */
    public function toFormState(): array
    {
        return [
            'title' => $this->title,
            'location' => $this->location ?? '',
            'categoryId' => $this->categoryId,
            'dueDate' => $this->dueDate ?? '',
            'isUrgent' => $this->isUrgent,
            'observation' => $this->observation ?? '',
            'checklistItems' => array_map(
                fn (ChecklistItemData $item) => $item->toFormState(),
                $this->checklistItems,
            ),
        ];
    }

    /**
     * @param  array<int, array{label?:string|null,is_completed?:bool,sort_order?:int}>  $items
     * @return list<ChecklistItemData>
     */
    private static function normalizeChecklistItems(array $items): array
    {
        $normalized = [];

        foreach (array_values($items) as $index => $item) {
            $checklistItem = ChecklistItemData::fromArray($item, $index);

            if ($checklistItem === null) {
                continue;
            }

            $normalized[] = $checklistItem;
        }

        return $normalized;
    }

    private static function normalizeNullableString(?string $value): ?string
    {
        $trimmed = $value === null ? null : trim($value);

        return $trimmed === '' ? null : $trimmed;
    }
}
