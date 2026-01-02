<?php

use App\Http\Controllers\Back\Exams\ExamNameController;
use App\Http\Controllers\Back\LectureController;
use App\Http\Controllers\Back\MonthController;
use App\Http\Controllers\Back\NotificationController;
use App\Http\Controllers\Back\PdfsController;
use App\Http\Controllers\Back\StudentSubscriptionsController;
use App\Http\Controllers\Back\ParentPortalDataController;
use App\Http\Controllers\Front\ExamAnswerController;
use App\Http\Controllers\Front\IndexController;
use App\Http\Controllers\Front\StudentController;
use App\Http\Controllers\Front\ParentPortalController;
use App\Http\Controllers\Front\PaymentController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PublicExamController;
use App\Models\ExamAnswer;
use App\Models\ExamName;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Auth::routes();

// Home Page (للزوار فقط)
Route::get('/', function () {
    // إذا كان الطالب مسجل دخول، توجيهه للداشبورد
    if (Auth::guard('student')->check()) {
        return redirect()->route('student.dashboard');
    }

    // استخدام Cache للـ Featured Lectures
    $limit = function_exists('courses_per_page') ? courses_per_page() : 6;
    $featuredLectures = \App\Services\CacheService::getFeaturedLectures($limit);
    return view('front.index', compact('featuredLectures'));
})->name('home');

// Route to serve upload_files (for compatibility)
Route::get('/upload_files/{filename}', function ($filename) {
    $path = public_path('upload_files/' . $filename);
    if (file_exists($path)) {
        return response()->file($path);
    }
    abort(404);
})->where('filename', '.*');

// Route to serve assignment files from storage
Route::get('/storage/{path}', function ($path) {
    $fullPath = storage_path('app/public/' . $path);
    if (!file_exists($fullPath)) {
        abort(404);
    }
    return response()->file($fullPath);
})->where('path', '.*')->name('storage.file');

// Legal Pages
Route::get('/privacy-policy', function () {
    return view('front.privacy_policy');
})->name('privacy.policy');

Route::get('/terms-of-service', function () {
    return view('front.terms_of_service');
})->name('terms.of.service');

// FAQ Page
Route::get('/faq', [\App\Http\Controllers\Front\FAQController::class, 'index'])->name('faq.index');

// About Teacher Page
Route::get('/about-teacher', function () {
    return view('front.about_teacher');
})->name('about.teacher');

// Parent Portal Routes (Public - No Auth Required)
Route::prefix('parent-portal')->name('parent-portal.')->group(function () {
    Route::get('/', [ParentPortalController::class, 'index'])->name('index');
    Route::post('/search', [ParentPortalController::class, 'search'])->name('search');
    Route::get('/report/{student}', [ParentPortalController::class, 'report'])->name('report');
    Route::get('/export-pdf/{student}', [ParentPortalController::class, 'exportPDF'])->name('export.pdf');
});

// Webhook Routes (External System Integration)
Route::prefix('webhook')->name('webhook.')->group(function () {
    Route::post('/payment', [\App\Http\Controllers\Back\WebhookController::class, 'payment'])->name('payment'); // رابط واحد للدفع (يسجل + يفعّل)
    Route::post('/register-student', [\App\Http\Controllers\Back\WebhookController::class, 'registerStudent'])->name('register.student');
    Route::post('/activate-subscription', [\App\Http\Controllers\Back\WebhookController::class, 'activateSubscription'])->name('activate.subscription');
    Route::post('/handle', [\App\Http\Controllers\Back\WebhookController::class, 'handle'])->name('handle');
});

// Student routes (front-end registration and login)
Route::get('student-login', [StudentController::class, 'login'])->name('studentLogin');
Route::get('student-signup', [StudentController::class, 'signup'])->name('studentSignup');
Route::post('student-login', [StudentController::class, 'goLogin'])->name('goLogin');
Route::post('student-signup', [StudentController::class, 'store'])->name('student.store');
Route::resource('student', StudentController::class)->except(['store']);

Route::get('student-Logout', [StudentController::class, 'studentLogout'])->name('studentLogout');

// Discount Code Validation (Public)
Route::post('/validate-discount-code', [\App\Http\Controllers\Front\DiscountCodeController::class, 'validateCode'])->name('discount.validate');

// Activation Code Instructions (Public - For QR Code)
Route::get('/activate-instructions', [\App\Http\Controllers\Front\ActivationCodeController::class, 'instructions'])->name('student.activate.instructions');

