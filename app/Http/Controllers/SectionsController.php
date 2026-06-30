<?php
namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SectionsController extends Controller
{
    //
    public function SectionShow()
    {
        $teachers = Teacher::all();
        $sections = Section::all();
        return view('section_dashboard', compact('teachers', 'sections'));
    }

    public function Create(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'teacher' => 'required',
                'grade'   => 'string|required',
                'section' => 'string|required|unique:sections,section_name',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator->errors());
            }

            $teacher = Teacher::findOrFail($request->teacher);

            Section::create([
                'teacher_id'   => $teacher->id,
                'grade_level'  => $request->grade,
                'section_name' => $request->section,
            ]);

            return redirect()->back()->with('message', 'Successfully Added Section');

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['critical' => 'there was an errror while processing request, please try again : ']);
        }
    }

    public function Delete(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator->errors());
            }

            $section = Section::findOrFail($request->id);

            $section->delete();

            return redirect()->back()->with('message', 'Successfully Deleted Section');

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['critical' => 'there was an errror while processing request, please try again : ']);
        }
    }

    public function Update(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id'      => 'required|exists:sections,id',
                'teacher' => 'required|exists:teachers,id',
                'grade'   => 'string|required',
                'section' => 'string|required|unique:sections,section_name,' . $request->id . ',id',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator->errors());
            }

            $teacher = Teacher::findOrFail($request->teacher);

            Section::where('id', $request->id)->update([
                'teacher_id'   => $teacher->id,
                'grade_level'  => $request->grade,
                'section_name' => $request->section,
            ]);

            return redirect()->back()->with('message', 'Successfully Updated Section');

        } catch (\Exception $e) {
            return redirect()->back()->withErrors([
                'critical' => 'There was an error while processing the request, please try again.',
            ]);
        }
    }

}
