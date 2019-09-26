<?php

namespace App\V1\Utils\Files\Filer;

use App\V1\Utils\Files\Disk\LocalDisk;
use App\V1\Utils\Files\FileHelper;
use App\V1\Utils\StringHelper;

class ChunkedFiler extends Filer
{
    const CHUNK_FILE_NAME = 'chunk';
    const CHUNK_FOLDER_NAME = 'chunks';

    public static function generateFileId()
    {
        $fileId = StringHelper::uuid();
        $chunkDirectory = FileHelper::concatPath(static::CHUNK_FOLDER_NAME, $fileId);
        if ((new LocalDisk())->exists($chunkDirectory)) { // prevent duplicate
            return static::generateFileId();
        }
        return $fileId;
    }

    protected $fileId;

    protected $chunkDirectory;
    protected $chunkIndex;
    protected $chunkTotal;
    protected $chunkExtension;

    /**
     * @var BinaryFiler
     */
    protected $joinedFiler;
    protected $joined;

    public function __construct($file, $fileId, $chunkIndex, $chunkTotal)
    {
        parent::__construct($file);

        $this->fileId = $fileId;
        $this->chunkDirectory = FileHelper::concatPath(static::CHUNK_FOLDER_NAME, $fileId);
        $this->joined = false;
        $this->chunkIndex = $chunkIndex;
        $this->chunkTotal = $chunkTotal;

        $this->setChunkExtension();

        if (!$this->moveInLocal($this->chunkDirectory, $this->getChunkFileBaseNameByIndex($this->chunkIndex))) {
            $this->move($this->chunkDirectory, $this->getChunkFileBaseNameByIndex($this->chunkIndex));
        }
    }

    protected function setChunkExtension()
    {
        $chunkExtension = $this->chunkIndex == 0 ?
            $this->getExtension()
            : (new Filer($this->disk->findFile($this->chunkDirectory, $this->getChunkFileNameByIndex())))->getExtension();
        $this->chunkExtension = $chunkExtension ? '.' . $chunkExtension : '';
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

    protected function getChunkFileBaseNameByIndex($chunkIndex = 0)
    {
        return $this->getChunkFileNameByIndex($chunkIndex) . $this->chunkExtension;
    }

    protected function getChunkFileBaseName()
    {
        return static::CHUNK_FILE_NAME . $this->chunkExtension;
    }

    protected function getChunkFilePathByIndex($chunkIndex = 0)
    {
        return $this->chunkDirectory . DIRECTORY_SEPARATOR . $this->getChunkFileBaseNameByIndex($chunkIndex);
    }

    protected function startJoining()
    {
        $this->joinedFiler = new BinaryFiler(FileHelper::concatPath($this->disk->getDirectory(), $this->getChunkFilePathByIndex()));
        $this->joinedFiler->duplicate(null, $this->getChunkFileBaseName());
        return $this;
    }

    protected function tryJoining()
    {
        if ($this->chunkTotal > 1) {
            $this->joinedFiler->openToAppend();
            foreach (range(1, $this->chunkTotal - 1) as $chunkIndex) {
                $chunkedFile = (new BinaryFiler(FileHelper::concatPath($this->disk->getDirectory(), $this->getChunkFilePathByIndex($chunkIndex))))
                    ->openToRead();
                $this->joinedFiler->write($chunkedFile->readAll());
                $chunkedFile->close();
            }
            $this->joinedFiler->close();
        }
        return $this;
    }

    protected function completeJoining()
    {
        return $this->publishJoinedFile()->removeChunkDirectory();
    }

    protected function publishJoinedFile()
    {
        $this->joinedFiler->moveInPublic(true, FileHelper::randomFileBaseName($this->joinedFiler->getExtension()));
        return $this;
    }

    protected function removeChunkDirectory()
    {
        $this->disk->fileDeleteFolder();
        return $this;
    }

    protected function canJoin()
    {
        for ($i = $this->chunkTotal - 1; $i >= 0; --$i) {
            if ($this->disk->exists($this->getChunkFilePathByIndex($i))) continue;
            return false;
        }
        return true;
    }

    public function join()
    {
        if ($this->canJoin()) {
            $this->startJoining()->tryJoining()->completeJoining();
            $this->joined = true;
        }

        return $this;
    }
}
