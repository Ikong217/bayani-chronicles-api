<?php
namespace App\Http\Controllers;

use App\Models\AppliedSection;
use App\Models\GameLevel;
use App\Models\User;
use App\Models\UserLevel;
use Illuminate\Support\Facades\Crypt;

class UserScoresController extends Controller
{
    //
    public function UserScoresShow($id)
    {
        $user_id = Crypt::decrypt($id);
        //dd($user_id);
        $user    = User::findOrFail($user_id);
        $section = AppliedSection::where('user_id', $user_id)->first();
        $scores  = UserLevel::with(['gameLevel.novel'])
            ->selectRaw("
        user_id,
        game_level_id,
        (
            select score
            from user_levels ul2
            where ul2.user_id = user_levels.user_id
            and ul2.game_level_id = user_levels.game_level_id
            and ul2.status = 'Completed'
            order by ul2.id asc
            limit 1
        ) as first_score,
        count(game_level_id) as attempts,
        avg(score) as average
    ")
            ->where('user_id', $user->id)
            ->where('status', 'Completed')
            ->groupBy('game_level_id', 'user_id')
            ->get();

        return view('user_scores', compact(
            'user',
            'section',
            'scores'));
    }

    public function UserLogsShow($id)
    {
        $compact_id = Crypt::decrypt($id);       //decrypts id contains user_id _ game_level_id
        $arr_id     = explode('_', $compact_id); //separates data into 0= user_id and 1= level_id
        $user       = User::findOrFail($arr_id[0]);
        $level      = GameLevel::findOrFail($arr_id[1]);
        $logs       = UserLevel::where('user_id', $arr_id[0])
            ->where('game_level_id', $arr_id[1])
            ->whereNot('status', 'Ongoing')
            ->get();
        //dd($logs);

        // === PIE CHART DATA (count per status except Abandoned) ===
        $statusCounts = $logs->whereNotIn('status', ['Abandoned'])
            ->groupBy('status')
            ->map(fn($group) => $group->count());

        // === BAR CHART DATA (average duration per status except Abandoned) ===
        $averageDurations = $logs->whereNotIn('status', ['Abandoned'])->groupBy('status')->map(function ($group) {
            $total = 0;
            $count = 0;
            foreach ($group as $log) {
                if ($log->finished_at && $log->started_at) {
                    $total += $log->finished_at->diffInSeconds($log->started_at);
                    $count++;
                }
            }
            return $count > 0 ? round($total / $count / 60, 2) : 0; // average duration in minutes
        });

        return view('user_activity_logs', compact('logs', 'user', 'level', 'statusCounts', 'averageDurations'));
    }

    public function getDetails($user_id, $level_id)
    {
        $logs = UserLevel::where('user_id', $user_id)
            ->where('game_level_id', $level_id)
            ->orderBy('created_at')
            ->get();

        // Count per status for bar chart
        $statusCounts = $logs->groupBy('status')->map->count();

        // Scores & attempts for line chart
        $scoreHistory  = $logs->where('status', 'Completed')->pluck('score')->toArray();
        $completedLogs = $logs->where('status', 'Completed')->values(); // reindex 0,1,2,...

        $attemptLabels = $completedLogs->map(function ($log, $index) {
            return "Completed: " . ($index + 1);
        })->toArray();

        // For bubble chart (completed only)
        $completed  = $logs->where('status', 'Completed')->values();
        $bubbleData = $completed->values()->map(function ($log, $index) {
            $duration = $log->finished_at ? (strtotime($log->finished_at) - strtotime($log->started_at)) / 60 : 0;
            return [
                'x' => $index + 1,  // attempt index
                'y' => $duration,   // duration (vertical axis)
                'r' => $log->score, // bubble size = score scaled
            ];
        });
        //dd($bubbleData);

        return response()->json([
            'statusCounts'  => $statusCounts,
            'scoreHistory'  => $scoreHistory,
            'attemptLabels' => $attemptLabels,
            'bubbleData'    => $bubbleData,
        ]);
    }

}
