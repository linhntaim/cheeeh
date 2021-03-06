<?php

namespace App\V1\Utils;

use App\V1\Configuration;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PaginationHelper
{
    public static function parse($paginator)
    {
        if ($paginator instanceof LengthAwarePaginator) {
            return (new static($paginator))->toArray();
        }
        return null;
    }

    public $last;
    public $first;
    public $next;
    public $prev;
    public $start;
    public $end;
    public $current;
    public $atFirst;
    public $atLast;
    public $startOrder;
    public $totalItems;
    public $itemsPerPage;

    public function __construct(LengthAwarePaginator $paginator, $maxPageShow = Configuration::DEFAULT_PAGINATION_ITEMS)
    {
        $current = $paginator->currentPage();
        $last = $paginator->lastPage();
        $this->itemsPerPage = intval($paginator->perPage());
        $this->totalItems = $paginator->total();
        $this->startOrder = ($current - 1) * $this->itemsPerPage;
        $pivot = round($maxPageShow / 2);
        $distance = floor($maxPageShow / 2);
        $this->last = $last;
        $this->first = 1;
        $this->prev = $current > $this->first ? $current - 1 : $this->first;
        $this->next = $current < $last ? $current + 1 : $last;
        $this->end = $current < $pivot ? ($last > $maxPageShow ? $maxPageShow : $last) : ($current < $last - $distance ? $current + $distance : $last);
        $this->start = $this->end - $maxPageShow + $this->first;
        if ($this->start < $this->first) {
            $this->start = $this->first;
        }
        $this->current = $current;
        $this->atFirst = $this->current == $this->first;
        $this->atLast = $this->current == $this->last;
    }

    public function toArray()
    {
        return [
            'start_order' => $this->startOrder,
            'current' => $this->current,
            'first' => $this->first,
            'last' => $this->last,
            'next' => $this->next,
            'prev' => $this->prev,
            'at_first' => $this->atFirst,
            'at_last' => $this->atLast,
            'range' => [
                'start' => $this->start,
                'end' => $this->end,
            ],
            'total_items' => $this->totalItems,
            'formatted_total_items' => NumberFormatHelper::getInstance()->formatInt($this->totalItems),
            'items_per_page' => $this->itemsPerPage,
        ];
    }
}
