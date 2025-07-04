<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ResumeController;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';


Route::middleware(['web', 'auth'])->group(function () {
    
    // Page Routes
    Route::get('/ai-checker', fn () => inertia('NavLinks/AiChecker'))
        ->name('ai-checker');
    
    // Data Routes
    Route::prefix('ajax')->name('ajax.')->group(function () {
        Route::post('/resume/upload', [ResumeController::class, 'upload'])
            ->name('resume.upload');
            
        Route::get('/resume/{resume}/status', [ResumeController::class, 'checkAnalysisStatus'])
            ->name('resume.status');
    });
});



Route::get('/create-resume', function () {
    return inertia('NavLinks/CreateResume');
})->name('create-resume');

Route::get('/ai-analytics', function () {
    return inertia('NavLinks/AiAnalytics');
})->name('ai-analytics');


 Route::post('/upload', [ResumeController::class, 'upload'])
        ->name('resume.process');


        // routes/web.php (temporary test route)
Route::get('/verify-hf-token', function() {
    $client = new \GuzzleHttp\Client();
    try {
        $response = $client->post('https://api-inference.huggingface.co/models/bert-base-uncased', [
            'headers' => [
                'Authorization' => 'Bearer ' . env('HUGGING_FACE_API_KEY'),
                'Content-Type' => 'application/json',
            ],
            'json' => ['inputs' => 'test']
        ]);
        return 'Token works! Status: ' . $response->getStatusCode();
    } catch (\Exception $e) {
        return 'Token error: ' . $e->getMessage();
    }
});