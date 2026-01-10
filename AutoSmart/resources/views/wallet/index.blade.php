@extends('layouts.app')

@section('title', 'المحفظة')

@section('content')
<div class="container py-4">
    <h1 class="h3 mb-4">المحفظة</h1>

    <div class="row">
        <div class="col-md-4">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body text-center py-4">
                    <h6 class="mb-1">الرصيد المتاح</h6>
                    <h2 class="mb-0">{{ number_format($wallet->balance, 2) }} ر.س</h2>
                </div>
            </div>

            @if(auth()->user()->isSeller())
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">طلب سحب</h6>
                    </div>
                    <div class="card-body">
                        @if($pendingWithdrawals->isNotEmpty())
                            <div class="alert alert-warning">
                                <i class="bi bi-clock"></i> لديك طلب سحب قيد المعالجة
                            </div>
                        @else
                            <form action="{{ route('wallet.withdraw') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">المبلغ (ر.س)</label>
                                    <input type="number" name="amount" class="form-control" min="100" max="{{ $wallet->balance }}" required>
                                    <div class="form-text">الحد الأدنى للسحب 100 ر.س</div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">اسم البنك</label>
                                    <input type="text" name="bank_name" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">رقم الحساب / IBAN</label>
                                    <input type="text" name="account_number" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">اسم صاحب الحساب</label>
                                    <input type="text" name="account_holder" class="form-control" required>
                                </div>
                                <button type="submit" class="btn btn-primary w-100" {{ $wallet->balance < 100 ? 'disabled' : '' }}>
                                    <i class="bi bi-cash-stack me-1"></i> طلب سحب
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">سجل المعاملات</h6>
                </div>
                <div class="card-body p-0">
                    @if($transactions->isEmpty())
                        <div class="text-center py-5">
                            <i class="bi bi-wallet2 display-4 text-muted"></i>
                            <p class="text-muted mt-2">لا توجد معاملات</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>التاريخ</th>
                                        <th>الوصف</th>
                                        <th>النوع</th>
                                        <th>المبلغ</th>
                                        <th>الرصيد</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($transactions as $transaction)
                                        <tr>
                                            <td>{{ $transaction->created_at->format('Y/m/d H:i') }}</td>
                                            <td>{{ $transaction->description }}</td>
                                            <td>
                                                <span class="badge bg-{{ $transaction->type_color }}">
                                                    {{ $transaction->type_label }}
                                                </span>
                                            </td>
                                            <td class="text-{{ $transaction->type === 'credit' ? 'success' : 'danger' }}">
                                                {{ $transaction->type === 'credit' ? '+' : '-' }}{{ number_format($transaction->amount, 2) }}
                                            </td>
                                            <td>{{ number_format($transaction->balance_after, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <div class="mt-4">
                {{ $transactions->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
