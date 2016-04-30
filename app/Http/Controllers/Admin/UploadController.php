<?php

namespace App\Http\Controllers\Admin;

use App\Services\UploadsManager;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;

class UploadController extends Controller
{
    /*
     * 文件上传管理服务对象
     * */
    protected $manager;

    public function __construct(UploadsManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Show page of files / subfolders
     */
    public function index(Request $request)
    {
        //获取当前需要访问的路径  默认是文件管理的的根目录
        $folder = $request->get('folder');
//        return $folder;
        $data = $this->manager->folderInfo($folder);

        return view('admin.upload.index', $data);
    }

    // 添加如下四个方法到UploadController控制器类
    /**
     * 创建新目录
     */
    public function createFolder(Requests\UploadNewFolderRequest $request)
    {
        $new_folder = $request->get('new_folder');
        $folder = $request->get('folder').'/'.$new_folder;
//        return $folder;

        $result = $this->manager->createDirectory($folder);
//        dd($result);
        if ($result === true) {
            return redirect()
                ->back()
                ->withSuccess("Folder '$new_folder' created.");
        }

        $error = $result ? : "An error occurred creating directory.";
        return redirect()
            ->back()
            ->withErrors([$error]);
    }

    /**
     * 删除文件
     */
    public function deleteFile(Request $request)
    {
        $del_file = $request->get('del_file');
        $path = $request->get('folder').'/'.$del_file;
//        return $path;
        $result = $this->manager->deleteFile($path);

        if ($result === true) {
            return redirect()
                ->back()
                ->withSuccess("File '$del_file' deleted.");
        }

        $error = $result ? : "An error occurred deleting file.";
        return redirect()
            ->back()
            ->withErrors([$error]);
    }

    /**
     * 删除目录
     */
    public function deleteFolder(Request $request)
    {
        $del_folder = $request->get('del_folder');
        $folder = $request->get('folder').'/'.$del_folder;

//        return $del_folder;
        $result = $this->manager->deleteDirectory($folder);

        if ($result === true) {
            return redirect()
                ->back()
                ->withSuccess("Folder '$del_folder' deleted.");
        }

        $error = $result ? : "An error occurred deleting directory.";
        return redirect()
            ->back()
            ->withErrors([$error]);
    }

    /**
     * 上传文件
     */
    public function uploadFile(Requests\UploadFileRequest $request)
    {
        $file = $_FILES['file'];
        $file1 = $request->file('file');//获取文件的临时保存的地址 D:\xampp\tmp\php29F1.tmp
//        return $file;
        $fileName = $request->get('file_name');//获取自定义的文件名
        $fileName = $fileName ?: $file['name'];
        $path = str_finish($request->get('folder'), '/') . $fileName;//文件的全路径
//        return $path;
        $content = File::get($file['tmp_name']); //获取文件的内容
//        $content = $file['tmp_name'];
//        return $content;
        $result = $this->manager->saveFile($path, $content);
//        $result = $this->manager->saveFile($path, $file1,$request->get('folder'),$fileName);

        if ($result === true) {
            return redirect()
                ->back()
                ->withSuccess("File '$fileName' uploaded.");
        }

        $error = $result ? : "An error occurred uploading file.";
        return redirect()
            ->back()
            ->withErrors([$error]);
    }

}
