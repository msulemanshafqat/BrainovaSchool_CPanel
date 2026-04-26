<?php

use Illuminate\Support\Facades\Route;
use Modules\BehaviourRecord\Providers\Http\Controllers\AppealConversationController;
use Modules\BehaviourRecord\Providers\Http\Controllers\BehaviourRecordController;
use Modules\BehaviourRecord\Providers\Http\Controllers\BehaviourRecordReportController;
use Modules\BehaviourRecord\Providers\Http\Controllers\IncidentController;
use Modules\BehaviourRecord\Providers\Http\Controllers\ParentPanelBehaviourRecordController;
use Modules\BehaviourRecord\Providers\Http\Controllers\StudentBehaviourRecordController;
use Modules\BehaviourRecord\Providers\Http\Controllers\StudentIncidentAssignAppealController;


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

Route::middleware(saasMiddleware())->group(function () {
    Route::group([
        'middleware' => [
            'XssSanitizer',
            'lang',
            'CheckSubscription',
            'FeatureCheck:account',
            'auth.routes',
            'AdminPanel'
        ],
    ], function () {
        Route::resource('behaviourrecord', BehaviourRecordController::class)->names('behaviourrecord');

        Route::prefix('incidents')->name('incidents.')->group(function () {
            Route::get('/', [IncidentController::class, 'index'])->name('index');
            Route::post('/store', [IncidentController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [IncidentController::class, 'edit'])->name('edit');
            Route::post('/update', [IncidentController::class, 'update'])->name('update')->middleware('DemoCheck');;
            Route::delete('/delete/{id}', [IncidentController::class, 'destroy'])->name('destroy')->middleware('DemoCheck');;

            Route::prefix('record')->name('record.')->group(function () {
                Route::get('/', [BehaviourRecordController::class, 'index'])->name('index');
                Route::post('/store', [BehaviourRecordController::class, 'store'])->name('store');
                Route::get('/student/{id}', [BehaviourRecordController::class, 'getStudentRecord'])->name('student');
            });

            Route::prefix('appeal')->name('appeal.')->group(function () {
                Route::get('/', [StudentIncidentAssignAppealController::class, 'index'])->name('index');
                Route::get('/details/{id}', [StudentIncidentAssignAppealController::class, 'details'])->name('details');
                Route::get('/reject/{id}',  [StudentIncidentAssignAppealController::class, 'reject'])->name('reject');
                Route::get('/withdraw/{id}',  [StudentIncidentAssignAppealController::class, 'withdraw'])->name('withdraw');
                Route::post('/change-status/{id}', [StudentIncidentAssignAppealController::class, 'changeStatus'])->name('changeStatus');

                Route::prefix('conversation')->name('conversation.')->group(function () {
                    Route::post('/store', [AppealConversationController::class, 'store'])->name('store');
                });
            });


            Route::prefix('reports')->name('reports.')->group(function () {
                Route::get('/', [BehaviourRecordReportController::class, 'index'])->name('index');
            });
        });

    });
});

Route::middleware(saasMiddleware())->group(function () {
    Route::group(['middleware' => ['XssSanitizer']], function () {
        Route::group(['middleware' => ['lang', 'CheckSubscription']], function () {
            Route::group(['middleware' => 'StudentPanel'], function () {
                Route::group(['middleware' => ['auth.routes']], function () {


                    Route::prefix('student')->name('student.')->group(function () {
                        Route::prefix('behaviourrecord')->name('behaviourrecord.')->group(function () {
                            Route::get('/', [StudentBehaviourRecordController::class, 'index'])->name('index');
                            Route::post('/appeal-store', [StudentBehaviourRecordController::class, 'appealStore'])->name('appeal.store');
                        });

                        Route::prefix('appeal')->name('appeal.')->group(function () {
                            Route::get('/', [StudentBehaviourRecordController::class, 'appeals'])->name('index');
                            Route::get('/details/{id}', [StudentBehaviourRecordController::class, 'details'])->name('details');
                            Route::get('/withdraw/{id}',  [StudentBehaviourRecordController::class, 'withdraw'])->name('withdraw');

                            Route::prefix('conversation')->name('conversation.')->group(function () {
                                Route::post('/store', [AppealConversationController::class, 'store'])->name('store');
                            });

                        });



                    });




                });
            });
        });

    });
});


Route::middleware(saasMiddleware())->group(function () {
    Route::group(['middleware' => ['XssSanitizer']], function () {
        Route::group(['middleware' => ['lang', 'CheckSubscription']], function () {
            Route::group(['middleware' => 'ParentPanel'], function () {
                Route::group(['middleware' => ['auth.routes']], function () {

                    Route::prefix('parent-panel')->name('parent-panel.')->group(function () {
                        Route::prefix('behaviourrecord')->name('behaviourrecord.')->group(function () {
                            Route::get('/', [ParentPanelBehaviourRecordController::class, 'index'])->name('index');
                            Route::post('/appeal-store', [ParentPanelBehaviourRecordController::class, 'appealStore'])->name('appeal.store');
                        });

                        Route::prefix('appeal')->name('appeal.')->group(function () {
                            Route::get('/', [ParentPanelBehaviourRecordController::class, 'appeals'])->name('index');
                            Route::get('/details/{id}', [ParentPanelBehaviourRecordController::class, 'details'])->name('details');
                            Route::get('/withdraw/{id}',  [StudentBehaviourRecordController::class, 'withdraw'])->name('withdraw');

                            Route::prefix('conversation')->name('conversation.')->group(function () {
                                Route::post('/store', [AppealConversationController::class, 'store'])->name('store');
                            });

                        });

                    });

                });
            });
        });
    });
});
