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
     * @return \App\Http\Resources\Medium
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
                $media_url = $this->get_media_url($current_media);
                $stored_media[] = $this->copy_remote_file($media_url);
            }
            return MediumResource::collection(collect($stored_media));
        } else {
            $media_url = $this->get_media_url($media);
            $stored_medium = $this->copy_remote_file($media_url);
            return new MediumResource($stored_medium);
        }
    }

    /**
     * Synchronize resources. Perform multiple actions.
     *
     * @return \App\Http\Resources\Medium
     */
    public function sync(Request $request)
    {
        /* Delete */
        if ($request->delete) {
            foreach($request->delete as $medium_name) {
                $medium = Medium::find($medium_name);

                /* remove medium file from storage */
                Storage::disk('public')
                    ->delete($medium->name . '.' . $medium->extension);

                /* remove medium entry from database */
                $medium->delete();
            }
        }

        /* Approve */
        foreach($request->approve as $medium_name) {
            $medium = Medium::find($medium_name);
            $medium->approved = true;
            $medium->save();
        }
        
        $approved = ($request->mode === 'assess') ? null : true;

        $medium_collection = Medium::where('approved', $approved)->inRandomOrder()->take(10)->get();

        if ($request->startWith) {
            $first_medium = Medium::find($request->startWith);
            $medium_collection->prepend($first_medium);
        }

        return MediumResource::collection($medium_collection);
    }

    /**
     * Return a random resource.
     *
     * @return \App\Http\Resources\Medium
     */
    public function random()
    {
        $medium_collection = Medium::where('approved', true)->inRandomOrder()->take(10)->get();

        return MediumResource::collection($medium_collection);
    }

    /**
     * Return a resource to assess.
     *
     * @return \App\Http\Resources\Medium
     */
    public function assess()
    {
        $medium_collection = Medium::where('approved', null)->inRandomOrder()->take(10)->get();

        return MediumResource::collection($medium_collection);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Medium  $medium
     * @return \Illuminate\Http\Response
     */
    public function show(Medium $medium)
    {
        $medium_collection = Medium::where('approved', true)->inRandomOrder()->take(9)->get();
        $medium_collection->prepend($medium);

        return MediumResource::collection($medium_collection);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Medium  $medium
     * @return \App\Http\Resources\Medium
     */
    public function update(Request $request, Medium $medium)
    {
        if ($request->approved == true) {
            $medium->approved = true;
            $medium->save();
        }

        $medium_collection = Medium::where('approved', null)->inRandomOrder()->take(10)->get();

        return MediumResource::collection($medium_collection);
    }

    /**
     * Remove the specified resource from database and the corresponding file
     * from storage.
     *
     * @param  \App\Medium  $medium
     * @return int
     */
    public function destroy(Medium $medium)
    {
        /* remove medium file from storage */
        Storage::disk('public')
            ->delete($medium->name . '.' . $medium->extension);

        /* remove medium entry from database */
        $medium->delete();

        $medium_collection = Medium::where('approved', null)->inRandomOrder()->take(10)->get();

        return MediumResource::collection($medium_collection);
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

    private function get_media_url($media) {
        switch ($media->getType()) {
            case 'image':
                return $media->getImageHighResolutionUrl();
                break;

            case 'video':
                return $media->getVideoStandardResolutionUrl();
                break;
        }
    }
}
