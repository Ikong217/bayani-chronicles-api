<?php

use App\Http\Controllers\AdditionalChoicesController;
use App\Http\Controllers\ForgotPassController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MiscController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\SectionDataController;
use App\Http\Controllers\SectionsController;
use App\Http\Controllers\SectionUsersController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\unityInGameController;
use App\Http\Controllers\UnityUsersAuth;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserScoresController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [LoginController::class, 'Remember']);
Route::get('/terms-and-condition', [MiscController::class, 'ShowTNC'])->name('tnc.show');
Route::get('/Download/{storage}/{filename}', [MiscController::class, 'download'])->name('file.download');

//Data from unity and user login
Route::controller(UnityUsersAuth::class)->group(function () {
    //Route::post('api/user/insert', 'insertData')->name('unity.insertUser');
    Route::post('api/user/login', 'logUser')->name('unity.loginUser');
    Route::post('/user/verify-otp', 'VerifyOtp')->name('unity.verifyOtp');
    Route::post('/user/resend-otp', 'resendOtp')->name('unity.resendOtp');
    Route::post('/player/access/request','RequestAccess')->name('player.access.request');
    Route::post('api/user/request/username/change','UserChangeUsername')->name('user.request.username.change');
    Route::post('api/user/request/change/email','ChangeNewEmail')->name('user.request.email.change');
    Route::post('api/user/request/change/email/awaitcode','AwaitCodeSuccess')->name('user.request.email.change.awaitcode');
    Route::post('/api/user/request/change/email/verify','EmailVerifyCode')->name('user.request.email.change.verify');
});

Route::get('/Email/Code/Verify/{encryptedId}/{code}', [MiscController::class, 'OldEmailVerify'])->name('user.email.code.verify');

//ingame requests for unity
Route::controller(unityInGameController::class)->group(function () {
    Route::post('/questions/request', 'QuestionsRequest')->name('unity.questions.request');
    Route::post('/score/start', 'ScoreCreate')->name('unity.score.start');
    Route::post('/score/update', 'ScoreUpdate')->name(name: 'unity.score.update');
    Route::post('/player/data/request', 'SaveUserInGameData')->name(name: 'unity.data.save');
    Route::post('/leaderboard/request','Leaderboard')->name('unity.leaderboard.request');
    Route::post('/questions/summative/request','SummativeQuestionsRequest')->name('unity.question.summative.request');
});

