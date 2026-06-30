<?php
namespace App\Http\Controllers;

use App\Models\GameLevel;
use App\Models\Level;
use App\Models\Novel;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;

class QuestionController extends Controller
{
    //
    public function showQuestions(string $novel = "All", string $level = "All", string $type = "All")
    {
        // For filters/navigation
        $novels = Novel::all();
        $levels = [];
        $types  = ["Multiple", "Identification", "ToF"];

        // Base query
        $query = Level::query();

        // Filter by Novel (optional)
        $tmp_Novel = null;
        if ($novel !== "All" && $novel !== "") {
            $tmp_Novel = Novel::where('novel_name', $novel)->first();
            if ($tmp_Novel) {
                $query->whereHas('gamelevel', function ($q) use ($tmp_Novel) {
                    $q->where('novel_id', $tmp_Novel->id);
                });
                $levels = GameLevel::where('novel_id', $tmp_Novel->id)->get();
            } else {
                $novel = "All"; // Reset if not found
            }
        }

        // Filter by Level (optional)
        $tmp_Level = null;
        if ($level !== "All" && $level !== "") {
            $tmp_Level = GameLevel::where('level_name', $level)->first();
            if ($tmp_Level) {
                $query->where('game_level_id', $tmp_Level->id);
            } else {
                $level = "All"; // Reset if not found
            }
        }

        // Filter by Type (optional)
        if ($type !== "All" && $type !== "") {
            if (in_array($type, $types)) {
                $query->where('type', $type);
            } else {
                $type = "All"; // Reset if invalid
            }
        }

        // Execute final query
        $questions = $query->orderBy('id', 'desc')->get();

        // Pass to view
        return view('questions_dashboard', compact('questions', 'novels', 'levels', 'types', 'novel', 'level', 'type'));
    }

