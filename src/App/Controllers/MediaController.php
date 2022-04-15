<?php

namespace binhnx\MediaManager\App\Controllers;

use App\Http\Controllers\Controller;
use League\Flysystem\Plugin\ListWith;
use binhnx\MediaManager\App\Controllers\Modules\Lock;
use binhnx\MediaManager\App\Controllers\Modules\Move;
use binhnx\MediaManager\App\Controllers\Modules\Utils;
use binhnx\MediaManager\App\Controllers\Modules\Delete;
use binhnx\MediaManager\App\Controllers\Modules\Rename;
use binhnx\MediaManager\App\Controllers\Modules\Upload;
use binhnx\MediaManager\App\Controllers\Modules\Download;
use binhnx\MediaManager\App\Controllers\Modules\NewFolder;
use binhnx\MediaManager\App\Controllers\Modules\GetContent;
use binhnx\MediaManager\App\Controllers\Modules\Visibility;
use binhnx\MediaManager\App\Controllers\Modules\GlobalSearch;

class MediaController extends Controller
{
    use Utils,
        GetContent,
        Delete,
        Download,
        Lock,
        Move,
        Rename,
        Upload,
        NewFolder,
        Visibility,
        GlobalSearch;

    protected $baseUrl;
    protected $db;
    protected $fileChars;
    protected $fileSystem;
    protected $folderChars;
    protected $ignoreFiles;
    protected $LMF;
    protected $GFI;
    protected $sanitizedText;
    protected $storageDisk;
    protected $storageDiskInfo;
    protected $unallowedMimes;
    protected $unallowedExt;

    public function __construct()
    {
        $config = app('config')->get('mediaManager');

        $this->fileSystem       = $config['storage_disk'];
        $this->ignoreFiles      = $config['ignore_files'];
        $this->fileChars        = $config['allowed_fileNames_chars'];
        $this->folderChars      = $config['allowed_folderNames_chars'];
        $this->sanitizedText    = $config['sanitized_text'];
        $this->unallowedMimes   = $config['unallowed_mimes'];
        $this->unallowedExt     = $config['unallowed_ext'];
        $this->LMF              = $config['last_modified_format'];
        $this->GFI              = $config['get_folder_info']   ?? true;
        $this->paginationAmount = $config['pagination_amount'] ?? 50;

        $this->storageDisk     = app('filesystem')->disk($this->fileSystem);
        $this->storageDiskInfo = app('config')->get("filesystems.disks.{$this->fileSystem}");
        $this->baseUrl         = $this->storageDisk->url('/');
        $this->db              = app('db')
                                    ->connection($config['database_connection'])
                                    ->table($config['table_locked']);

        $this->storageDisk->addPlugin(new ListWith());
    }

    /**
     * main view.
     *
     * @return [type] [description]
     */
    public function index()
    {
        return view('MediaManager::media');
    }
}
