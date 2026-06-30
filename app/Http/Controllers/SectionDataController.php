<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helper\AdminChecker;
use App\Models\Section;
use App\Models\User;
use App\Models\AppliedSection;
use App\Models\UserLevel;
use App\Models\UserInGameData;
use App\Models\Novel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\Rules\Enum;

class SectionDataController extends Controller
{
    /**
     * Show overall quiz data for a section
     */
    public function ShowOverallQuizData($id)
    {
        try {
            // Verify if teacher can access the section
            if (!AdminChecker::allowCurrentTeacher($id) && !AdminChecker::isAdmin(id: Auth::guard('teacher')->user()->id)) {
                return redirect()->back()->withErrors(['unauthorized' => 'This Section is missing in your List']);
            }

            $section = Section::findOrFail($id);
            $appliedUsers = AppliedSection::where('section_id', $id)->pluck('user_id');
            $users = User::whereIn('id', $appliedUsers)->get();

            $quizResults = [];

            foreach ($users as $user) {
                $scores = UserLevel::with(['gameLevel.novel'])
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

                foreach ($scores as $score) {
                    $novelName = $score->gameLevel->novel->novel_name ?? 'Unknown';
                    $quizResults[] = [
                        'username' => $user->username,
                        'novel' => $novelName,
                        'average' => round($score->average, 1),
                    ];
                }
            }

            return view('section_quiz', compact('quizResults', 'section'));

        } catch (\Exception $e) {
            // \Log::error('Quiz data fetch error: ' . $e->getMessage());
            return redirect()->back()->withErrors(['invalid' => 'Something went wrong, please try again']);
        }
    }

    /**
     * Show overall summative data for a section
     */
    public function ShowOverallSummativeData($id)
    {
        try {
            // Verify if teacher can access the section
            if (!AdminChecker::allowCurrentTeacher($id) && !AdminChecker::isAdmin(id: Auth::guard('teacher')->user()->id)) {
                return redirect()->back()->withErrors(['unauthorized' => 'This Section is missing in your List']);
            }

            $section = Section::findOrFail($id);
            $appliedUsers = AppliedSection::where('section_id', $id)->pluck('user_id');
            $users = User::whereIn('id', $appliedUsers)->get();

            $summativeResults = [];

            foreach ($users as $user) {
                $userData = UserInGameData::where('user_id', $user->id)->first();
                if (!$userData || empty($userData->summative)) {
                    continue; // skip users without data
                }

                $logs = json_decode($userData->summative, true);

                foreach ($logs['logs'] as $log) {
                    $novelId = $log['novel'];

                    // Map novel ID using enum
                    $novelName = NovelEnum::getLabel($novelId);

                    $questions = $log['summativeQuestionContainer']['questions'] ?? [];
                    if (count($questions) === 0) continue;

                    $scoreSum = 0;
                    $totalQuestions = count($questions);
                    $started = $log['startTime'];
                    $finished = $log['finishedTime'];

                    foreach ($questions as $q) {
                        if (!empty($q['isCorrect']) && $q['isCorrect'] === true) {
                            $scoreSum++;
                        }
                    }

                    $average = ($scoreSum / $totalQuestions) * 100;

                    $summativeResults[] = [
                        'username' => $user->username,
                        'novel' => $novelName,
                        'average' => round($average, 1),
                        'started' => $started,
                        'finished' => $finished
                    ];
                }
            }

            return view('section_summative', compact('summativeResults', 'section'));

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['invalid' => 'Something went wrong, please try again']);
        }
    }
}

class NovelEnum
{
    const NOLI_ME_TANGERE = 0;
    const EL_FILIBUSTERISMO = 1;

    public static function getLabel(int $id): string
    {
        return match($id) {
            self::NOLI_ME_TANGERE => 'Noli Me Tangere',
            self::EL_FILIBUSTERISMO => 'El Filibusterismo',
            default => 'Unknown',
        };
    }
}
