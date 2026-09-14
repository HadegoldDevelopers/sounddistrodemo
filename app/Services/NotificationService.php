<?php

namespace App\Services;

use App\Mail\EarningsReport;
use App\Mail\ReleaseApproved;
use App\Mail\ReleaseRejected;
use App\Mail\ReleaseSubmitted;
use App\Mail\WithdrawalApproved;
use App\Mail\WithdrawalRejected;
use App\Mail\WithdrawalRequested;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    /* =======================
       RELEASE SUBMITTED
    ======================= */
    public static function releaseSubmitted($upload): void
    {
        $user = $upload->user;

        if ($user && $user->email) {
            Mail::to($user->email)->send(new ReleaseSubmitted($upload, false));
        }

        Admin::all()->each(function ($admin) use ($upload) {
            if ($admin->email) {
                Mail::to($admin->email)->send(new ReleaseSubmitted($upload, true));
            }
        });
    }

    /* =======================
       RELEASE APPROVED
    ======================= */
    public static function releaseApproved($project): void
    {
        if ($project->user && $project->user->email) {
            Mail::to($project->user->email)
                ->send(new ReleaseApproved($project, $project->user));
        }
    }

    /* =======================
       RELEASE REJECTED
    ======================= */
    public static function releaseRejected($project): void
    {
        if ($project->user && $project->user->email) {
            Mail::to($project->user->email)
                ->send(new ReleaseRejected($project, $project->user));
        }
    }

    /* =======================
       WITHDRAWAL REQUESTED
    ======================= */
   public static function withdrawalRequested($withdrawal): void
{
    // Notify the user
    Mail::to($withdrawal->user->email)
        ->send(new WithdrawalRequested($withdrawal, false));

    // Notify all admins
    Admin::all()->each(function ($admin) use ($withdrawal) {
        if ($admin->email) {
            Mail::to($admin->email)
                ->send(new WithdrawalRequested($withdrawal, true, $admin));
        }
    });
}

    /* =======================
       WITHDRAWAL APPROVED
    ======================= */
    public static function withdrawalApproved($withdrawal): void
{
    $user = $withdrawal->user;

    if ($user && $user->email) {
        Mail::to($user->email)
            ->send(new WithdrawalApproved($withdrawal, $user));
    }
}

    /* =======================
       WITHDRAWAL REJECTED
    ======================= */
   public static function withdrawalRejected($withdrawal): void
{
    $user = $withdrawal->user;

    if ($user && $user->email) {
        Mail::to($user->email)
            ->send(new WithdrawalRejected($withdrawal, $user));
    }
}

    /* =======================
       EARNINGS REPORT
    ======================= */
   public static function earningsReport(User   $user, string $csvContent, string $filename, string $period, int    $totalStreams, float  $totalEarnings): void {
       
    if (!$user->email) {
        Log::warning("EarningsReport mail skipped — no email for user ID: {$user->id}");
        return;
    }

    Mail::to($user->email)->send(new EarningsReport(
        user:          $user,
        csvContent:    $csvContent,
        filename:      $filename,
        period:        $period,
        totalStreams:  $totalStreams,
        totalEarnings: $totalEarnings,
    ));
}
}