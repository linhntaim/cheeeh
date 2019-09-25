<?php

namespace App\V1\Utils\Files;

use App\V1\Exceptions\AppException;
use App\V1\Utils\Files\Filer\Filer;
use App\V1\Utils\Files\FileWriter\BinaryFileWriter;
use App\V1\Utils\Files\Disk\LocalStorageHandler;
use App\V1\Utils\StringHelper;

class ChunkedFileJoiner
{
    const CHUNK_FILE_NAME = 'chunk';
    const CHUNK_FOLDER_NAME = 'chunks';

    protected $fileId;
    protected $chunkDirectory;
    protected $joinedFilePath;
    protected $joined;

    protected $currentChunkFile;
    protected $joinedFiler;

    public function __construct($fileId = null )
    {
        $this->tryFileId($fileId);
        $this->joined = false;
    }

    protected function tryFileId($fileId)
    {
        $isEmptyFiledId = empty($fileId);
        if ($isEmptyFiledId) {
            $fileId = StringHelper::uuid();
        }
        $chunkDirectory = FileHelper::getInstance()->concatPath(static::CHUNK_FOLDER_NAME, $fileId);
        if ($isEmptyFiledId && $this->storage->exists($chunkDirectory)) { // prevent duplicate
            $this->tryFileId(null);
            return;
        }
        $this->fileId = $fileId;
        $this->chunkDirectory = $chunkDirectory;
        $this->joinedFilePath = $this->chunkDirectory . DIRECTORY_SEPARATOR . static::CHUNK_FILE_NAME;
    }

    public function getFileId()
    {
        return $this->fileId;
    }

    public function isJoined()
    {
        return $this->joined;
    }

    protected function getChunkFileNameByIndex($chunkIndex = 0)
    {
        return sprintf('%s.%s', static::CHUNK_FILE_NAME, $chunkIndex);
    }

    protected function getChunkFilePathByIndex($chunkIndex = 0)
    {
        return $this->chunkDirectory . DIRECTORY_SEPARATOR . $this->getChunkFileNameByIndex($chunkIndex);
    }

    protected function startToJoin()
    {
        if (!rename($this->getChunkFilePathByIndex(), $this->joinedFilePath)) {
            throw new AppException('Cannot start joining');
        }
        return $this;
    }

    protected function tryToJoin($chunkTotal)
    {
        if ($chunkTotal > 1) {
            $joinedFileWriter = (new BinaryFileWriter(static::CHUNK_FILE_NAME, false, $this->chunkDirectory))->openToAppend();
            for ($i = 1; $i < $chunkTotal; ++$i) {
                $chunkFileHandler = (new BinaryFileWriter($this->getChunkFileNameByIndex($i), false, $this->chunkDirectory))->openToRead();
                $joinedFileWriter->write($chunkFileHandler->readAll());
                $chunkFileHandler->close()
                    ->delete();
            }
            $joinedFileWriter->close();
        }
        return $this;
    }

    protected function storeJoinedFile()
    {
        $this->joinedFiler = new Filer($this->joinedFilePath);
        $this->joinedFiler->store([
            'extension' => $this->joinedFiler->getFile()->guessExtension(),
        ]);

        return $this;
    }

    protected function removeChunkDirectory()
    {
        rmdir($this->chunkDirectory);
        return $this;
    }

    protected function completeToJoin()
    {
        return $this->storeJoinedFile()->removeChunkDirectory();
    }

    protected function canJoin($chunkTotal)
    {
        for ($i = $chunkTotal - 1; $i >= 0; --$i) {
            if (!file_exists($this->getChunkFilePathByIndex($i))) return false;
        }
        return true;
    }

    public function join($chunkIndex, $chunkTotal, $chunkFile)
    {
        $this->currentChunkFile = File::from($chunkFile)->move($this->chunkDirectory, $this->getChunkFileNameByIndex($chunkIndex));

        if ($this->canJoin($chunkTotal)) {
            $this->startToJoin()
                ->tryToJoin($chunkTotal)
                ->completeToJoin();

            $this->currentChunkFile = null;
            $this->joined = true;
        }

        return $this;
    }
}
