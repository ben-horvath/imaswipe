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
use App\Jobs\ScrapeInstagram;

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

        ScrapeInstagram::dispatch($clean_url);
    }

    /**
     * Synchronize resources. Perform multiple actions.
     *
     * @return \App\Http\Resources\Medium
     */
    public function sync(Request $request)
    {
        /* Get the approved ones only by default */
        $approved = true;

        /* Do the admin-only actions */
        if (
            $request->user('api') &&
            $request->user('api')->isAdmin()
        ) {
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

            /* If user is admin and the selected mode is the assessment, return media that is not yet approved */
            $approved = ($request->mode === 'assess') ? null : true;
        }

        $medium_collection = Medium::where('approved', $approved)->inRandomOrder()->take(10)->get();

        if ($request->startWith) {
            $first_medium = Medium::find($request->startWith);
            $medium_collection->prepend($first_medium);
        }

        return MediumResource::collection($medium_collection);
    }

    /**
     * Merge resources.
     */
    public function merge(Request $request)
    {
        /* Do the admin-only actions */
        if (
            $request->user('api') &&
            $request->user('api')->isAdmin()
        ) {
            /* Delete */
            if (count($request->duplicates)) {
                foreach($request->duplicates as $duplicate) {
                    $medium = Medium::find($duplicate);

                    /* remove medium file from storage */
                    Storage::disk('public')
                        ->delete($medium->name . '.' . $medium->extension);

                    /* remove medium entry from database */
                    $medium->delete();
                }
            }
        }
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
}
