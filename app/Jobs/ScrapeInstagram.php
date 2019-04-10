<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Http\Resources\Medium as MediumResource;
use InstagramScraper\Instagram;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;
use Illuminate\Http\File;
use App\Medium;


class ScrapeInstagram implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $url;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($url)
    {
        $this->url = $url;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        /* scrape Instagram post */
        $instagram = new Instagram;
        $media = $instagram->getMediaByUrl($this->url);
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
