<?php
namespace App\Http\Controllers;

use App\Helper\AdminChecker;
use App\Mail\UserMail;
use App\Models\Admin;
use App\Models\Teacher;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;

class TeacherController extends Controller
{
    public function showTeachers()
    {
        $teachers = Teacher::get();
        return view('teachers_dashboard', compact('teachers'));
    }

    // ADD TEACHER
    public function Add(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'profile'  => 'nullable|file|mimes:png,jpg,jpeg|max:2048', // make nullable and limit size
                'username' => 'string|required|unique:teachers,name',
                'email'    => [
                    'required',
                    'email',
                    'unique:teachers,email',
                    'regex:/^[a-zA-Z0-9.$&*_\-+]+@gmail\.com$/'
                ],
                'contact'  => 'string|required',
                'bdate'    => 'required|date',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator->errors());
            }

            // Handle profile upload
            $profileName = null;
            if ($request->hasFile('profile')) {
                $file        = $request->file('profile');
                $profileName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('assets/images/profiles'), $profileName); // store in public/uploads/profiles
            }

            $bdate          = Carbon::parse($request->bdate)->format('Y-m-d');
            $hashedPassword = Hash::make($bdate);

            $teacher = Teacher::create([
                'name'     => $request->username,
                'email'    => $request->email,
                'contact'  => $request->contact,
                'birthday' => $bdate,
                'password' => $hashedPassword,
                'profile'  => $profileName, // save profile name
            ]);

            // Send email with password
            Mail::to($request->email)->send(new UserMail($request->username, $bdate));

            return redirect()->back()->with('message', 'New teacher has been successfully added!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', "Error: " . $e->getMessage());
        }
    }

    // UPDATE TEACHER
    public function Edit(Request $request)
    {
        try {
            // Validate request
            $request->validate([
                'id'      => 'int|required',
                'profile' => 'nullable|file|mimes:png,jpg,jpeg|max:2048',
                'name'    => 'string|required|unique:teachers,name,' . $request->id,
                'email'    => [
                    'required',
                    'email',
                    'unique:teachers,name,' . $request->id,
                    'regex:/^[a-zA-Z0-9.$&*_\-+]+@gmail\.com$/'
                ],
                'contact' => 'string|required',
                'bdate'   => 'required|date',
            ]);

            $teacher = Teacher::findOrFail($request->id);
            $bdate   = Carbon::parse($request->bdate)->format('Y-m-d');

            $profileName = $teacher->profile; // keep existing profile by default

            // Handle profile update if a new file is uploaded
            if ($request->hasFile('profile')) {
                // Delete old profile if it exists
                if ($teacher->profile && file_exists(public_path('assets/images/profiles/' . $teacher->profile))) {
                    unlink(public_path('assets/images/profiles/' . $teacher->profile));
                }

                // Save new profile
                $file        = $request->file('profile');
                $profileName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('assets/images/profiles'), $profileName);
            }

            // Update teacher record
            $teacher->update([
                'name'     => $request->name,
                'email'    => $request->email,
                'contact'  => $request->contact,
                'birthday' => $bdate,
                'profile'  => $profileName, // save new or existing profile
            ]);

            // Optional: send notification email for update
            Mail::to($request->email)->send(new UserMail($request->name, null, 'update'));

            return redirect()->back()->with('message', 'Teacher data has been successfully updated!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'There was a problem while updating data. Please try again!');
        }
    }

    // DELETE TEACHER
    public function Delete(Request $request)
    {
        try {
            $request->validate([
                'id' => 'int|required',
            ]);

            if (AdminChecker::isCurrentUser($request->id)) {
                return redirect()->back()->withErrors(['OnLog' => 'You Cannot Delete yourself']);
            }

            $teacher = Teacher::findOrFail($request->id);

            // Delete profile image if exists
            if ($teacher->profile && file_exists(public_path('assets/images/profiles/' . $teacher->profile))) {
                unlink(public_path('assets/images/profiles/' . $teacher->profile));
            }

            $teacher->delete();

            return redirect()->back()->with('message', 'Teacher has been successfully deleted.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'There was a problem while deleting the data. Please try again!');
        }
    }

    public function Promotion(Request $request)
    {
        try {
            $request->validate([
                'id' => 'int|required',
            ]);

            if (AdminChecker::isCurrentUser($request->id)) {
                return redirect()->back()->withErrors(['OnLog' => 'You Cannot Demote yourself']);
            }
            $message = "demoted";
            if (! AdminChecker::isAdmin($request->id)) {
                Admin::create(['teacher_id' => $request->id]);
                $message = "promoted";
            } else {
                $admin = Admin::where('teacher_id', $request->id);
                $admin->delete();
            }

            return redirect()->back()->with('message', 'Teacher has been successfully ' . $message . '.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'There was a problem while deleting the data. Please try again!');
        }
    }

    // IMPORT TEACHERS
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt,xlsx,xls|max:5120',
        ]);

        try {
            $extension      = strtolower($request->file('file')->getClientOriginalExtension());
            $expectedHeader = ['username', 'email', 'contact', 'birthdate'];
            $rows           = [];

            $normalizeCell = function ($h) {
                $h = trim((string) $h);
                $h = preg_replace('/^\xEF\xBB\xBF/', '', $h);
                $h = str_replace("\xC2\xA0", ' ', $h);
                $h = preg_replace('/[\x00-\x1F\x7F]/u', '', $h);
                return mb_strtolower($h);
            };

            // READ FILE
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
                    return back()->with('error', 'Invalid header. Expected: ' . implode(', ', $expectedHeader));
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
                    return back()->with('error', 'Invalid header.');
                }

                array_shift($sheetRows);
                $rows = $sheetRows;
            }

            if (empty($rows)) {
                return back()->with('error', 'No data rows found.');
            }

            DB::beginTransaction();

            $rowCount       = 0;
            $duplicateCount = 0;
            $skippedRows    = 0;

            foreach ($rows as $row) {
                [$username, $email, $contact, $birthday] = array_pad($row, 4, null);
                $username                                = trim($username);
                $email                                   = trim($email);
                $contact                                 = trim($contact);
                $birthday                                = trim($birthday);

                if (empty($username) || empty($email)) {$skippedRows++;
                    continue;}
                if (Teacher::where('name', $username)->orWhere('email', $email)->exists()) {$duplicateCount++;
                    continue;}

                $validator = Validator::make([
                    'username' => $username,
                    'email'    => $email,
                    'contact'  => $contact,
                    'birthday' => $birthday,
                ], [
                    'username' => 'string|required',
                    'email'    => [
                        'required',
                        'email',
                        'unique:teachers,email',
                        'regex:/^[a-zA-Z0-9.$&*_\-+]+@gmail\.com$/'
                    ],
                    'contact'  => 'string|required',
                    'birthday' => 'date|required',
                ]);

                if ($validator->fails()) {$skippedRows++;
                    continue;}

                try { $parsedDate = Carbon::parse($birthday)->format('Y-m-d');} catch (\Exception $e) {$skippedRows++;
                    continue;}

                $password = Hash::make($parsedDate);

                $teacher = Teacher::create([
                    'name'     => $username,
                    'email'    => $email,
                    'contact'  => $contact,
                    'birthday' => $parsedDate,
                    'password' => $password,
                ]);

                try {
                    Mail::to($email)->send(new UserMail($username, $parsedDate));
                } catch (\Exception) {}

                $rowCount++;
            }

            DB::commit();

            $message = "✅ Import successful! $rowCount rows processed.";
            if ($skippedRows > 0 || $duplicateCount > 0) {
                $message .= " ⚠️ $skippedRows skipped, $duplicateCount duplicates found.";
            }

            return back()->with('message', $message);

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', '❌ Import failed: ' . $e->getMessage());
        }
    }
}
