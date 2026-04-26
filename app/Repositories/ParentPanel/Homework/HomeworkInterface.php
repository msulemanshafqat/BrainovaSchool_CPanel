<?php

namespace App\Repositories\ParentPanel\Homework;

interface HomeworkInterface
{
    /** Initial page load — returns children list for the dropdown. */
    public function indexParent(): array;

    /** Parent selects a child — returns homework + submission records. */
    public function search($request): array;

    /** Legacy compatibility — not used by new blade. */
    public function index($request);
}
