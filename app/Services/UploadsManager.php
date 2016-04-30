<?php

namespace App\Services;

use Carbon\Carbon;
use Dflydev\ApacheMimeTypes\PhpRepository;
use Illuminate\Support\Facades\Storage;

class UploadsManager
{
    protected $disk;
    protected $mimeDetect;

    public function __construct(PhpRepository $mimeDetect)
    {
        //获取文件系统的存储驱动器  local
        $this->disk = Storage::disk(config('blog.uploads.storage'));
        //PhpRepository  获取文件的mime类型的对象
        $this->mimeDetect = $mimeDetect;
    }

    /**
     * Return files and directories within a folder
     *
     * @param string $folder
     * @return array of [
     *     'folder' => 'path to current folder',
     *     'folderName' => 'name of just current folder',
     *     'breadCrumbs' => breadcrumb array of [ $path => $foldername ]
     *     'folders' => array of [ $path => $foldername] of each subfolder
     *     'files' => array of file details on each file in folder
     * ]
     */
    public function folderInfo($folder)
    {
        //返回当前的文件的目录
        $folder = $this->cleanFolder($folder);
//        dd($folder);
        $breadcrumbs = $this->breadcrumbs($folder);//面包屑导航目录 返回一个数组
        $slice = array_slice($breadcrumbs, -1);// 从数组中取出一段 获取数组的末端键值
        $folderName = current($slice);//返回数组中的当前单元 获取当前的目录名
        $breadcrumbs = array_slice($breadcrumbs, 0, -1);//获取剩下的目录路径

        /*
         * 获取当前目录下的所有目录
         * */
        $subfolders = [];//当前目录下的子目录数组
        //directories 方法返回指定目录下的目录数组 array_unique移除数组中重复的值
        //allDirectories 方法获取指定目录下的子目录以及子目录所包含的目录。
        foreach (array_unique($this->disk->directories($folder)) as $subfolder) {
            //返回路径中的文件名部分
            $subfolders["/$subfolder"] = basename($subfolder);
        }

        /*
         * 获取当前目录下所有文件
         * */
        $files = [];//当前目录下的文件数组
        //files 方法返回指定目录下的文件数组
        //如果你希望返回包含指定目录下所有子目录的文件，则可以使用 allFiles 方法。
        foreach ($this->disk->files($folder) as $path) {
            $files[] = $this->fileDetails($path);
        }

        // 建立一个数组，包括变量名和它们的值
        return compact(
            'folder',
            'folderName',
            'breadcrumbs',
            'subfolders',
            'files'
        );
    }

    /**
     * Sanitize the folder name
     */
    protected function cleanFolder($folder)
    {
        return '/' . trim(str_replace('..', '', $folder), '/');
    }

    /**
     * 返回当前目录路径
     */
    protected function breadcrumbs($folder)
    {
        $folder = trim($folder, '/');
        $crumbs = ['/' => 'root'];

        if (empty($folder)) {
            return $crumbs;
        }
        //目录拆分 例如 hhh/www/eee
        $folders = explode('/', $folder);
        $build = '';
        foreach ($folders as $folder) {
            $build .= '/'.$folder;
            $crumbs[$build] = $folder;
        }

        return $crumbs;
    }

    /**
     * 返回文件详细信息数组
     */
    protected function fileDetails($path)
    {
        //文件的路径
        $path = '/' . ltrim($path, '/');

        return [
            'name' => basename($path),//文件名
            'fullPath' => $path,//全路径
            'webPath' => $this->fileWebpath($path),//文件web访问路径
            'mimeType' => $this->fileMimeType($path),//文件的类型
            'size' => $this->fileSize($path),//获取文件的大小并以 bytes 显示：
            'modified' => $this->fileModified($path),//返回文件的最后修改时间
        ];
    }

    /**
     * 返回文件完整的web路径
     */
    public function fileWebpath($path)
    {
        //获取配置文件下设置的文件web访问路径
        //   'webpath' => 'public/uploads',
        $path = rtrim(config('blog.uploads.webpath'), '/') . '/' .ltrim($path, '/');
        return url($path);
    }

    /**
     * 返回文件MIME类型
     */
    public function fileMimeType($path)
    {
        //获取指定文件的mime类型   pathinfo返回文件路径的信息这里就只返回文件的扩展名
        return $this->mimeDetect->findType(
            pathinfo($path, PATHINFO_EXTENSION)
        );
    }

    /**
     * 返回文件大小
     */
    public function fileSize($path)
    {
        return $this->disk->size($path);
    }

    /**
     * 返回最后修改时间
     */
    public function fileModified($path)
    {
        return Carbon::createFromTimestamp(
            $this->disk->lastModified($path)
        );
    }

    // 在该类中新增以下4个方法
    /**
     * 创建新目录
     */
    public function createDirectory($folder)
    {
        $folder = $this->cleanFolder($folder);

        if ($this->disk->exists($folder)) {
            return "Folder '$folder' already exists.";
        }

        return $this->disk->makeDirectory($folder);
    }

    /**
     * 删除目录
     */
    public function deleteDirectory($folder)
    {
        $folder = $this->cleanFolder($folder);

        // array_merge 合并一个或多个数组
        $filesFolders = array_merge(
            $this->disk->directories($folder),
            $this->disk->files($folder)
        );
       // dd( $this->disk->directories($folder));
        if (! empty($filesFolders)) {
            return "Directory must be empty to delete it.";
        }

        return $this->disk->deleteDirectory($folder);
    }

    /**
     * 删除文件
     */
    public function deleteFile($path)
    {
        $path = $this->cleanFolder($path);

        if (! $this->disk->exists($path)) {
            return "File does not exist.";
        }

        return $this->disk->delete($path);
    }

    /**
     * 保存文件
     */
    public function saveFile($path, $content)
    {
        $path = $this->cleanFolder($path);

        if ($this->disk->exists($path)) {
            return "File already exists.";
        }
        //put 方法保存单个文件于磁盘上 使用文件系统底层的 stream 支持
        // 将文件内容写入文件 推荐使用stream保存文件
        return $this->disk->put($path, $content);
//        dd(public_path('uploads').$folder);
//        return $content->move(public_path('uploads').$folder,$filename);
    }

}