    public function getLevels(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:novels,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => "error",
                    'message' => 'Invalid novel selected',
                ], 422); // Added proper HTTP status code
            }

            $levels = GameLevel::where('novel_id', $request->id)
                ->select('id', 'level_name')
                ->get();

            return response()->json([
                'status' => 'success',
                'levels' => $levels,
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status'  => "error",
                'message' => 'Server error: ' . $e->getMessage(),
            ], 500); // Added proper HTTP status code
        }
    }

    public function Add(Request $request)
    {
        try {
            // Base rules
            $rules = [
                'level'    => 'required|exists:game_levels,id',
                'type'     => 'required|in:Multiple,Identification,ToF',
                'question' => 'required|string',
                'rationalization' => 'required|string',
            ];

            // Add rules depending on question type
            if ($request->type === "Multiple") {
                $rules = array_merge($rules, [
                    'answer' => 'required|string',
                    'ans1'   => 'required|string',
                    'ans2'   => 'required|string',
                    'ans3'   => 'required|string',
                ]);
            } elseif ($request->type === "Identification") {
                $rules = array_merge($rules, [
                    'answer' => 'required|string',
                ]);
            } elseif ($request->type === "ToF") {
                $rules = array_merge($rules, [
                    'answer' => 'required|string|in:true,false',
                ]);
            }

            // Validate
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator->errors());
            }

            // Save
            Level::create([
                'game_level_id' => $request->level,
                'question'      => $request->question,
                'type'          => $request->type,
                'answer'        => $request->answer,
                'rationalization' => $request->rationalization,
                'ans1'          => $request->ans1 ?? "",
                'ans2'          => $request->ans2 ?? "",
                'ans3'          => $request->ans3 ?? "",
            ]);

            return redirect()->back()->with('message', 'New Question has been successfully added !!');
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['exceptions' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function Edit(Request $request)
    {
        try {
            // Validation rules
            $rules = [
                'id'       => 'required|exists:levels,id',
                'level'    => 'required|exists:game_levels,id',
                'type'     => 'required|in:Multiple,Identification,ToF',
                'question' => 'required|string',
                'rationalization' => 'required|string',
            ];

            // Type-specific rules
            switch ($request->type) {
                case "Multiple":
                    $rules = array_merge($rules, [
                        'answer' => 'required|string',
                        'ans1'   => 'required|string',
                        'ans2'   => 'required|string',
                        'ans3'   => 'required|string',
                    ]);
                    break;
                case "Identification":
                    $rules = array_merge($rules, [
                        'answer' => 'required|string',
                    ]);
                    break;
                case "ToF":
                    $rules = array_merge($rules, [
                        'answer' => 'required|string|in:true,false',
                    ]);
                    break;
            }

            // Validate inputs
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            // Fetch the question
            $question = Level::findOrFail($request->id);

            // Update data
            $question->update([
                'game_level_id' => $request->level,
                'question'      => $request->question,
                'type'          => $request->type,
                'rationalization' => $request->rationalization,
                'answer'        => $request->answer,
                'ans1'          => $request->ans1 ?? "",
                'ans2'          => $request->ans2 ?? "",
                'ans3'          => $request->ans3 ?? "",
            ]);

            return redirect()->back()->with('message', 'Question has been successfully updated!');
        } catch (Exception $e) {
            return redirect()->back()->withErrors([
                'exceptions' => 'Error: ' . $e->getMessage(),
            ])->withInput();
        }
    }

    public function Delete(Request $request)
    {
        try {
            $request->validate([
                'id' => 'int|required',
            ]);

            $teacher = Level::findOrFail($request->id);
            $teacher->delete();

            return redirect()->back()->with('message', 'Question has been successfully deleted.');
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['exceptions' => 'Error: ' . $e->getMessage()]);
        }
    }
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt,xlsx,xls|max:5120',
        ]);

        try {
            $extension      = strtolower($request->file('file')->getClientOriginalExtension());
            $expectedHeader = ['novel', 'level', 'type', 'question', 'answer', 'choice1', 'choice2', 'choice3','rationalization'];
            $rows           = [];

            $normalizeCell = function ($h) {
                $h = trim((string) $h);
                $h = preg_replace('/^\xEF\xBB\xBF/', '', $h);    // remove UTF-8 BOM
                $h = str_replace("\xC2\xA0", ' ', $h);           // replace non-breaking space
                $h = preg_replace('/[\x00-\x1F\x7F]/u', '', $h); // remove control chars
                return mb_strtolower($h);
            };

            // STEP 1: READ FILE
            if (in_array($extension, ['csv', 'txt'])) {
                $path   = $request->file('file')->getRealPath();
                $handle = fopen($path, 'r');

                if (! $handle) {
                    return back()->with('error', 'Could not open uploaded file.');
                }

                $header = fgetcsv($handle);
                if ($header === false) {
                    fclose($handle);
                    return back()->with('error', 'CSV is empty or invalid.');
                }

                $header = array_map($normalizeCell, $header);
                if ($header !== $expectedHeader) {
                    fclose($handle);
                    return back()->with('error', 'Invalid header. Expected: ' . implode(', ', $expectedHeader) . '. Found: ' . implode(', ', $header));
                }

                while (($data = fgetcsv($handle)) !== false) {
                    if (count(array_filter($data)) > 0) {
                        $rows[] = $data;
                    }
                }

                fclose($handle);
            } else {
                $spreadsheet = IOFactory::load($request->file('file')->getRealPath());
                $sheet       = $spreadsheet->getActiveSheet();
                $sheetRows   = array_values(array_filter($sheet->toArray(null, true, true, false), fn($r) => count(array_filter($r)) > 0));

                if (count($sheetRows) === 0) {
                    return back()->with('error', 'The file is empty or unreadable.');
                }

                $header = array_map($normalizeCell, $sheetRows[0]);
                if ($header !== $expectedHeader) {
                    return back()->with('error', 'Invalid header. Expected: ' . implode(', ', $expectedHeader) . '. Found: ' . implode(', ', $header));
                }

                array_shift($sheetRows); // drop header
                $rows = $sheetRows;
            }

            if (empty($rows)) {
                return back()->with('error', 'No data rows found.');
            }

            // STEP 2: PROCESS DATA
            DB::beginTransaction();

            $rowCount       = 0;
            $skippedRows    = 0;
            $duplicateCount = 0;

            foreach ($rows as $row) {
                if (count(array_filter($row)) < 5) {
                    $skippedRows++;
                    continue;
                }

                [$novel, $level, $type, $question, $answer, $choice1, $choice2, $choice3, $rationalization] = array_pad($row, 9, null);

                $novelRaw    = strtolower(trim($novel));
                $levelRaw    = strtolower(trim($level));
                $type     = strtolower(trim($type));
                $rationalization = trim($rationalization);
                $question = trim($question);
                $answer   = trim($answer);

                $novel = $novelRaw;

                if(preg_match('/\b^(noli\s*me\s*tangere|noli\s*me|noli|no|n)$\b/i',$novelRaw)){
                    //dd("uhaw");
                    $novel = 'noli me tangere';
                }elseif(preg_match('/\b^(el\s*filibusterismo|el\s*fili|el\s*fi|el|e|ef)$\b/i',$novelRaw)){
                    $novel = 'el filibusterismo';
                }else{
                    //avoid creating new novels. alisin nalang pag kaylangan
                    $skippedRows++;
                    continue;
                }

                // Apply preg_replace_callback safely
                $level = preg_replace_callback(
                    '/\b(level|lvl|lv|l)?\s*(\d+)\b/i', // optional shortcut + optional space + number
                    function ($m) {
                        return 'level ' . $m[2]; // always output level + number
                    },
                    $levelRaw
                );

                $Novel = Novel::whereRaw('LOWER(novel_name) = ?', [$novel])->first();
                if (! $Novel) {
                    $skippedRows++;
                    continue;
                }

                $Level = GameLevel::whereRaw('LOWER(level_name) = ?', [$level])
                    ->where('novel_id', $Novel->id)
                    ->first();
                if (! $Level) {
                    $skippedRows++;
                    continue;
                }

                if (empty($question) || empty($answer) || empty($rationalization)) {
                    $skippedRows++;
                    continue;
                }

                if (in_array($type, ['multiple', 'multiple choice', 'mc', 'abcd'])) {
                    $type = "Multiple";
                    if (empty($choice1) || empty($choice2) || empty($choice3)) {
                        $skippedRows++;
                        continue;
                    }
                } elseif (in_array($type, ['tof', 'true or false', 'tf', 'true/false'])) {
                    $type   = 'ToF';
                    $answer = strtolower($answer);
                    if (in_array($answer, ['true', '1', 't'])) {
                        $answer = 'true';
                    } elseif (in_array($answer, ['false', '0', 'f'])) {
                        $answer = 'false';
                    } else {
                        $skippedRows++;
                        continue;
                    }
                    $choice1 = $choice2 = $choice3 = "";
                } elseif (in_array($type, ['identification', 'id', 'identify', 'identi'])) {
                    $type    = 'Identification';
                    $choice1 = $choice2 = $choice3 = "";
                } else {
                    $skippedRows++;
                    continue;
                }

                $exists = Level::where('question', $question)
                    ->where('game_level_id', $Level->id)
                    ->where('type', $type)
                    ->exists();

                if ($exists) {
                    $duplicateCount++;
                }

                Level::updateOrCreate(
                    [
                        'game_level_id' => $Level->id,
                        'type'          => $type,
                        'question'      => $question,
                    ],
                    [
                        'answer' => $answer,
                        'ans1'   => $choice1,
                        'ans2'   => $choice2,
                        'ans3'   => $choice3,
                        'rationalization' => $rationalization,
                    ]
                );

                $rowCount++;
            }

            DB::commit();

            if ($rowCount === 0) {
                return back()->with('error', '⚠️ No valid rows were imported.');
            }

            $message = "✅ Import successful! $rowCount rows processed.";
            if ($skippedRows > 0 || $duplicateCount > 0) {
                $message .= " ⚠️ $skippedRows skipped, $duplicateCount updated.";
            }

            return back()->with('message', $message);
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', '❌ Import failed: ' . $e->getMessage());
        }
    }

}
