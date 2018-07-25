<?php

namespace App\Http\Controllers\API;

use App\Medium;
use App\Http\Resources\Medium as MediumResource;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use InstagramScraper\Instagram;
use Illuminate\Support\Facades\Log;

class MediumController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        /* check and sanitize input */
        if (!$request->filled('url')) {
            return;
        }
        $clean_url = strtok($request->input('url'), '?');

        /* scrape Instagram post */
        $instagram = new Instagram;
        $media = $instagram->getMediaByUrl($clean_url);
        $collection = $media->getSidecarMedias();
        if (count($collection) > 1) {
            foreach ($collection as $current_media) {
                $media_url = $current_media->getImageHighResolutionUrl();
                $stored_media[] = $this->copy_remote_file($media_url);
            }
            return MediumResource::collection(collect($stored_media));
        } else {
            $media_url = $media->getImageHighResolutionUrl();
            $stored_medium = $this->copy_remote_file($media_url);
            return new MediumResource($stored_medium);
        }
    }

    /**
     * Display a random resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function random()
    {
        $medium = Medium::inRandomOrder()->first();

        return new MediumResource($medium);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Medium  $medium
     * @return \Illuminate\Http\Response
     */
    public function show(Medium $medium)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Medium  $medium
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Medium $medium)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Medium  $medium
     * @return \Illuminate\Http\Response
     */
    public function destroy(Medium $medium)
    {
        //
    }

    private function copy_remote_file($url) {
        /* get remote file to a temporary local place */
        $contents = file_get_contents($url);
        $name = Str::random(40);
        Storage::put($name, $contents, 'public');

        /* get file meta data */
        $path = Storage::path($name);
        $file = new File($path);
        $extension = $file->guessExtension();
        $mime_type = $file->getMimeType();

        /* rename the temporary file to also contain the correct extension */
        Storage::move($name, $name . '.' . $extension);

        /* save to database */
        $medium = new Medium;
        $medium->name = $name;
        $medium->extension = $extension;
        $medium->mime_type = $mime_type;
        $medium->save();

        /* return the newly created medium as a resource */
        return $medium;
    }
}
