<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Medium;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;

class ProcessMediaInputFolder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $files = Storage::files('input');

        foreach ($files as $file) {
            $orig_file_path = (string)$file;

            $orig_file_object = new File(Storage::disk('public')->getDriver()->getAdapter()->getPathPrefix() . $orig_file_path);

            $new_name = Str::random(40);
            $new_extension = $orig_file_object->guessExtension();
            $mime_type = $orig_file_object->getMimeType();

            if (
                !Storage::move(
                    $orig_file_path,
                    $new_name . '.' . $new_extension
                )
            ) {
                continue;
            }

            /* save to database */
            $medium = new Medium;
            $medium->name = $new_name;
            $medium->extension = $new_extension;
            $medium->mime_type = $mime_type;
            $medium->save();
        }

    }
}
