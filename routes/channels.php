<?php

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('branch.{branchId}', function ($user, $branchId) {
    // Allow if user belongs to the branch
    return (int)$user->branch_id === (int)$branchId;
});

Broadcast::channel('branch.{branchId}.kitchen', function ($user, $branchId) {
    // Allow kitchen staff who belong to the branch (role check added)
    return (int)$user->branch_id === (int)$branchId && ($user->hasRole('chef') || $user->hasRole('manager'));
});
