<?php

namespace App\Jobs\Moment;

use App\Enums\MomentStatus;
use App\Models\Moment;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CleanupExpiredMomentsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $now = Carbon::now('UTC');

        Moment::where('status', '!=', MomentStatus::EXPIRED)
            ->where('status', '!=', MomentStatus::DELIVERED)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', $now)
            ->update(['status' => MomentStatus::EXPIRED]);
    }
}
