<?php

namespace App\Http\Controllers\Manage;

use App\DataTables\PaymentDataTable;
use App\Enums\PaymentStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PaymentController extends Controller
{
    public function index(PaymentDataTable $dataTable)
    {
        return $dataTable->render('manage.payments.index');
    }

    public function show(Payment $payment)
    {
        $payment->load(['user','items.webResource.culturalObjects']);
        return view('manage.payments.show', compact('payment'));
    }

    public function edit(Payment $payment)
    {
        return view('manage.payments.edit', compact('payment'));
    }

    public function update(Request $request, Payment $payment)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(PaymentStatusEnum::getReadableStatus()))],
            'admin_note' => ['nullable', 'string'],
            'external_transaction_id' => ['nullable', 'string'],
        ]);

        $payment->update($data);

        return redirect()
            ->route('manage.payments.show', $payment)
            ->with('success', 'Плащането е обновено успешно.');
    }
}


