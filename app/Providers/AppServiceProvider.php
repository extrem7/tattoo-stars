<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Http\UploadedFile;
use ProtoneMedia\LaravelFFMpeg\FFMpeg\FFProbe;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        \Validator::extend('string255', fn($attr, $value) => (is_string($value) && mb_strlen($value) <= 255));

        \Validator::extend('video_duration', function ($attr, UploadedFile $value, $parameters, $validator) {
            if (!isset($parameters[0]) || !is_int((int)$parameters[0])) {
                throw new \InvalidArgumentException('Video duration parameter is required.');
            }

            $validator->addReplacer('video_duration', function ($message, $a, $r, $parameters) {
                return str_replace([':seconds'], $parameters[0], $message);
            });

            $format = FFProbe::make(\FFMpeg::new()->getFFProbe())->format($value->getRealPath());

            $duration = $format->get('duration');
            $duration = round($duration);

            return $duration < $parameters[0];
        });
    }
}
