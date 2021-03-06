<?php

namespace App\V1\Http\Controllers\Api\Admin;

use App\V1\Http\Controllers\ApiController;
use App\V1\Http\Requests\Request;
use Illuminate\Support\Facades\File;

class SystemLogController extends ApiController
{
    public function index(Request $request)
    {
        $systemLogs = [];
        $logPath = storage_path('logs');
        foreach (File::allFiles($logPath) as $logFile) {
            $logFileRelativePath = trim(str_replace($logPath, '', $logFile->getRealPath()), '\\/');
            $systemLogs[] = [
                'name' => $logFileRelativePath,
                'url' => url('system-log/' . str_replace('\\', '/', $logFileRelativePath)),
            ];
        }

        return $this->responseSuccess([
            'system_logs' => $systemLogs,
        ]);
    }
}
