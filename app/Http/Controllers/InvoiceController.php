<?php

namespace Vanguard\Http\Controllers;

use Vanguard\Http\Controllers\Traits\CompanyIdTrait;
use Vanguard\Services\Invoice\InvoiceDetails;
use Vanguard\Services\Invoice\InvoiceList;
use Yajra\DataTables\DataTables;
use Barryvdh\DomPDF\Facade as PDF;

class InvoiceController extends Controller
{
    use CompanyIdTrait;

    public function all()
    {
        return view('invoices.all-invoices');
    }

    public function getInvoiceDate(DataTables $dataTables)
    {
        $invoice_list_service = new InvoiceList($this->companyId());
        $invoice_list = $invoice_list_service->invoiceListQuery();
        return $dataTables->collection($invoice_list)
            ->addColumn('status', function ($invoice_list){
                if($invoice_list->status === 1){
                    return '<span class="span_state status_success">Approved</span></td>';
                }else{
                    return '<span class="span_state status_pending">Pending</span>';
                }
            })->addColumn('view', function ($pending_invoices){
                return '<a href="'.route('invoice.details', ['id' => $pending_invoices->invoice_id]).'" class="modal_view_invoice_click">view</a>';
            })
            ->rawColumns(['status' => 'status', 'view' => 'view'])
            ->addIndexColumn()
            ->make(true);
    }


    public function pending()
    {
        return view('invoices.pending-invoices');
    }

    public function pendingData(DataTables $dataTables)
    {
        $invoice_list_service = new InvoiceList($this->companyId());
        $pending_invoices = $invoice_list_service->pendingInvoiceListQuery();
        return $dataTables->collection($pending_invoices)
            ->addColumn('status', function ($pending_invoices){
                return '<span class="span_state status_pending">Pending</span>';
            })->addColumn('view', function ($pending_invoices){
                return '<a href="'.route('invoice.details', ['id' => $pending_invoices->invoice_id]).'" class="modal_view_invoice_click">view</a>';
            })
            ->rawColumns(['status' => 'status', 'view' => 'view'])
            ->addIndexColumn()
            ->make(true);
    }

    public function getInvoiceDetails($id)
    {
        $invoice_details_service = new InvoiceDetails($id);
        return view('invoices.invoice_details')->with('invoice_details', $invoice_details_service->invoiceDetailsData());
    }

    public function exportToPDF($id)
    {
        $invoice_details_service = new InvoiceDetails($id);
        $invoice_details = $invoice_details_service->invoiceDetailsData();
        $pdf = PDF::loadView('invoices.invoice_details_pdf', compact('invoice_details'));
        return $pdf->download($invoice_details['campaign_name'].'pdf');
    }

}
