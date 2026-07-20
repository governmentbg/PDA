<?php

use App\Http\Controllers\Auth\ActivationController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Route;

//add non auth related routes here
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');

Route::get('/session-expired', function () {
    return view('session-expired');
})->name('session-expired');


Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

//feed
Route::feeds();

//lang
Route::post('locale', [App\Http\Controllers\LocaleController::class, 'switch'])->name('locale');

//search.
Route::group([
    'prefix' => 'search',
    'as' => 'search.',
], function () {
    Route::get('/', [App\Http\Controllers\SearchController::class, 'index'])->name('index');
    Route::get('/export', [App\Http\Controllers\SearchController::class, 'exportCsv'])->name('export');
});

//article.
Route::group(
    [
        'prefix' => 'article',
        'as' => 'article.',
    ],
    function () {
        Route::get('/', [App\Http\Controllers\ArticleController::class, 'index'])->name('index');
        Route::get('/view/{id}/{slug}', [App\Http\Controllers\ArticleController::class, 'view'])->name('view');
    }
);

//provider.
Route::group(
    [
        'prefix' => 'provider',
        'as' => 'provider.',
    ],
    function () {
        Route::get('/', [App\Http\Controllers\ProviderController::class, 'index'])->name('index');
        Route::get('/view/{id}', [App\Http\Controllers\ProviderController::class, 'view'])->name('view');
    }
);


//cultural_object.
Route::group(
    [
        'prefix' => 'cultural_object',
        'as' => 'cultural_object.',
    ],
    function () {
        Route::get('/', [App\Http\Controllers\CulturalObjectController::class, 'index'])->name('index');
        Route::get('/view/{id}', [App\Http\Controllers\CulturalObjectController::class, 'view'])->name('view');
        Route::get('/export/{id}', [App\Http\Controllers\CulturalObjectController::class, 'exportCsv'])->name('export');
        Route::get('/download/{id}', [App\Http\Controllers\CulturalObjectController::class, 'download'])->name('download');
        Route::get('/{web_id}/tiff-page/{page_number}', [App\Http\Controllers\CulturalObjectController::class, 'getTiffPage'])
            ->name('tiff-page');
        Route::get('/tile-proxy/{web_id}/{page_number}/{iiif_path}', [App\Http\Controllers\CulturalObjectController::class, 'proxyTiffTile'])
            ->name('proxy-tiff-tile')
            ->where('iiif_path', '.*');
        Route::post('/video/sign', [App\Http\Controllers\CulturalObjectController::class, 'signVideo'])->name('video.sign');
    }
);

//page.
Route::group(
    [
        'prefix' => 'page',
        'as' => 'page.',
    ],
    function () {
        Route::get('/{sef_title}', [PageController::class, 'show'])->name('show');
    }
);

//gallery
Route::group(
    [
        'prefix' => 'galleries',
        'as' => 'gallery.',
    ],
    function () {
        Route::get('/', [\App\Http\Controllers\GalleryController::class, 'public'])->name('index');
        Route::get('/show/{gallery}', [\App\Http\Controllers\GalleryController::class, 'view'])->name('view');
    }
);
//gallery.
//add non auth related routes here


//add auth related routes for users here
Route::group([
    'middleware' => ['guest'],
    'prefix' => 'auth',
    'as' => 'auth.',
], function () {


    Route::get('register', [RegisterController::class, 'showRegistrationForm'])
        ->name('register');


    Route::get('activate/{token}', [ActivationController::class, 'activate'])
        ->name('activate');

    Route::get('resend-activation', [ActivationController::class, 'showResendForm'])
        ->name('resend-activation');
    Route::post('resend-activation', [ActivationController::class, 'resend'])
        ->name('resend-activation.post');

    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);

    Route::get('password/forgot', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

});


Route::middleware('auth')->group(function () {
    Route::get('auth/logout', [LoginController::class, 'logout'])->name('auth.logout');

});

