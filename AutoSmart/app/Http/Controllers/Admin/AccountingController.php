<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AccountingIntegrationService;
use Illuminate\Http\Request;

class AccountingController extends Controller
{
    protected $accounting;

    public function __construct(AccountingIntegrationService $accounting)
    {
        $this->middleware(['auth', 'role:admin']);
        $this->accounting = $accounting;
    }

    public function index()
    {
        return view('admin.accounting.index');
    }

    public function salesReport(Request $request)
    {
        $startDate = $request->start_date ?? now()->startOfMonth()->format('Y-m-d');
        $endDate = $request->end_date ?? now()->format('Y-m-d');

        $report = $this->accounting->exportSalesReport($startDate, $endDate);

        return view('admin.accounting.sales', compact('report', 'startDate', 'endDate'));
    }

    public function posReport(Request $request)
    {
        $date = $request->date ?? now()->format('Y-m-d');
        $report = $this->accounting->getPosReport($date);

        return view('admin.accounting.pos', compact('report', 'date'));
    }

    public function vatReport(Request $request)
    {
        $startDate = $request->start_date ?? now()->startOfMonth()->format('Y-m-d');
        $endDate = $request->end_date ?? now()->format('Y-m-d');

        $report = $this->accounting->getVatReport($startDate, $endDate);

        return view('admin.accounting.vat', compact('report', 'startDate', 'endDate'));
    }

    public function exportSales(Request $request)
    {
        $startDate = $request->start_date ?? now()->startOfMonth()->format('Y-m-d');
        $endDate = $request->end_date ?? now()->format('Y-m-d');

        $report = $this->accounting->exportSalesReport($startDate, $endDate);

        // Generate CSV
        $headers = ['رقم الطلب', 'التاريخ', 'العميل', 'المجموع الفرعي', 'الضريبة', 'الخصم', 'الإجمالي', 'طريقة الدفع', 'الحالة'];
        $csv = implode(',', $headers)."\n";

        foreach ($report['data'] as $row) {
            $csv .= implode(',', array_values($row->toArray()))."\n";
        }

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="sales_report_'.$startDate.'_'.$endDate.'.csv"');
    }
}