// Payment Callbacks (Public - No Auth Required)
Route::get('/payments/verify/{payment?}', [\App\Http\Controllers\Front\PaymentController::class, 'verifyPayment'])->name('verify-payment');
Route::match(['get','post'], '/payment/callback', [PaymentController::class, 'callback'])->name('payment.callback');
Route::post('/payment/webhook', [PaymentController::class, 'webhook'])->name('payment.webhook');


Route::group(
    ['middleware' => ['auth:student', 'ensure.student.profile']],
    function () {

        // Student Dashboard
        Route::get('dashboard', [IndexController::class, 'dashboard'])->name('student.dashboard');

        Route::get('courses', [IndexController::class, 'index'])->name('courses.index');
        Route::get('bundles', [\App\Http\Controllers\Front\BundleController::class, 'index'])->name('bundles.index');
        Route::get('bundle-details/{id}', [\App\Http\Controllers\Front\BundleController::class, 'show'])->name('bundle.details');
        Route::get('course-details/{id}', [IndexController::class, 'course_details'])->name('course_details');

        // Route::get('month-content',[IndexController::class,'month_content']);
        Route::get('/month-content', [IndexController::class, 'month_content'])->name('month.content');
        Route::get('month-content-lectures', [IndexController::class, 'month_content_lectures'])->name('videos');
        Route::get('month-lecture-details', [IndexController::class, 'lecture'])->name('lecture');
        Route::post('/api/lecture/{lecture_id}/track-progress', [IndexController::class, 'trackProgress'])->name('lecture.track.progress');

        // Quiz Routes (Front)
        Route::get('student-quiz/{quizId}', [\App\Http\Controllers\Front\QuizController::class, 'show'])->name('quiz.show');
        Route::post('student-quiz/{quizId}/start', [\App\Http\Controllers\Front\QuizController::class, 'start'])->name('quiz.start');
        Route::post('student-quiz/{quizId}/submit', [\App\Http\Controllers\Front\QuizController::class, 'submit'])->name('quiz.submit');
        Route::get('student-quiz/{quizId}/result', [\App\Http\Controllers\Front\QuizController::class, 'result'])->name('quiz.result');
        Route::get('quiz/check-lecture-access/{lectureId}', [\App\Http\Controllers\Front\QuizController::class, 'checkLectureAccess'])->name('quiz.checkLectureAccess');

        Route::get('month-content-pdfs', [IndexController::class, 'month_content_pdfs'])->name('pdfs');
        Route::get('pdf/view/{id}', [IndexController::class, 'viewPdf'])->name('pdf.view');
        Route::get('pdf/file/{id}', [IndexController::class, 'servePdf'])->name('pdf.serve');
        Route::get('student-profile', [IndexController::class, 'student_profile'])->name('student_profile');

        Route::get('month-content-exams', [IndexController::class, 'month_content_exams'])->name('exams');
        Route::get('month-content-exam-questions', [IndexController::class, 'exam_questions'])->name('exam_questions');

        Route::post('add_question_answer/{exam_id}', [ExamAnswerController::class, 'add_question_answer'])->name('add_question_answer');
        Route::get('exam_review', [IndexController::class, 'exam_review'])->name('exam_review');

        Route::post('student-update-image', [StudentController::class, 'updateImage'])->name('updateImage');

        Route::post('/update-password', [App\Http\Controllers\Front\StudentController::class, 'updatePassword'])->name('updatePassword');

        // Bundle Activation Routes
        Route::prefix('bundle')->name('bundle.')->group(function () {
            Route::get('/{bundle_id}/activate', [\App\Http\Controllers\Front\BundleController::class, 'showActivateForm'])->name('activate.form');
            Route::post('/{bundle_id}/activate', [\App\Http\Controllers\Front\BundleController::class, 'activateWithCode'])->name('activate');
        });

        // Payment Routes
        Route::prefix('payment')->name('payment.')->group(function () {
            // Specific routes must come before parameterized routes
            Route::get('/history', [\App\Http\Controllers\Front\PaymentController::class, 'history'])->name('history');
            Route::get('/success/{payment_id}', [\App\Http\Controllers\Front\PaymentController::class, 'success'])->name('success');
            Route::get('/fail/{payment_id}', [\App\Http\Controllers\Front\PaymentController::class, 'fail'])->name('fail');
            Route::get('/bundle/{bundle_id}', [\App\Http\Controllers\Front\BundleController::class, 'showPayment'])->name('bundle.show');
            Route::post('/bundle/{bundle_id}', [\App\Http\Controllers\Front\BundleController::class, 'purchaseBundle'])->name('bundle.purchase');
            Route::get('/{month_id}', [\App\Http\Controllers\Front\PaymentController::class, 'showPayment'])->name('show');
            Route::post('/initiate/{month_id}', [\App\Http\Controllers\Front\PaymentController::class, 'initiatePayment'])->name('initiate');
        });

        // Student Assignments Routes
        Route::prefix('assignments')->name('student.assignments.')->group(function () {
            Route::get('/month/{month_id}', [\App\Http\Controllers\Front\StudentAssignmentController::class, 'index'])->name('index');
            Route::get('/{assignment}', [\App\Http\Controllers\Front\StudentAssignmentController::class, 'show'])->name('show');
            Route::post('/{assignment}/submit', [\App\Http\Controllers\Front\StudentAssignmentController::class, 'submit'])->name('submit');
            Route::delete('/submission/{submission}', [\App\Http\Controllers\Front\StudentAssignmentController::class, 'deleteSubmission'])->name('submission.delete');
        });

        // Notifications Routes
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Front\StudentNotificationController::class, 'index'])->name('index');
            Route::get('/unread-count', [\App\Http\Controllers\Front\StudentNotificationController::class, 'unreadCount'])->name('unread.count');
            Route::get('/recent', [\App\Http\Controllers\Front\StudentNotificationController::class, 'recent'])->name('recent');
            Route::post('/mark-read/{id}', [\App\Http\Controllers\Front\StudentNotificationController::class, 'markAsRead'])->name('mark.read');
            Route::post('/mark-all-read', [\App\Http\Controllers\Front\StudentNotificationController::class, 'markAllAsRead'])->name('mark.all.read');
        });

        // Activation Codes Routes
        Route::get('/activate-code', [\App\Http\Controllers\Front\ActivationCodeController::class, 'index'])->name('activation_code.index');
        Route::post('/activate-code', [\App\Http\Controllers\Front\ActivationCodeController::class, 'activate'])->name('activation_code.activate');
        Route::post('/activation/activate', [\App\Http\Controllers\Front\ActivationCodeController::class, 'activate'])->name('activation.activate');
        Route::post('/validate-activation-code', [\App\Http\Controllers\Front\ActivationCodeController::class, 'validateCode'])->name('activation.validate');

    }
);

