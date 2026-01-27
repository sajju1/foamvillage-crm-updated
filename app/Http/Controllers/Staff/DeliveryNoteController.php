<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
   use Illuminate\Support\Facades\DB;


use App\Models\Orders\Order;
use App\Models\Delivery\DeliveryNote;
use App\Models\Delivery\DeliveryNoteLine;
use App\Models\Customer\CustomerAddress;
use App\Models\Company\Company;

/*
| PDF / Mail dependencies (same as Portfolio)
*/
use Barryvdh\DomPDF\Facade\Pdf;
use App\Mail\GenericDocumentMail;

class DeliveryNoteController extends Controller
{
    /**
     * Show create delivery / collection note form
     */
    public function create(Order $order)
    {
        if ($order->deliveryNotes()->exists()) {
        return redirect()
            ->back()
            ->with('error', 'A delivery note has already been created for this order.');
    }
        $customer = $order->customer;

        $addresses = $customer->addresses()
            ->where('is_active', true)
            ->where('address_type', 'delivery')
            ->get();

        $defaultAddress = $customer->addresses()
            ->where('is_active', true)
            ->where('address_type', 'delivery')
            ->where('is_default', true)
            ->first();

        $order->load('orderLines.product', 'orderLines.productVariation');

        return view('staff.delivery-notes.create', compact(
            'order',
            'customer',
            'addresses',
            'defaultAddress'
        ));
    }

    /**
     * Store delivery / collection note
     */

public function store(Request $request, Order $order)
{

 // 🔒 Guard: prevent duplicate delivery notes
    if ($order->deliveryNotes()->exists()) {
        return redirect()
            ->back()
            ->with('error', 'This order already has a delivery note.');
    }
    $validated = $request->validate([
        'type' => ['required', 'in:delivery,collection'],
        'delivery_address_id' => ['nullable', 'exists:customer_addresses,id'],
        'lines' => ['required', 'array'],
        'lines.*.order_line_id' => ['required', 'exists:order_lines,id'],
        'lines.*.processed_quantity' => ['required', 'integer', 'min:0'],
    ]);

    DB::transaction(function () use ($validated, $order, &$deliveryNote) {

        $deliveryNote = DeliveryNote::create([
            'order_id' => $order->id,
            'customer_id' => $order->customer_id,
            'delivery_note_number' => $this->generateDeliveryNoteNumber(),
            'type' => $validated['type'],
            'delivery_address_id' => $validated['delivery_address_id'],
            'status' => 'issued',
            'issued_at' => now(),
            'created_by' => Auth::id(),
        ]);

        foreach ($validated['lines'] as $line) {
            DeliveryNoteLine::create([
                'delivery_note_id' => $deliveryNote->id,
                'order_line_id' => $line['order_line_id'],
                'processed_quantity' => $line['processed_quantity'],
            ]);
        }
    });

    return redirect()
        ->route('staff.delivery-notes.show', $deliveryNote)
        ->with('success', 'Delivery note created successfully.');
}


    /**
     * Show delivery / collection note (screen view)
     */
    public function show(DeliveryNote $deliveryNote)
    {
        $deliveryNote->load([
            'order',
            'customer',
            'address',
            'lines.orderLine.product',
            'lines.orderLine.productVariation',
        ]);

        $company = Company::first();

        return view('staff.delivery-notes.show', compact(
            'deliveryNote',
            'company'
        ));
    }


    /**
     * Print (browser)
     */
    public function print(DeliveryNote $deliveryNote)
    {
        $deliveryNote->load([
            'order',
            'customer',
            'address',
            'lines.orderLine.product',
            'lines.orderLine.productVariation',
        ]);

        $company = Company::first(); // active company

        return view('staff.delivery-notes.document', compact(
            'deliveryNote',
            'company'
        ));
    }

    /**
     * Download PDF
     */
    public function pdf(DeliveryNote $deliveryNote)
    {
        $deliveryNote->load([
            'order',
            'customer',
            'address',
            'lines.orderLine.product',
            'lines.orderLine.productVariation',
        ]);

        $company = Company::first();

        $pdf = Pdf::loadView(
            'staff.delivery-notes.document',
            compact('deliveryNote', 'company')
        );

        return $pdf->download(
            $deliveryNote->delivery_note_number . '.pdf'
        );
    }

    /**
     * Email to customer (PDF attachment)
     */
    public function email(DeliveryNote $deliveryNote)
    {
        $deliveryNote->load([
            'order',
            'customer',
            'address',
            'lines.orderLine.product',
            'lines.orderLine.productVariation',
        ]);

        $company = Company::first();

        $pdf = Pdf::loadView(
            'staff.delivery-notes.document',
            compact('deliveryNote', 'company')
        );

        Mail::to($deliveryNote->customer->email)
            ->send(new GenericDocumentMail(
                subject: strtoupper($deliveryNote->type) . ' Note ' . $deliveryNote->delivery_note_number,
                body: 'Please find attached your ' . $deliveryNote->type . ' note.',
                pdf: $pdf->output(),
                filename: $deliveryNote->delivery_note_number . '.pdf'
            ));

        return back()->with('success', 'Delivery note emailed successfully.');
    }

    /**
     * Generate delivery note number
     */
    protected function generateDeliveryNoteNumber(): string
    {
        return 'DN-' . now()->format('Ymd') . '-' . strtoupper(Str::random(5));
    }

    /**
     * List delivery & collection notes
     */
    public function index()
    {
        $deliveryNotes = DeliveryNote::with([
            'customer',
            'order'
        ])
            ->latest('issued_at')
            ->paginate(20);

        return view('staff.delivery-notes.index', compact(
            'deliveryNotes'
        ));
    }
}
