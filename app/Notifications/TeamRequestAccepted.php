<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Team;

class TeamRequestAccepted extends Notification
{
    use Queueable;

    protected $team;

    public function __construct(Team $team)
    {
        $this->team = $team;
    }

    // هنحدد إننا هنخزن الإشعار في الداتابيز
    public function via($notifiable): array
    {
        return ['database'];
    }

    // الداتا اللي هتتخزن وتظهر لليوزر
    public function toArray($notifiable): array
    {
        return [
            'team_id' => $this->team->id,
            'team_name' => $this->team->name,
            'message' => "مبروك! تم قبول طلب انضمامك لفريق: " . $this->team->name,
        ];
    }
}