//////////////////////////////////////////////////////////////////////////////////////////////
Route::get('reset-password', [IndexController::class, 'showresetPassword'])->name('showresetPassword');
Route::post('reset-password', [IndexController::class, 'resetPassword'])->name('resetPassword');
Route::get('reset/{token}', [IndexController::class, 'changePassword']);
Route::post('reset/{token}', [IndexController::class, 'postchangePassword']);
////////////////////////////////////////////////////////////////////////////////////////////////////////
Route::get('/search-excel', function () {
    return view('front.search_excel');
});
Route::post('/find-student-excel', [IndexController::class, 'searchInExcel'])->name('students.find.excel');




/////////////////////////////////////////////////////////////////////
Route::get('/admin', [HomeController::class, 'index'])->name('home');
Route::get('/admin-logout', [HomeController::class, 'logoutAdmin'])->name('logoutAdmin');

Route::group(
    ['middleware' => ['auth:web']],
    function () {

        // Smart Search
        Route::get('/admin/search', [\App\Http\Controllers\Back\SearchController::class, 'search'])->name('admin.search');

        // Analytics & Reports
        Route::prefix('admin/analytics')->name('admin.analytics.')->group(function () {
            Route::get('/dashboard', [\App\Http\Controllers\Back\AnalyticsController::class, 'dashboard'])->name('dashboard');
            Route::get('/top-students', [\App\Http\Controllers\Back\AnalyticsController::class, 'topStudents'])->name('top.students');
            Route::get('/student-performance', [\App\Http\Controllers\Back\AnalyticsController::class, 'studentPerformance'])->name('student.performance');
            Route::get('/revenue', [\App\Http\Controllers\Back\AnalyticsController::class, 'revenue'])->name('revenue');
            Route::get('/content-usage', [\App\Http\Controllers\Back\AnalyticsController::class, 'contentUsage'])->name('content.usage');
        });

        // Media Library
        Route::prefix('admin/media')->name('admin.media.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Back\MediaController::class, 'index'])->name('index');
            Route::post('/upload', [\App\Http\Controllers\Back\MediaController::class, 'upload'])->name('upload');
            Route::delete('/{file}', [\App\Http\Controllers\Back\MediaController::class, 'delete'])->name('delete');
        });

        // Bulk Operations
        Route::post('/admin/bulk-operations', [\App\Http\Controllers\Back\BulkOperationsController::class, 'bulkUpdate'])->name('admin.bulk.update');

        // Cache Management
        Route::post('/admin/cache/clear', [\App\Http\Controllers\Back\CacheController::class, 'clearAll'])->name('admin.cache.clear');
        Route::post('/admin/cache/clear-specific', [\App\Http\Controllers\Back\CacheController::class, 'clearSpecific'])->name('admin.cache.clearSpecific');

        // Discount Codes
        Route::resource('admin/discount-codes', \App\Http\Controllers\Back\DiscountCodeController::class)->names([
            'index' => 'admin.discount_codes.index',
            'create' => 'admin.discount_codes.create',
            'store' => 'admin.discount_codes.store',
            'edit' => 'admin.discount_codes.edit',
            'update' => 'admin.discount_codes.update',
            'destroy' => 'admin.discount_codes.destroy',
        ]);

        // Activation Codes
        // Export route must come before resource route to avoid conflicts
        Route::get('admin/activation-codes/export', [\App\Http\Controllers\Back\ActivationCodeController::class, 'export'])->name('admin.activation_codes.export');
        Route::get('admin/activation-codes/export-pdf', [\App\Http\Controllers\Back\ActivationCodeController::class, 'exportPdf'])->name('admin.activation_codes.export_pdf');
        Route::resource('admin/activation-codes', \App\Http\Controllers\Back\ActivationCodeController::class)->names([
            'index' => 'admin.activation_codes.index',
            'create' => 'admin.activation_codes.create',
            'store' => 'admin.activation_codes.store',
            'show' => 'admin.activation_codes.show',
            'edit' => 'admin.activation_codes.edit',
            'update' => 'admin.activation_codes.update',
            'destroy' => 'admin.activation_codes.destroy',
        ]);

        // Lecture Restrictions (قيود المحاضرات)
        Route::prefix('admin/lecture-restrictions')->name('admin.lecture_restrictions.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Back\LectureRestrictionController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Back\LectureRestrictionController::class, 'create'])->name('create');
            Route::post('/store', [\App\Http\Controllers\Back\LectureRestrictionController::class, 'store'])->name('store');
            Route::delete('/{id}', [\App\Http\Controllers\Back\LectureRestrictionController::class, 'destroy'])->name('destroy');
            Route::delete('/all/delete', [\App\Http\Controllers\Back\LectureRestrictionController::class, 'destroyAll'])->name('destroyAll');
            Route::post('/student/delete', [\App\Http\Controllers\Back\LectureRestrictionController::class, 'destroyByStudentPost'])->name('destroyByStudent');
            Route::post('/lecture/delete', [\App\Http\Controllers\Back\LectureRestrictionController::class, 'destroyByLecturePost'])->name('destroyByLecture');
            Route::get('/student/{studentId}', [\App\Http\Controllers\Back\LectureRestrictionController::class, 'studentRestrictions'])->name('studentRestrictions');
            Route::get('/api/lectures/{monthId}', [\App\Http\Controllers\Back\LectureRestrictionController::class, 'getLecturesByMonth'])->name('api.lectures');
        });

        // Blocked Students
        Route::prefix('admin/blocked-students')->name('admin.blocked_students.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Back\BlockedStudentsController::class, 'index'])->name('index');
            Route::get('/unblock/{id}', [\App\Http\Controllers\Back\BlockedStudentsController::class, 'unblock'])->name('unblock');
            Route::post('/unblock-multiple', [\App\Http\Controllers\Back\BlockedStudentsController::class, 'unblockMultiple'])->name('unblock_multiple');
        });

        // Parent Portal Data Management
        Route::prefix('admin/parent-portal')->name('parent-portal.')->group(function () {
            Route::get('/', [ParentPortalDataController::class, 'index'])->name('index');

            // Import routes
            Route::post('/import-attendance', [ParentPortalDataController::class, 'importAttendance'])->name('import-attendance');
            Route::post('/import-payments', [ParentPortalDataController::class, 'importPayments'])->name('import-payments');
            Route::post('/import-tasks', [ParentPortalDataController::class, 'importTasks'])->name('import-tasks');

            // Export template routes
            Route::get('/export-attendance-template', [ParentPortalDataController::class, 'exportAttendanceTemplate'])->name('export-attendance-template');
            Route::get('/export-payment-template', [ParentPortalDataController::class, 'exportPaymentTemplate'])->name('export-payment-template');
            Route::get('/export-task-template', [ParentPortalDataController::class, 'exportTaskTemplate'])->name('export-task-template');

            // Export failed rows routes
            Route::get('/export-failed-attendance', [ParentPortalDataController::class, 'exportFailedAttendance'])->name('export-failed-attendance');
            Route::get('/export-failed-payments', [ParentPortalDataController::class, 'exportFailedPayments'])->name('export-failed-payments');
            Route::get('/export-failed-tasks', [ParentPortalDataController::class, 'exportFailedTasks'])->name('export-failed-tasks');

            // View data routes
            Route::get('/attendance', [ParentPortalDataController::class, 'viewAttendance'])->name('view-attendance');
            Route::get('/payments', [ParentPortalDataController::class, 'viewPayments'])->name('view-payments');
            Route::get('/tasks', [ParentPortalDataController::class, 'viewTasks'])->name('view-tasks');
            Route::get('/select-courses', [ParentPortalDataController::class, 'selectCourses'])->name('select-courses');
            Route::post('/save-course-selections', [ParentPortalDataController::class, 'saveCourseSelections'])->name('save-course-selections');

        });

        // SEO Management
        Route::prefix('admin/seo')->name('admin.seo.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Back\SeoController::class, 'index'])->name('index');
            Route::post('/update', [\App\Http\Controllers\Back\SeoController::class, 'update'])->name('update');
            Route::post('/generate-sitemap', [\App\Http\Controllers\Back\SeoController::class, 'generateSitemap'])->name('generate.sitemap');
        });

        // Payments Management
        Route::prefix('admin/payments')->name('admin.payments.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Back\PaymentController::class, 'index'])->name('index');
            Route::get('/statistics', [\App\Http\Controllers\Back\PaymentController::class, 'statistics'])->name('statistics');
            Route::get('/export', [\App\Http\Controllers\Back\PaymentController::class, 'export'])->name('export');
            Route::get('/{id}', [\App\Http\Controllers\Back\PaymentController::class, 'show'])->name('show');
            Route::post('/{id}/refund', [\App\Http\Controllers\Back\PaymentController::class, 'refund'])->name('refund');
        });

        // Kashier Configuration
        Route::prefix('admin/kashier')->name('admin.kashier.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Back\KashierConfigController::class, 'index'])->name('index');
            Route::post('/test-connection', [\App\Http\Controllers\Back\KashierConfigController::class, 'testConnection'])->name('test.connection');
        });

        // Content Management
        Route::prefix('admin/content')->name('admin.content.')->group(function () {
            Route::get('/schedule', [\App\Http\Controllers\Back\ContentController::class, 'scheduleIndex'])->name('schedule.index');
            Route::post('/schedule/{id}', [\App\Http\Controllers\Back\ContentController::class, 'scheduleStore'])->name('schedule.store');
            Route::get('/versions/{id}', [\App\Http\Controllers\Back\ContentController::class, 'versions'])->name('versions');
            Route::post('/update-order', [\App\Http\Controllers\Back\ContentController::class, 'updateOrder'])->name('updateOrder');
        });

        Route::resource('month', MonthController::class);
        Route::get('month/delete/{id}', [MonthController::class, 'delete']);
        Route::get('delete-all-monthes', [MonthController::class, 'deleteAllMonthes'])->name('deleteAllMonthes');

        // get monthes by ajax
        Route::get('monthes/{grade}', [LectureController::class, 'get_monthes']);
        // get lectures by ajax
        Route::get('lectures/{month_id}', [LectureController::class, 'get_lectures']);

        Route::resource('lecture', LectureController::class);
        Route::get('lecture/delete/{id}', [LectureController::class, 'delete']);
        Route::get('delete-all-lectures', [LectureController::class, 'deleteAllLectures'])->name('deleteAllLectures');

        // Quiz Routes (Admin)
        Route::get('admin/quiz/{lectureId}', [\App\Http\Controllers\Back\QuizController::class, 'show'])->name('admin.quiz.show');
        Route::post('admin/quiz/{lectureId}', [\App\Http\Controllers\Back\QuizController::class, 'store'])->name('admin.quiz.store');
        Route::delete('admin/quiz/{lectureId}', [\App\Http\Controllers\Back\QuizController::class, 'destroy'])->name('admin.quiz.destroy');
        Route::get('admin/quiz/{lectureId}/results', [\App\Http\Controllers\Back\QuizController::class, 'results'])->name('admin.quiz.results');
        Route::get('admin/quiz/attempt/{attemptId}/details', [\App\Http\Controllers\Back\QuizController::class, 'attemptDetails'])->name('admin.quiz.attempt.details');

        Route::resource('student_subscription', StudentSubscriptionsController::class);
        Route::get('subscription/delete/{id}', [StudentSubscriptionsController::class, 'delete']);
        Route::get('delete-All-Subscriptions', [StudentSubscriptionsController::class, 'deleteAllSubscriptions'])->name('deleteAllSubscriptions');
        Route::get('active-all', [StudentSubscriptionsController::class, 'active_all'])->name('student_subscriptionActiveAll');

        // get student by ajax
        Route::get('get_student/{grade}', [StudentSubscriptionsController::class, 'get_students']);
        Route::get('subscription-months/{grade}', [StudentSubscriptionsController::class, 'get_months'])->name('subscription.months');
        Route::post('subscription/activate-multiple', [StudentSubscriptionsController::class, 'activateMultiple'])->name('subscription.activateMultiple');
        Route::post('subscription/load-students', [StudentSubscriptionsController::class, 'loadStudents'])->name('subscription.loadStudents');
        Route::post('subscription/search', [StudentSubscriptionsController::class, 'search'])->name('subscription.search');

        // Excel Import Routes
        Route::prefix('admin/excel')->name('admin.excel.')->group(function () {
            Route::get('/import/students', [\App\Http\Controllers\Back\ExcelImportController::class, 'showImportStudents'])->name('import.students');
            Route::post('/import/students', [\App\Http\Controllers\Back\ExcelImportController::class, 'importStudents'])->name('import.students');
            Route::get('/import/subscriptions', [\App\Http\Controllers\Back\ExcelImportController::class, 'showImportSubscriptions'])->name('import.subscriptions');
            Route::post('/import/subscriptions', [\App\Http\Controllers\Back\ExcelImportController::class, 'importSubscriptions'])->name('import.subscriptions');
            Route::post('/import/subscriptions/manual', [\App\Http\Controllers\Back\ExcelImportController::class, 'importSubscriptionsManual'])->name('import.subscriptions.manual');
            Route::get('/activated-students', [\App\Http\Controllers\Back\ExcelImportController::class, 'showActivatedStudents'])->name('activated.students');
            Route::get('/deactivate/subscriptions', [\App\Http\Controllers\Back\ExcelImportController::class, 'showDeactivateSubscriptions'])->name('deactivate.subscriptions');
            Route::post('/deactivate/subscriptions', [\App\Http\Controllers\Back\ExcelImportController::class, 'deactivateSubscriptions'])->name('deactivate.subscriptions');
            Route::post('/deactivate/subscriptions/manual', [\App\Http\Controllers\Back\ExcelImportController::class, 'deactivateSubscriptionsManual'])->name('deactivate.subscriptions.manual');
            Route::post('/reactivate/student', [\App\Http\Controllers\Back\ExcelImportController::class, 'reactivateStudent'])->name('reactivate.student');
        });

        Route::resource('pdf', PdfsController::class);
        Route::get('pdf/delete/{id}', [PdfsController::class, 'delete']);
        Route::get('delete-All-Pdfs', [PdfsController::class, 'deleteAllPdfs'])->name('deleteAllPdfs');

        // exams
        Route::resource('exam_name', ExamNameController::class);
        Route::get('exam/delete/{id}', [ExamNameController::class, 'delete']);
        Route::get('delete-All-Exams', [ExamNameController::class, 'deleteAllExams'])->name('deleteAllExams');

        Route::get('add-question/{id}', [ExamNameController::class, 'add_question'])->name('exam_name.add_question');
        Route::get('add-question/{id}/create', [ExamNameController::class, 'create_question'])->name('exam_question.create');
        Route::post('add-question/{id}', [ExamNameController::class, 'insert_question'])->name('add_question.insert');
        Route::get('add-question/{exam_id}/edit/{question_id}', [ExamNameController::class, 'edit_question'])->name('exam_question.edit');
        Route::post('update-question/{id}', [ExamNameController::class, 'update_question'])->name('exam_question.update_Q');
        Route::get('question/delete/{id}', [ExamNameController::class, 'delete_question']);
        Route::get('question/delete-all', [ExamNameController::class, 'delete_all_question'])->name('deleteAllQuestions');
        Route::get('exam/{id}/questions/pdf', [ExamNameController::class, 'exportQuestionsPDF'])->name('exam.questions.pdf');
        Route::get('exam/{id}/questions/pdf-without-answers', [ExamNameController::class, 'exportQuestionsPDFWithoutAnswers'])->name('exam.questions.pdf.without.answers');
        Route::get('degree/show-all', [ExamNameController::class, 'showAllDegress'])->name('showAllDegress');

        // Add Student (Admin)
        Route::get('admin/add-student', [HomeController::class, 'add_student'])->name('admin.student.create');
        Route::post('admin/add-student', [HomeController::class, 'store_student'])->name('admin.student.store');

        // General Settings (Super Admin Only)
        Route::prefix('admin/general-settings')->name('admin.general_settings.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Back\GeneralSettingsController::class, 'index'])->name('index');
            Route::put('/', [\App\Http\Controllers\Back\GeneralSettingsController::class, 'update'])->name('update');
            Route::post('/reset', [\App\Http\Controllers\Back\GeneralSettingsController::class, 'reset'])->name('reset');
        });
        // Assignments
        Route::get('admin/assignments/{id}/submissions', [\App\Http\Controllers\Back\AssignmentController::class, 'submissions'])->name('assignments.submissions');
        Route::post('admin/assignments/{id}/submissions/{submission}/grade', [\App\Http\Controllers\Back\AssignmentController::class, 'grade'])->name('assignments.submissions.grade');
        Route::get('admin/assignments/submissions/{submission}/download', [\App\Http\Controllers\Back\AssignmentController::class, 'downloadSubmission'])->name('assignments.submissions.download');
        Route::get('admin/assignments/submissions/{submission}/preview', [\App\Http\Controllers\Back\AssignmentController::class, 'previewSubmission'])->name('assignments.submissions.preview');
        Route::resource('admin/assignments', \App\Http\Controllers\Back\AssignmentController::class)->names([
            'index' => 'assignments.index',
            'create' => 'assignments.create',
            'store' => 'assignments.store',
            'show' => 'assignments.show',
            'edit' => 'assignments.edit',
            'update' => 'assignments.update',
            'destroy' => 'assignments.destroy',
        ]);
        // Users & Roles Management
        Route::prefix('admin')->group(function () {
            Route::resource('users', \App\Http\Controllers\Back\Admin\UserController::class)->names([
                'index' => 'admin.users.index',
                'create' => 'admin.users.create',
                'store' => 'admin.users.store',
                'show' => 'admin.users.show',
                'edit' => 'admin.users.edit',
                'update' => 'admin.users.update',
                'destroy' => 'admin.users.destroy',
            ]);

            Route::resource('roles', \App\Http\Controllers\Back\Admin\RoleController::class)->names([
                'index' => 'admin.roles.index',
                'create' => 'admin.roles.create',
                'store' => 'admin.roles.store',
                'show' => 'admin.roles.show',
                'edit' => 'admin.roles.edit',
                'update' => 'admin.roles.update',
                'destroy' => 'admin.roles.destroy',
            ]);

            Route::get('roles/{role}/permissions', [\App\Http\Controllers\Back\Admin\PermissionController::class, 'edit'])->name('admin.roles.permissions');
            Route::put('roles/{role}/permissions', [\App\Http\Controllers\Back\Admin\PermissionController::class, 'update'])->name('admin.roles.permissions.update');
        });

        // صفحة موحدة لجميع الطلاب
        Route::get('students-all', [HomeController::class, 'students_all'])->name('students.all');

        // Routes للتوافق مع الصفحات القديمة (يمكن إزالتها لاحقاً)
    
        Route::get('students-male', [HomeController::class, 'students_male']);
        Route::get('students-male/edit/{id}', [HomeController::class, 'edit_student'])->name('student.edit');
        Route::get('students-male-delete-all', [HomeController::class, 'deleteAllMaleStudents'])->name('deleteAllMaleStudents');

        Route::get('student-profile/{id}', [HomeController::class, 'student_profile']);
        Route::post('student-profile/{id}/add-course', [HomeController::class, 'addStudentToCourse'])->name('admin.student.addCourse');
        Route::post('student-profile/{id}/remove-course/{subscription_id}', [HomeController::class, 'removeStudentFromCourse'])->name('admin.student.removeCourse');
        Route::get('Student/delete/{id}', [HomeController::class, 'delete_student']);
        Route::post('Student/update/{id}', [HomeController::class, 'Student_update'])->name('Student_update');
        Route::get('Student/reset-device/{id}', [HomeController::class, 'resetDevice']);

        Route::get('admin/logoutAllStudents', [HomeController::class, 'logOutAllStudents']);
        Route::get('students-female', [HomeController::class, 'students_female']);
        Route::get('students-female/edit/{id}', [HomeController::class, 'edit_student'])->name('student.female.edit');
        Route::get('students-female-delete-all', [HomeController::class, 'deleteAllFemaleStudents'])->name('deleteAllFemaleStudents');
        Route::post('show-all-degrees', [HomeController::class, 'showAllDegrees'])->name('showAllDegrees');
        // تفعيل اشتراك شامل لمجموعة من الطلبة
        Route::get('students/all-access', [HomeController::class, 'showAllAccessForm'])->name('admin.students.all_access.form');
        Route::post('students/all-access/excel', [HomeController::class, 'setAllAccessFromExcel'])->name('admin.students.all_access.excel');
        Route::post('students/all-access/manual', [HomeController::class, 'setAllAccessFromIds'])->name('admin.students.all_access.manual');
        Route::post('students/remove-all-access/{id}', [HomeController::class, 'removeAllAccess'])->name('admin.students.remove_all_access');
        Route::get('admin/students/search', [HomeController::class, 'searchStudents'])->name('admin.students.search');

        Route::get('show-taken-exams', [HomeController::class, 'show_taken_exams']);
        Route::get('export-exam-degrees', [HomeController::class, 'exportExamDegreesToExcel'])->name('exportExamDegrees');
        Route::get('deleteAllTakenExams', [HomeController::class, 'deleteAllTakenExams'])->name('deleteAllTakenExams');
        Route::get('delete-taken-exam/{id}', [HomeController::class, 'deleteTakenExam']);
        Route::delete('delete-exam-results', [HomeController::class, 'deleteExamResults'])->name('deleteExamResults');
        Route::post('exam-degree-update/{id}', [HomeController::class, 'exam_degree_update'])->name('exam_degree.update');
        Route::get('lecure-views', [HomeController::class, 'lecure_views']);

        Route::get('admin-profile', [HomeController::class, 'admin_profile_show']);
        Route::post('admin-profile-update/{id}', [HomeController::class, 'admin_profile_update'])->name('admin_profile_update');

        // Views Statistics Routes
        Route::prefix('admin/views')->name('admin.views.')->group(function () {
            Route::get('/lectures', [\App\Http\Controllers\Back\ViewsController::class, 'lectures'])->name('lectures');
            Route::get('/lectures/{id}/students', [\App\Http\Controllers\Back\ViewsController::class, 'lectureViews'])->name('lecture.students');
            Route::get('/pdfs', [\App\Http\Controllers\Back\ViewsController::class, 'pdfs'])->name('pdfs');
            Route::get('/pdfs/{id}/students', [\App\Http\Controllers\Back\ViewsController::class, 'pdfViews'])->name('pdf.students');
        });

        // Notifications
        Route::get('admin/notifications', [NotificationController::class, 'index'])->name('admin.notifications.index');
        Route::post('admin/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('admin.notifications.read');
        Route::post('admin/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('admin.notifications.markAllRead');
        Route::delete('admin/notifications/{id}', [NotificationController::class, 'destroy'])->name('admin.notifications.destroy');
        Route::post('admin/notifications/delete-all', [NotificationController::class, 'deleteAll'])->name('admin.notifications.deleteAll');
        Route::get('admin/notifications/unread-count', [NotificationController::class, 'getUnreadCount'])->name('admin.notifications.unreadCount');
        Route::get('admin/notifications/latest', [NotificationController::class, 'getLatest'])->name('admin.notifications.latest');

        // تنويهات الأدمن
        Route::get('admin/alerts', [App\Http\Controllers\Admin\AlertController::class, 'index'])->name('admin.alerts');
        Route::post('admin/alerts/store', [App\Http\Controllers\Admin\AlertController::class, 'store'])->name('admin.alerts.store');
    }
);

