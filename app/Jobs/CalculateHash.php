<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Medium;

class CalculateHash implements ShouldQueue
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
        $medium_collection = Medium::where('hash', null)->take(50)->get();

        foreach ($medium_collection as $medium) {
            $file = storage_path('app/public/' . $medium->name . '.' . $medium->extension);

            $hash = sha1_file($file);

            if ($hash) {
                $medium->hash = $hash;
            } else {
                // TODO: error handling
            }

            $medium->save();
        }
    }
}
