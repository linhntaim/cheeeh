<?php

namespace App\V1\Http\Controllers\Api\Account;

use App\V1\Http\Controllers\ApiController;
use App\V1\Http\Requests\Request;
use App\V1\Utils\Files\Filer\ChunkedFiler;

class UploadController extends ApiController
{
    public function store(Request $request)
    {
        if ($request->has('_chunk_init')) {
            return $this->storeChunkInit($request);
        }
        if ($request->has('_chunk')) {
            return $this->storeChunk($request);
        }

        return $this->responseFail();
    }

    private function storeChunkInit(Request $request)
    {
        return $this->responseModel([
            'file_id' => ChunkedFiler::generateFileId(),
        ]);
    }

    private function storeChunk(Request $request)
    {
        $this->validated($request, [
            'file_id' => 'required',
            'chunk_index' => 'required',
            'chunk_total' => 'required',
            'chunk_file' => 'required',
        ]);

        $joiner = (new ChunkedFiler(
            $request->file('chunk_file'),
            $request->input('file_id'),
            $request->input('chunk_index'),
            $request->input('chunk_total')
        ))->join();

        return $this->responseModel([
            'file_id' => $joiner->getFileId(),
            'joined' => $joiner->isJoined(),
        ]);
    }
}
