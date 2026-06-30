<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Section;
use App\Helper\AdminChecker;
use App\Models\Admin;
use App\Models\AppliedSection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\UserLevel;
use App\Models\UserInGameData;

class SectionUsersController extends Controller
{
    //
    public function Show($id)
    {
        try {
            if (AdminChecker::allowCurrentTeacher($id) || AdminChecker::isAdmin(id: Auth::guard('teacher')->user()->id)) {
                $section = Section::findOrFail($id);
                $users = AppliedSection::where('section_id', $id)->get();
                $students = User::all();

                // === LEADERBOARD LOGIC ===
                $grade_level = $section->grade_level;
                $leaderboard = [];

                // Get all sections with same grade level
                $all_sections = Section::where('grade_level', $grade_level)->get();

                foreach ($all_sections as $sect) {
                    $applied_users = AppliedSection::where('section_id', $sect->id)->get();

                    foreach ($applied_users as $applied) {
                        $individual_user = User::find($applied->user_id);
                        if (!$individual_user) continue;

                        $GnS = $sect->grade_level . " - " . $sect->section_name;
                        $user_igd = UserInGameData::where('user_id', $individual_user->id)->first();
                        if (!$user_igd) continue;

                        $jsonData = json_decode($user_igd->levels, true);
                        if (!$jsonData) continue;

                        $levels = [];

                        // Helper to safely decode either array or JSON string
                        $safeDecode = function ($data) {
                            if (is_string($data)) {
                                $decoded = json_decode($data, true);
                                return is_array($decoded) ? $decoded : null;
                            }
                            return is_array($data) ? $data : null;
                        };

                        // Grade 10: merge Noli + El Fili
                        if ($sect->grade_level == "Grade - 10") {
                            $noli = isset($jsonData['noli']) ? $safeDecode($jsonData['noli']) : null;
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
                        $stars = 0;
                        $total_possible = count($levels) * 10;

                        foreach ($levels as $level) {
                            $stars += intval($level['stars']);
                        }

                        // Calculate total score
                        $level_scores = UserLevel::selectRaw('MAX(score) as highest')
                            ->where('user_id', $individual_user->id)
                            ->where('status', 'Completed')
                            ->groupBy('game_level_id')
                            ->get();

                        $total_score_int = $level_scores->sum('highest');
                        $total_score = "{$total_score_int}/{$total_possible}";

                        // Average score
                        $average_score = UserLevel::where('user_id', $individual_user->id)
                            ->where('status', 'Completed')
                            ->avg('score') ?? 0;

                        $average = number_format($average_score, 2);

                        // Attempts
                        $attempts = UserLevel::where('user_id', $individual_user->id)
                            ->where('status', 'Completed')
                            ->count();

                        $leaderboard[] = [
                            'username' => $individual_user->username,
                            'section' => $GnS,
                            'stars' => $stars,
                            'total_score' => $total_score,
                            'average' => $average,
                            'attempts' => $attempts,
                            'total_score_int' => $total_score_int,
                        ];
                    }
                }

                // Sort by total_score_int (descending), then by stars
                usort($leaderboard, function($a, $b) {
                    if ($a['total_score_int'] == $b['total_score_int']) {
                        return $b['stars'] - $a['stars'];
                    }
                    return $b['total_score_int'] - $a['total_score_int'];
                });

                // Assign rankings
                foreach ($leaderboard as $index => &$entry) {
                    $entry['ranking'] = $index + 1;
                }

                return view('section_users', compact('users', 'section', 'students', 'leaderboard', 'grade_level'));
            }
            return redirect()->back()->withErrors(['unauthorized' => 'This Section is missing in your List']);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['invalid' => 'Something went wrong, please try again']);
        }
    }

    public function Add(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:users,id',
                'section_id'   => 'required|exists:sections,id',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator->errors());
            }

            if(AdminChecker::userHasSection($request->id)){
                return redirect()->back()->withErrors(['invalid'=>'This user Already Has a section Please try again']);
            }

            AppliedSection::create([
                'user_id'   => $request->id,
                'section_id'  => $request->section_id,
            ]);

            return redirect()->back()->with('message', 'Successfully Added Section');

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['critical' => 'there was an errror while processing request, please try again : '.$e->getMessage()]);
        }
    }

    public function Remove(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator->errors());
            }

            $section = AppliedSection::where('user_id',$request->id)->first();

            $section->delete();

            return redirect()->back()->with('message', 'Successfully Removed User');

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['critical' => 'there was an errror while processing request, please try again : ']);
        }
    }
}
