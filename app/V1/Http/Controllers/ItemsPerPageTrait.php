<?php

namespace App\V1\Http\Controllers;

use App\V1\Configuration;

trait ItemsPerPageTrait
{
    protected function paging()
    {
        return $this->paged() ? Configuration::FETCH_PAGING_YES : Configuration::FETCH_PAGING_NO;
    }

    protected function paged()
    {
        return request()->has('page');
    }

    protected function itemsPerPage()
    {
        $itemsPerPage = request()->input('items_per_page', Configuration::DEFAULT_ITEMS_PER_PAGE);
        if (!in_array($itemsPerPage, Configuration::ALLOWED_ITEMS_PER_PAGE)) $itemsPerPage = Configuration::DEFAULT_ITEMS_PER_PAGE;
        return $itemsPerPage;
    }
}
