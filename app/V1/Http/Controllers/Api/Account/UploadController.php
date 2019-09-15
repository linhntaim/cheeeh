<?php

namespace App\V1\Http\Controllers\Api\Account;

use App\V1\Http\Controllers\ApiController;
use App\V1\Http\Requests\Request;
use App\V1\Utils\Files\ChunkedFileJoiner;

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
        return $this->responseCustomModel([
            'file_id' => (new ChunkedFileJoiner())->getFileId(),
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

        $joiner = (new ChunkedFileJoiner($request->input('file_id')))
            ->join(
                $request->input('chunk_index'),
                $request->input('chunk_total'),
                $request->file('chunk_file')
            );

        return $this->responseCustomModel([
            'file_id' => $joiner->getFileId(),
            'joined' => $joiner->isJoined(),
        ]);
    }
}
