<?php

namespace App\Services;

use App\Repositories\Interfaces\FileRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class FileService
{
    private $repo;

    public function __construct(FileRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function index()
    {
        $user = Auth::user();
        if ("admin" === $user->type) {
            return $this->repo->all();
        } else if ("user" === $user->type) {
            return Auth::user()->files;
        }
    }

    public function create($file)
    {
        $path = $file->storeAs('file', Str::uuid() . "." . $file->extension());
        $attrributes = [
            'author_id' => Auth::user()->id,
            'original_filename' => $file->getClientOriginalName(),
            'path' => $path,
        ];
        return $this->repo->create($attrributes);
    }

    public function versions($filename)
    {
        $user = Auth::user();
        if ("admin" === $user->type) {
            return $this->repo->all();
        } else if ("user" === $user->type) {
            return $user->file_versions($filename)
                ->get();
        }
    }
}
