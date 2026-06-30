<?php

namespace App\Http\Controllers;

use App\Models\AdditionalChoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class AdditionalChoicesController extends Controller
{
    public function showChoices($id)
    {
        try {
            $Level_ID = Crypt::decrypt($id);

            $choices = AdditionalChoice::where('level_id', $Level_ID)->get();
            // /dd($choices);
            return view('choices_dashboard', compact('choices','Level_ID'));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to find the level.']);
        }
    }

    public function Add(Request $request)
    {
        try {
            $request->validate([
                'choice' => 'string|required',
                'level_id' => 'int|required',
            ]);

            AdditionalChoice::create([
                'choice' => $request->choice,
                'level_id' => $request->level_id, // Make sure this is included in the form!
            ]);

            return redirect()->back()->with('message', 'New choice has been successfully added!');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'There was a problem while inserting data. Please try again!']);
        }
    }

    public function Edit(Request $request)
    {
        try {
            $request->validate([
                'id' => 'int|required',
                'choice' => 'string|required',
            ]);

            $choice = AdditionalChoice::findOrFail($request->id);

            $choice->update([
                'choice' => $request->choice,
            ]);

            return redirect()->back()->with('message', 'Data has been successfully updated!');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'There was a problem while updating data. Please try again!']);
        }
    }

    public function Delete(Request $request)
    {
        try {
            $request->validate([
                'id' => 'int|required',
            ]);

            $choice = AdditionalChoice::findOrFail($request->id);
            $choice->delete();

            return redirect()->back()->with('message', 'Choice has been successfully deleted.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'There was a problem while deleting the data. Please try again!']);
        }
    }
}
