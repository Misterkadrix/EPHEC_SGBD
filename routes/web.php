<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UniversityController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\EquipmentTypeController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\CourseSessionController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\DashboardController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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

Route::middleware(['auth','verified'])->group(function(){
    // Routes pour les produits
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    
    // Routes pour le tableau de bord
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/stats', [DashboardController::class, 'stats'])->name('stats');
    
    // Routes pour les universités
    Route::resource('universities', UniversityController::class);
    
    // Routes pour les sites
    Route::resource('sites', SiteController::class);
    
    // Routes pour les salles
    Route::resource('rooms', RoomController::class);
    
    // Routes pour les types d'équipements
    Route::resource('equipment-types', EquipmentTypeController::class);
    
    // Routes pour les équipements
    Route::resource('equipment', EquipmentController::class);
    
    // Routes pour les cours
    Route::resource('courses', CourseController::class);
    
    // Routes pour les années académiques
    Route::resource('academic-years', AcademicYearController::class);
    
    // Routes pour les groupes
    Route::resource('groups', GroupController::class);
    
    // Routes pour les sessions de cours
    Route::resource('course-sessions', CourseSessionController::class);
    
    // Routes pour les fériés
    Route::resource('holidays', HolidayController::class);
});

require __DIR__.'/auth.php';
require __DIR__.'/settings.php';
