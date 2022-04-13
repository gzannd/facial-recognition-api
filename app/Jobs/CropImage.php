<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CropImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $imageData;
    protected $geometry;
    protected $format;
    protected $imageId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($imageData, $geometry, $format, $imageId)
    {
          $this->imageData = $imageData;
          $this->geometry = $geometry;
          $this->format = $format;
          $this->imageId = $imageId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $imageCropper = new ImageCropper($imageData, $format);
        return $imageCropper->Crop($this->geometry);
    }
}
