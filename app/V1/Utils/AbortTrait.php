<?php

namespace App\V1\Utils;

trait AbortTrait
{
    protected function abort($code)
    {
        return abort($code, trans('error.def.abort.' . $code));
    }

    protected function abort403()
    {
        return $this->abort(403);
    }

    protected function abort404()
    {
        return $this->abort(404);
    }
}
