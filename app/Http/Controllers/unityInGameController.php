<?php
namespace App\Http\Controllers;

use App\Helper\AdminChecker;
use App\Models\AppliedSection;
use App\Models\GameLevel;
use App\Models\Level;
use App\Models\Novel;
use App\Models\Section;
use App\Models\User;
use App\Models\UserInGameData;
use App\Models\UserLevel;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;

class UnityInGameController extends Controller
{
    public function QuestionsRequest(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'novel' => 'required|exists:novels,novel_name',
                'level' => 'required|exists:game_levels,level_name',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Invalid data',
                    'errors'  => $validator->errors(),
                ], 422);
            }

            $novel = Novel::where('novel_name', $request->novel)->first();

            if (! $novel) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Novel not found',
                ], 404);
            }

            $game_level = GameLevel::where('novel_id', $novel->id)
                ->where('level_name', $request->level)
                ->first();

            if (! $game_level) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Level not found',
                ], 404);
            }

            // Get levels that match BOTH novel AND level name
            $levels = Level::where('game_level_id', $game_level->id)
                ->inRandomOrder()
                ->get();

            $questionsData = [];

            foreach ($levels as $level) {
                $otherAnswers = [$level->ans1, $level->ans2, $level->ans3];
                if (count($otherAnswers) == 3) {
                    $questionsData[] = [
                        'id'              => $level->id,
                        'type'            => $level->type,
                        'question'        => $level->question,
                        'answer'          => $level->answer,
                        'otherAnswers'    => $level->type == "Multiple" ? $otherAnswers : [],
                        'rationalization' => $level->rationalazation,
                    ];
                }
            }

            return response()->json([
                'status'    => 'success',
                'questions' => $questionsData, // Direct array, no wrapper
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function ScoreCreate(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|string',
                'novel'   => 'required|exists:novels,novel_name',
                'level'   => 'required|exists:game_levels,level_name',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Invalid data',
                    'errors'  => $validator->errors(),
                ], 422);
            }

            $id = Crypt::decrypt($request->user_id);
            User::findOrFail($id);

            $novel = Novel::where('novel_name', $request->novel)->first();

            if (! $novel) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Novel not found',
                ], 404);
            }

            $game_level = GameLevel::where('novel_id', $novel->id)
                ->where('level_name', $request->level)
                ->first();

            if (! $game_level) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Level not found',
                ], 404);
            }

            $userLevel = UserLevel::create([
                'user_id'       => $id,
                'game_level_id' => $game_level->id,
            ]);

            return response()->json([
                'status' => 'success',
                'id'     => Crypt::decrypt($userLevel->id),
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function ScoreUpdate(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id'     => 'required|string',
                'status' => 'required|string',
                'score'  => 'required|integer',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Invalid data',
                    'errors'  => $validator->errors(),
                ], 422);
            }

            $id        = Crypt::decrypt($request->id);
            $userLevel = UserLevel::findOrFail($id);

            $userLevel->status = $request->status;
            if ($request->status != "Abandoned") {
                $userLevel->finished_at = now();
            }
            $userLevel->score = $request->score;

            $userLevel->save();

            return response()->json([
                'status'  => 'success',
                'message' => 'Data successfully updated',
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function SaveUserInGameData(Request $request)
    {
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'id'      => 'required|string',
                'scrolls' => 'required|json', // json is enough, don't use string|json
                'levels'  => 'required|json', // levels is also JSON
                'summative'  => 'required|json', // levels is also JSON
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Invalid data',
                    'errors'  => $validator->errors(),
                ], 422);
            }

            // Decrypt user ID
            $id = Crypt::decrypt($request->id);

            // Save or update the data
            UserInGameData::updateOrCreate(
                ['user_id' => $id], // condition
                [
                    'scrolls' => $request->scrolls,
                    'levels'  => $request->levels,
                    'summative' => $request->summative,
                ]
            );

            return response()->json([
                'status'  => 'success',
                'message' => 'Data successfully updated',
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function Leaderboard(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation Error',
                'errors'  => $validator->errors(),
            ]);
        }

        try {
            $user_id = Crypt::decrypt($request->user_id);
            $user    = User::findOrFail($user_id);
        } catch (Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Parsing Error',
                'errors'  => ['parse' => [$e->getMessage()]],
            ]);
        }

        // Get user's section and grade level
        $user_section = AdminChecker::userGetSection($user->id);

        if (! $user_section) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Not Enrolled',
                'errors'  => ['error' => [
                    'You are not enrolled',
                    'Please check',
                    'Ask your teacher to enroll you',
                ]],
            ]);
        }

        $grade_level = $user_section->section->grade_level;
        $leaderboard = [];

        // Get all sections with same grade level
        $all_sections = Section::where('grade_level', $grade_level)->get();

        foreach ($all_sections as $section) {
            $applied_users = AppliedSection::where('section_id', $section->id)->get();

            foreach ($applied_users as $applied) {
                $individual_user = User::find($applied->user_id);
                if (! $individual_user) {
                    continue;
                }

                $GnS      = $section->grade_level . " - " . $section->section_name;
                $user_igd = UserInGameData::where('user_id', $individual_user->id)->first();
                if (! $user_igd) continue;

                $jsonData = json_decode($user_igd->levels, true);
                if (! $jsonData) continue;

                $levels = [];

                // helper to safely decode either array or JSON string
                $safeDecode = function ($data) {
                    if (is_string($data)) {
                        $decoded = json_decode($data, true);
                        return is_array($decoded) ? $decoded : null;
                    }
                    return is_array($data) ? $data : null;
                };

                // Grade 10: merge Noli + El Fili
                if ($section->grade_level == "Grade - 10") {
                    $noli   = isset($jsonData['noli'])   ? $safeDecode($jsonData['noli'])   : null;
                    $elfili = isset($jsonData['elfili']) ? $safeDecode($jsonData['elfili']) : null;

                    if ($noli && isset($noli['playerLevels'])) {
                        $levels = array_merge($levels, $noli['playerLevels']);
                    }
                    if ($elfili && isset($elfili['playerLevels'])) {
                        $levels = array_merge($levels, $elfili['playerLevels']);
                    }
                } else {
                    // Non-grade 10: only Noli
                    $noli = isset($jsonData['noli']) ? $safeDecode($jsonData['noli']) : null;
                    if ($noli && isset($noli['playerLevels'])) {
                        $levels = $noli['playerLevels'];
                    }
                }

                if (empty($levels)) continue;

                // --- Calculations ---
                $stars          = 0;
                $total_possible = count($levels) * 10;

                foreach ($levels as $level) {
                    $stars += intval($level['stars']);
                }

                // Calculate total score = sum of highest per game_level_id
                $level_scores = UserLevel::selectRaw('MAX(score) as highest')
                    ->where('user_id', $individual_user->id)
                    ->where('status', 'Completed')
                    ->groupBy('game_level_id')
                    ->get();

                $total_score_int = $level_scores->sum('highest');
                $total_score     = "{$total_score_int}/{$total_possible}";

                // Average score (works fine now)
                $average_score = UserLevel::where('user_id', $individual_user->id)
                    ->where('status', 'Completed')
                    ->avg('score') ?? 0;

                $average = number_format($average_score, 2);

                // Attempts
                $attempts = UserLevel::where('user_id', $individual_user->id)
                    ->where('status', 'Completed')
                    ->count();

                $leaderboard[] = [
                    'username'    => $individual_user->username,
                    'section'     => $GnS,
                    'stars'       => $stars,
                    'total_score' => $total_score,
                    'average'     => $average,
                    'attempts'    => $attempts,
                    'ranking'     => 0,
                ];
            }
        }

        // Sort leaderboard by total score (descending)
        usort($leaderboard, function ($a, $b) {
            $aScore = intval(explode('/', $a['total_score'])[0]);
            $bScore = intval(explode('/', $b['total_score'])[0]);
            return $bScore <=> $aScore;
        });

        // Assign ranks
        foreach ($leaderboard as $index => &$entry) {
            $entry['ranking'] = $index + 1;
        }

        return response()->json([
            'status'      => 'success',
            'message'     => 'Leaderboard retrieved',
            'leaderboard' => $leaderboard,
        ]);
    }

   public function SummativeQuestionsRequest(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'novel' => 'required|exists:novels,novel_name',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Invalid data',
                    'errors'  => $validator->errors(),
                ], 422);
            }

            $novel = Novel::where('novel_name', $request->novel)->first();

            if (! $novel) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Novel not found',
                ], 404);
            }

            /**
             * Fetch ALL levels that belong to this novel
             * via game_levels → levels
             */
            $levels = Level::whereHas('gameLevel', function ($query) use ($novel) {
                    $query->where('novel_id', $novel->id);
                })
                ->inRandomOrder()
                ->limit(50)
                ->get();

            $questionsData = [];

            foreach ($levels as $level) {
                $questionsData[] = [
                    'id'              => $level->id,
                    'type'            => $level->type,
                    'question'        => $level->question,
                    'answer'          => $level->answer,
                    'otherAnswers'    => $level->type === 'Multiple'
                        ? [$level->ans1, $level->ans2, $level->ans3]
                        : [],
                    'rationalization' => $level->rationalization,
                ];
            }

            return response()->json([
                'status' => 'success',
                'data'   => [
                    'questions' => $questionsData
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

}
