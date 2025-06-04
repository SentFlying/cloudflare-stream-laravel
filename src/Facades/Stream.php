<?php

namespace SentFlying\CloudflareStreamLaravel\Facades;

use Illuminate\Support\Facades\Facade;
use SentFlying\CloudflareStreamLaravel\Client;

/**
 * @method static array listLiveInputs()
 * @method static array createLiveInput(array $meta, ?array $recording = null, ?string $uid = null, ?int $deleteRecordingAfterDays = null)
 * @method static array getLiveInput(string $liveInputId)
 * @method static array updateLiveInput(string $liveInputId, ?array $meta = null, ?array $recording = null, ?int $deleteRecordingAfterDays = null)
 * @method static bool deleteLiveInput(string $liveInputId)
 * 
 * @see \SentFlying\CloudflareStreamLaravel\Client
 */
class Stream extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return Client::class;
    }
}