//add auth related routes for users here
//profile.
Route::group([
    'middleware' => ['auth'],
    'prefix' => 'profile',
    'as' => 'profile.',
], function () {
    Route::get('/', [ProfileController::class, 'show'])->name('show');
    Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
    Route::post('/update', [ProfileController::class, 'update'])->name('update');

    Route::prefix('favorites')->group(function () {

        Route::get('/', [FavoriteController::class, 'index'])->name('favorites.index');

        Route::post('/remove-multiple', [FavoriteController::class, 'removeMultiple'])->name('favorites.remove-multiple');
        Route::post('/add-multiple', [FavoriteController::class, 'addMultiple'])->name('favorites.add-multiple');
    });

    Route::prefix('my-galleries')->name('galleries.')->group(function () {
        Route::get('/', [GalleryController::class, 'index'])->name('index');
        Route::post('/', [GalleryController::class, 'store'])->name('store');
        Route::get('gallery/{gallery}', [GalleryController::class, 'show'])->name('show');
        Route::put('gallery/{gallery}', [GalleryController::class, 'update'])->name('update');
        Route::delete('{gallery}', [GalleryController::class, 'destroy'])->name('destroy');

        Route::post('add-objects', [GalleryController::class, 'addObjects'])->name('addObjects');
        Route::post('remove-objects', [GalleryController::class, 'removeObjects'])->name('removeObjects');
    });

    Route::prefix('password')->name('password.')->group(function () {
        Route::get('/',  [ProfileController::class, 'editPassword'])->name('edit');
        Route::post('/', [ProfileController::class, 'updatePassword'])->name('update');
    });

//    Route::group([
//        'prefix' => 'cart',
//        'as' => 'cart.',
//    ], function () {
//
//        Route::get('/', [CartController::class, 'index'])->name('index');
//        Route::post('/checkout', [CartController::class, 'checkout'])->name('checkout');
//        Route::post('/{webResource}', [CartController::class, 'add'])->name('add');
//        Route::delete('/{webResource}', [CartController::class, 'remove'])->name('remove');
//        Route::delete('/', [CartController::class, 'clear'])->name('clear');
//
//    });
//
//    Route::group([
//        'prefix' => 'payments',
//        'as' => 'payments.',
//    ], function () {
//        Route::get('/', [PaymentController::class, 'index'])->name('index');
//        Route::post('/{payment}/suspend', [PaymentController::class, 'suspend'])->name('suspend');
//    });
});