// middleware for teachers and admin
Route::middleware('teacher')->group(function () {

    //dashboard
    Route::get('/Dashboard', [MiscController::class, 'DashboardShow'])->name('dashboard');

    //middleware for admin only
    Route::middleware('admin')->group(function () {
        //TeacherController for teacher dashboard
        Route::controller(TeacherController::class)->group(function () {
            Route::get('/Teachers/List', 'showTeachers')->name('teachers.show');
            Route::post('/teacher/add', 'Add')->name('teacher.add');
            Route::put('/Teacher/update', 'Edit')->name('teacher.update');
            Route::post('/Teacher/delete', 'Delete')->name('teacher.delete');
            Route::post('/Teacher/promotion', 'Promotion')->name('teacher.promotion');
            Route::post('Teacher/Import/Excell','import')->name('teacher.import');
        });

        //Route for testing areas
        Route::controller(TestController::class)->group(function () {
            Route::get('/TestArea/Test1', 'Test1')->name('test.1');
            Route::post("/TestArea/Request/All", 'allRequest')->name('request.all');
            Route::post('/TestArea/GetRequest', 'GetRequest')->name('test.request.get');
        });

        Route::get('/Admin/Audit',[MiscController::class,'ShowAudit'])->name('admin.audit');
    });

    //Route for Profile
    Route::controller(ProfileController::class)->group(function(){
        Route::get('/Dashboard/Profile', 'ProfileShow')->name('profile.show');
        Route::post('/Dashboard/Profile/save-pic', 'ProfileSave')->name('profile.savePic');
        Route::post('/Dashboard/Profile/save-info', 'ChangeCredential')->name('profile.saveInfo');
        Route::post('/Dashboard/Profile/change-pass', 'ChangePassword')->name('profile.changePass');
    });

    //Usercontroller for userlist dashboard
    Route::controller(UserController::class)->group(function () {
        Route::get('/Users/List', 'showUsers')->name('users.show');
        Route::post('/User/Drop', 'Drop')->name('user.drop');
        Route::post('/User/Ban', 'Ban')->name('user.ban');
        Route::post('/User/Create', 'Create')->name('user.create');
        Route::post('User/Update', 'Update')->name('user.update');
        Route::post('User/Delete', 'Delete')->name('user.delete');
        Route::post('User/Import/Excell','import')->name('user.import');
    });

    //Questioncontroller for Questions dashboard
    Route::controller(QuestionController::class)->group(function () {
        Route::get('/Questions/List/{novel?}/{level?}/{type?}' ,'ShowQuestions')->name('questions.show');
        Route::post('/Questions/Novels/GetLevels', 'GetLevels')->name('question.getlevel');
        Route::post('/Questions/Add', 'Add')->name('question.add');
        Route::put('/Questions/Update', 'Edit')->name('question.update');
        Route::post('Question/Delete', 'Delete')->name('question.delete');
        Route::post('/Question/Import/CSV_File','import')->name('questions.import');
    });

    //Additionalchoiced controller for additional choices
    Route::controller(AdditionalChoicesController::class)->group(function () {
        Route::get('/Questions/Choices/{id}', 'showChoices')->name('choices.show');
        Route::post('/Questions/Choices/Add', 'Add')->name('choices.add');
        Route::put('/Questions/Choices/Update', 'Edit')->name('choices.update');
        Route::post('Question/Choices/Delete', 'Delete')->name('choices.delete');
    });

    //SectionController for sections
    Route::controller(SectionsController::class)->group(function () {
        Route::get('/Sections/List', 'SectionShow')->name('section.show');
        Route::post('Section/Create', 'Create')->name('section.create');
        Route::post('Section/Delete', 'Delete')->name('section.delete');
        Route::post('Section/Update', 'Update')->name('section.update');
    });

    Route::controller(SectionDataController::class)->group(function(){
        Route::get('/Section/Data/Quiz/{id}','ShowOverallQuizData')->name('section.data.quiz');
        Route::get('/Section/Data/Summative/{id}','ShowOverallSummativeData')->name('section.data.summative');
    });

    Route::controller(SectionUsersController::class)->group(function () {
        Route::get('Section/Users/{id}', 'Show')->name('section.user');
        Route::post('Section/User/Add', 'Add')->name('section.user.add');
        Route::post('Section/User/Remove', 'Remove')->name('section.user.remove');
    });

    //Route for UserScores
    Route::controller(UserScoresController::class)->group(function () {
        Route::get('/user/{id}/scores', 'UserScoresShow')->name('users.scores.show');
        Route::get('/user/{id}/logs', 'UserLogsShow')->name('user.logs.show');
        Route::get('/api/score/details/{user_id}/{level_id}','getDetails')->name('charts.userlogs.get');
    });
});

//for Logging in and logging out
Route::controller(LoginController::class)->group(function () {
    Route::get('/Login', 'showLogin')->name('login.show');
    Route::post('/Login/Submit', 'loginSubmit')->name('login.submit');
    Route::post('/Login/OTP/Verify', 'OtpVerify')->name('login.otp.verify');
    Route::post('/Login/OTP/Resend', 'ResendOtp')->name('login.otp.resend');
    Route::post('/Login/ForgotPass', 'ForgotPass')->name('login.forgotpass');
    Route::post('/Login/ForgotPass/Verify', 'FpassVerify')->name('login.forgotpass.verify');
    Route::post('/Logout', 'logout')->name('logout');
});

// for forgotpassword
Route::controller(ForgotPassController::class)->group(function () {
    Route::get('/ForgotPassword/{code}', "ForgotPassShow")->name('forgotpass.show');
    Route::put('/ForgotPassword/Submit', 'ForgorPassSubmit')->name('forgotpass.submit');
    //Route::get('/users/ForgotPassword', 'UserForgotShow')->name('users.forgot');
    Route::post('/users/forgot/request-code', 'UserForgotPass')->name('users.forgot.code');
    Route::post('/users/forgot/verify', 'UserFpassVerify')->name('user.forgot.verify');
    //Route::get('/users/reset/{code}', 'UserResetPass')->name('users.resetpass');
    Route::post('/users/reset/submit', 'UserResetPassSubmit')->name('users.reset.submit');
});