// Public Exams Routes (لا تحتاج تسجيل دخول)
Route::get('/public-exams', [PublicExamController::class, 'index'])->name('publicExam.index');

Route::get('/public-exam/{exam_id}/take', [PublicExamController::class, 'take'])->name('publicExam.take');
Route::post('/public-exam/{exam_id}/start', [PublicExamController::class, 'start'])->name('publicExam.start');
Route::post('/public-exam/{exam_id}/submit', [PublicExamController::class, 'submit'])->name('publicExam.submit');

Route::get('/public-exam/thanks', function () {
    return view('front.public_exam_thanks');
})->name('exam_thanks');

// البحث عن نتيجة الامتحان العام
Route::get('/public-exam/search-result', [PublicExamController::class, 'searchResult'])->name('publicExam.searchResult');
Route::post('/public-exam/find-result', [PublicExamController::class, 'findResult'])->name('publicExam.findResult');
Route::get('/public-exam/result/{id}', [PublicExamController::class, 'showResult'])->name('publicExam.showResult');

// Public Exam Results (للأدمن فقط)
Route::group(
    ['middleware' => ['auth:web']],
    function () {
        Route::get('/admin/public-exam-results', [PublicExamController::class, 'results'])->name('publicExam.results');
        Route::get('/admin/public-exam-results/{exam_id}', [PublicExamController::class, 'results'])->name('publicExam.results.exam');
    }
);
