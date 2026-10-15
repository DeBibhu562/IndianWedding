<?php

namespace App\Notifications;

use App\Models\ProjectTeamMember;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class TeamAssignedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public ProjectTeamMember $member) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'New assignment',
            'body' => 'You were assigned as '.$this->member->roleLabel().' on a project.',
            'project_id' => $this->member->project_id,
        ];
    }
}
