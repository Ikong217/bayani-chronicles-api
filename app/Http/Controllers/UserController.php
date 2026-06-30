<?php

namespace App\Http\Controllers;

use App\Mail\UserMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;

class UserController extends Controller
{
    public function showUsers()
    {
        $users = User::get();
        return view('user_dashboard', compact('users'));
    }

    // ADD USER
    public function Create(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'username' => 'string|required|unique:users,username',
                'gender'   => 'string|required',
                'email'    => [
                    'required',
                    'email',
                    'unique:users,email',
                    'regex:/^[a-zA-Z0-9.$&*_\-+]+@gmail\.com$/'
                ],
                'bdate'    => 'required|date',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator->errors());
            }

            $bdate = Carbon::parse($request->bdate)->format('Y-m-d');
            $hashedPassword = Hash::make($bdate);

            $user = User::create([
                'username' => $request->username,
                'email'    => $request->email,
                'gender'   => strtolower($request->gender),
                'birthday' => $bdate,
                'password' => $hashedPassword,
            ]);

            // Send email with password
            Mail::to($request->email)->send(new UserMail($request->username, $bdate));

            return redirect()->back()->with('message', "Successfully Added a user");

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['critical' => 'There was an error while processing the request, please try again']);
        }
    }

    // UPDATE USER
    public function Update(Request $request)
    {
        try {
            $user = User::find($request->id);

            if (!$user) {
                return redirect()->back()->withErrors(['unknown' => 'Unable to find user, please check the source!']);
            }

            $validator = Validator::make($request->all(), [
                'username' => 'string|required|unique:users,username,' . $user->id,
                'gender'   => 'string|required',
                'email'    => [
                    'required',
                    'email',
                    'required|unique:users,email,' . $user->id,
                    'regex:/^[a-zA-Z0-9.$&*_\-+]+@gmail\.com$/'
                ],
                'bdate'    => 'required|date',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator->errors());
            }

            //dd($request);

            // Send email (no password change)
            Mail::to($request->email)->send(new UserMail($request->username, null, 'update'));

            $bdate = Carbon::parse($request->bdate)->format('Y-m-d');
            $user->update([
                'username' => $request->username,
                'email'    => $request->email,
                'gender'   => strtolower($request->gender),
                'birthday' => $bdate
            ]);

            return redirect()->back()->with('message', "Successfully Updated user: {$user->username}");

        } catch (\Exception $e) {
            //dd($e);
            return redirect()->back()->withErrors(['error' => 'There was an error while processing the request, please try again']);
        }
    }

    // DELETE USER
    public function Delete(Request $request)
    {
        try {
            $user = User::find($request->id);

            if (!$user) {
                return redirect()->back()->withErrors(['unknown' => 'Unable to find user, please check the source!!']);
            }

            $username = $user->username;
            $user->delete();

            return redirect()->back()->with('message', "Successfully Deleted a user: {$username}");

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['critical' => 'There was an error while processing request, please try again']);
        }
    }

    // BAN/UNBAN
    public function Ban(Request $request)
    {
        try {
            $user = User::findOrFail($request->id);

            $user->isBanned = !$user->isBanned;
            $user->save();

            $status = $user->isBanned ? 'Banned' : 'Unbanned';
            return redirect()->back()->with('message', "The user has been successfully {$status}.");

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['critical' => 'There was a problem while updating the user status. Please try again!']);
        }
    }

    // IMPORT USERS
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt,xlsx,xls|max:5120',
        ]);

        try {
            $extension = strtolower($request->file('file')->getClientOriginalExtension());
            $expectedHeader = ['username', 'gender', 'email', 'birthdate'];
            $rows = [];

            $normalizeCell = function ($h) {
                $h = trim((string)$h);
                $h = preg_replace('/^\xEF\xBB\xBF/', '', $h);
                $h = str_replace("\xC2\xA0", ' ', $h);
                $h = preg_replace('/[\x00-\x1F\x7F]/u', '', $h);
                return mb_strtolower($h);
            };

            // READ FILE
            if (in_array($extension, ['csv', 'txt'])) {
                $path = $request->file('file')->getRealPath();
                $handle = fopen($path, 'r');
                if (!$handle) return back()->with('error', 'Could not open uploaded file.');

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
                    if (count(array_filter($data)) > 0) $rows[] = $data;
                }

                fclose($handle);

            } else {
                $spreadsheet = IOFactory::load($request->file('file')->getRealPath());
                $sheet = $spreadsheet->getActiveSheet();
                $sheetRows = array_values(array_filter($sheet->toArray(null, true, true, false), fn($r) => count(array_filter($r)) > 0));
                if (count($sheetRows) === 0) return back()->with('error', 'The file is empty or unreadable.');

                $header = array_map($normalizeCell, $sheetRows[0]);
                if ($header !== $expectedHeader) return back()->with('error', 'Invalid header.');

                array_shift($sheetRows);
                $rows = $sheetRows;
            }

            if (empty($rows)) return back()->with('error', 'No data rows found.');

            DB::beginTransaction();

            $rowCount = 0; $duplicateCount = 0; $skippedRows = 0;

            foreach ($rows as $row) {
                [$username, $gender, $email, $birthday] = array_pad($row, 4, null);
                $username = trim($username);
                $email = trim($email);
                $gender = trim($gender);
                $birthday = trim($birthday);

                if (empty($username) || empty($email)) { $skippedRows++; continue; }
                if (User::where('username', $username)->orWhere('email', $email)->exists()) { $duplicateCount++; continue; }

                $validator = Validator::make([
                    'username' => $username,
                    'gender' => $gender,
                    'email' => $email,
                    'birthday' => $birthday
                ], [
                    'username' => 'string|required',
                    'gender' => 'string|required',
                    'email'    => [
                        'required',
                        'email',
                        'unique:users,email',
                        'regex:/^[a-zA-Z0-9.$&*_\-+]+@gmail\.com$/'
                    ],
                    'birthday' => 'date|required'
                ]);

                if ($validator->fails()) { $skippedRows++; continue; }

                $genderLower = strtolower($gender);
                if (in_array($genderLower, ['male','m','he','him'])) $gender='male';
                elseif (in_array($genderLower, ['female','f','she','her','fm'])) $gender='female';
                else $gender='male';

                try { $parsedDate = Carbon::parse($birthday)->format('Y-m-d'); }
                catch (\Exception $e) { $skippedRows++; continue; }

                $password = Hash::make($parsedDate);

                $user = User::create([
                    'username' => $username,
                    'email' => $email,
                    'birthday' => $parsedDate,
                    'password' => $password,
                    'gender' => $gender,
                ]);

                try {
                    Mail::to($email)->send(new UserMail($username, $parsedDate));
                } catch (\Exception) {}

                $rowCount++;
            }

            DB::commit();

            $message = "✅ Import successful! $rowCount rows processed.";
            if ($skippedRows>0 || $duplicateCount>0) $message .= " ⚠️ $skippedRows skipped, $duplicateCount duplicates found.";

            return back()->with('message', $message);

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', '❌ Import failed: ' . $e->getMessage());
        }
    }
}