//add administration related routes here
Route::group(
    [
        'middleware' => ['auth', 'role:'.\App\Models\Role::ADMINISTRATOR],
        'prefix' => 'manage',
        'as' => 'manage.',
    ],

    function () {
        //settings/
        Route::group(
            [
                'prefix'     => 'settings',
                'as'     => 'settings.',
            ],
            function () {
                Route::get('index', [App\Http\Controllers\Manage\SettingController::class,'index'])->name('index');
                Route::get('create',  [App\Http\Controllers\Manage\SettingController::class,'create'])->name('create');
                Route::post('store', [App\Http\Controllers\Manage\SettingController::class,'store'])->name('store');
                Route::get('edit/{id}', [App\Http\Controllers\Manage\SettingController::class,'edit'])->name('edit');
                Route::patch('update/{id}', [App\Http\Controllers\Manage\SettingController::class,'update'])->name('update');
                Route::delete('destroy{id}', [App\Http\Controllers\Manage\SettingController::class, 'destroy'])->name('destroy');
            }
        );


        //article_type.
        Route::group(
            [
                'prefix'     => 'article_type',
                'as'     => 'article_type.',
            ],
            function () {
                Route::get('index', [App\Http\Controllers\Manage\ArticleTypeController::class,'index'])->name('index');
                Route::get('create',  [App\Http\Controllers\Manage\ArticleTypeController::class,'create'])->name('create');
                Route::post('store', [App\Http\Controllers\Manage\ArticleTypeController::class,'store'])->name('store');
                Route::get('edit/{id}', [App\Http\Controllers\Manage\ArticleTypeController::class,'edit'])->name('edit');
                Route::patch('update/{id}', [App\Http\Controllers\Manage\ArticleTypeController::class,'update'])->name('update');
                Route::delete('destroy{id}', [App\Http\Controllers\Manage\ArticleTypeController::class, 'destroy'])->name('destroy');
            }
        );
        //article_type.

        //article.
        Route::group(
            [
                'prefix'     => 'article',
                'as'     => 'article.',
            ],
            function () {
                Route::get('slugify', [App\Http\Controllers\Manage\ArticleController::class,'slugify'])->name('slugify');
                Route::get('index', [App\Http\Controllers\Manage\ArticleController::class,'index'])->name('index');
                Route::get('create',  [App\Http\Controllers\Manage\ArticleController::class,'create'])->name('create');
                Route::post('store', [App\Http\Controllers\Manage\ArticleController::class,'store'])->name('store');
                Route::get('edit/{id}', [App\Http\Controllers\Manage\ArticleController::class,'edit'])->name('edit');
                Route::patch('update/{id}', [App\Http\Controllers\Manage\ArticleController::class,'update'])->name('update');
                Route::delete('destroy{id}', [App\Http\Controllers\Manage\ArticleController::class, 'destroy'])->name('destroy');
                Route::post('toggle-publish/{id}', [App\Http\Controllers\Manage\ArticleController::class,'togglePublish'])->name('toggle-publish');
                Route::get('deleteImage/{articleId}/{imageId}', [App\Http\Controllers\Manage\ArticleController::class, 'deleteImage'])->name('deleteImage');
            }
        );
        //article.

        //page
        Route::group(
            [
                'prefix' => 'page',
                'as' => 'page.',
            ],
            function () {
                Route::get('index', [App\Http\Controllers\Manage\PageController::class,'index'])->name('index');
                Route::get('create', [App\Http\Controllers\Manage\PageController::class,'create'])->name('create');
                Route::post('store', [App\Http\Controllers\Manage\PageController::class,'store'])->name('store');
                Route::get('edit/{id}', [App\Http\Controllers\Manage\PageController::class,'edit'])->name('edit');
                Route::patch('update/{id}', [App\Http\Controllers\Manage\PageController::class,'update'])->name('update');
                Route::delete('destroy/{id}', [App\Http\Controllers\Manage\PageController::class,'destroy'])->name('destroy');
                Route::post('toggle-publish/{id}', [App\Http\Controllers\Manage\PageController::class,'togglePublish'])->name('toggle-publish');
                Route::get('slugify', [App\Http\Controllers\Manage\PageController::class,'slugify'])->name('slugify');
            }
        );
        //page.

        //gallery
        Route::group(
            [
                'prefix' => 'galleries',
                'as' => 'gallery.',
            ],
            function () {
                Route::get('/', [\App\Http\Controllers\Manage\GalleryController::class, 'index'])->name('index');
                Route::patch('/update/{gallery}', [\App\Http\Controllers\Manage\GalleryController::class, 'update'])->name('update');
                Route::patch('/{gallery}/approve', [\App\Http\Controllers\Manage\GalleryController::class, 'approve'])->name('approve');
                Route::patch('/{gallery}/reject', [\App\Http\Controllers\Manage\GalleryController::class, 'reject'])->name('reject');
            }
        );
        //gallery.

        // payments
        Route::group(
            [
                'prefix' => 'payments',
                'as' => 'payments.',
            ],
            function () {
                Route::get('/', [\App\Http\Controllers\Manage\PaymentController::class, 'index'])->name('index');
                Route::get('/{payment}', [\App\Http\Controllers\Manage\PaymentController::class, 'show'])->name('show');
                Route::get('/{payment}/edit', [\App\Http\Controllers\Manage\PaymentController::class, 'edit'])->name('edit');
                Route::patch('/{payment}', [\App\Http\Controllers\Manage\PaymentController::class, 'update'])->name('update');
            });
        // payments
    }
);
//add administration related routes here

//feedback.
Route::group(
    [
        'prefix' => 'feedback',
        'as' => 'feedback.',
    ],
    function () {
        Route::post('store', [FeedbackController::class, 'store'])->name('store')
            ->middleware('throttle:feedback'); // RateLimiter
});


