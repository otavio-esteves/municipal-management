<?php

namespace App\Application\Auth\Data;

final readonly class RedirectTargetData
{
    /**
     * @param  array<string, int|string>  $parameters
     */
    public function __construct(
        public string $routeName,
        public array $parameters = [],
    ) {}

    /**
     * @param  array<string, scalar>  $query
     */
    public function toUrl(bool $absolute = false, array $query = []): string
    {
        $url = route($this->routeName, $this->parameters, $absolute);

        if ($query === []) {
            return $url;
        }

        return $url.'?'.http_build_query($query);
    }
}
