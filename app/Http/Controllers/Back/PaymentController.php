<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Month;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:web');
    }

    /**
     * Display all payments
     */
    public function index(Request $request)
    {
        $query = Payment::with(['student', 'month', 'discountCode']);

        // Filters
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('month_id') && $request->month_id) {
            $query->where('month_id', $request->month_id);
        }

        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('kashier_order_id', 'like', "%{$search}%")
                  ->orWhere('payment_id', 'like', "%{$search}%")
                  ->orWhereHas('student', function($sq) use ($search) {
                      $sq->where('id', 'like', "%{$search}%")
                        ->orWhere('student_phone', 'like', "%{$search}%")
                        ->orWhereRaw("CONCAT(first_name, ' ', second_name, ' ', third_name, ' ', forth_name) LIKE ?", ["%{$search}%"]);
                  });
            });
        }

        $payments = $query->orderBy('created_at', 'desc')->paginate(20);

        // Statistics
        $stats = [
            'total' => Payment::count(),
            'paid' => Payment::where('status', 'paid')->count(),
            'pending' => Payment::where('status', 'pending')->count(),
            'failed' => Payment::where('status', 'failed')->count(),
            'total_amount' => Payment::where('status', 'paid')->sum('amount'),
            'total_original' => Payment::where('status', 'paid')->sum('original_amount'),
            'total_discounts' => Payment::where('status', 'paid')->sum('discount_amount'),
            'today_amount' => Payment::where('status', 'paid')->whereDate('paid_at', today())->sum('amount'),
            'month_amount' => Payment::where('status', 'paid')->whereMonth('paid_at', now()->month)->whereYear('paid_at', now()->year)->sum('amount'),
        ];

        $months = Month::all();

        return view('back.payments.index', compact('payments', 'stats', 'months'));
    }

    /**
     * Show payment details
     */
    public function show($id)
    {
        $payment = Payment::with(['student', 'month', 'discountCode'])->findOrFail($id);
        
        return view('back.payments.show', compact('payment'));
    }

    /**
     * Export payments to Excel/CSV
     */
    public function export(Request $request)
    {
        $query = Payment::with(['student', 'month', 'discountCode']);

        // Apply same filters as index
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('month_id') && $request->month_id) {
            $query->where('month_id', $request->month_id);
        }

        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $payments = $query->orderBy('created_at', 'desc')->get();

        $filename = 'payments_' . date('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($payments) {
            $file = fopen('php://output', 'w');
            
            // BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Headers
            fputcsv($file, [
                'رقم الطلب',
                'اسم الطالب',
                'رقم الهاتف',
                'الكورس',
                'المبلغ الأصلي',
                'الخصم',
                'المبلغ النهائي',
                'كود الخصم',
                'الحالة',
                'تاريخ الدفع',
                'طريقة الدفع',
                'رقم المعاملة'
            ]);

            // Data
            foreach ($payments as $payment) {
                fputcsv($file, [
                    $payment->kashier_order_id,
                    $payment->student ? $payment->student->first_name . ' ' . $payment->student->second_name . ' ' . $payment->student->third_name . ' ' . $payment->student->forth_name : '-',
                    $payment->student ? $payment->student->student_phone : '-',
                    $payment->month ? $payment->month->name : '-',
                    number_format($payment->original_amount ?? $payment->amount, 2),
                    number_format($payment->discount_amount ?? 0, 2),
                    number_format($payment->amount, 2),
                    $payment->discountCode ? $payment->discountCode->code : '-',
                    $this->getStatusLabel($payment->status),
                    $payment->paid_at ? $payment->paid_at->format('Y-m-d H:i') : '-',
                    $payment->payment_method ?? '-',
                    $payment->payment_id ?? '-',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get status label in Arabic
     */
    private function getStatusLabel($status)
    {
        $labels = [
            'paid' => 'مدفوع',
            'pending' => 'قيد الانتظار',
            'failed' => 'فشل',
        ];

        return $labels[$status] ?? $status;
    }

    /**
     * Refund payment (Manual)
     */
    public function refund(Request $request, $id)
    {
        $payment = Payment::findOrFail($id);

        if ($payment->status !== 'paid') {
            return redirect()->back()->with('error', 'لا يمكن استرجاع دفعة غير مدفوعة');
        }

        $request->validate([
            'refund_reason' => 'required|string|max:500',
        ]);

        // Update payment status
        $payment->update([
            'status' => 'refunded',
            'kashier_response' => array_merge($payment->kashier_response ?? [], [
                'refunded_at' => now(),
                'refund_reason' => $request->refund_reason,
                'refunded_by' => auth()->id(),
            ]),
        ]);

        // Deactivate subscription
        \App\Models\StudentSubscriptions::where('student_id', $payment->student_id)
            ->where('month_id', $payment->month_id)
            ->update(['is_active' => 0]);

        return redirect()->back()->with('success', 'تم استرجاع الدفعة بنجاح');
    }

    /**
     * Statistics Dashboard
     */
    public function statistics()
    {
        // Daily statistics for last 30 days
        $dailyStats = Payment::where('status', 'paid')
            ->where('paid_at', '>=', Carbon::now()->subDays(30))
            ->selectRaw('DATE(paid_at) as date, COUNT(*) as count, SUM(amount) as total')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // Monthly statistics for last 12 months
        $monthlyStats = Payment::where('status', 'paid')
            ->where('paid_at', '>=', Carbon::now()->subMonths(12))
            ->selectRaw('YEAR(paid_at) as year, MONTH(paid_at) as month, COUNT(*) as count, SUM(amount) as total')
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        // Top courses by revenue
        $topCourses = Payment::where('status', 'paid')
            ->with('month')
            ->selectRaw('month_id, COUNT(*) as count, SUM(amount) as total')
            ->groupBy('month_id')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();

        // Payment methods statistics
        $paymentMethods = Payment::where('status', 'paid')
            ->selectRaw('payment_method, COUNT(*) as count, SUM(amount) as total')
            ->groupBy('payment_method')
            ->get();

        return view('back.payments.statistics', compact('dailyStats', 'monthlyStats', 'topCourses', 'paymentMethods'));
    }
}

