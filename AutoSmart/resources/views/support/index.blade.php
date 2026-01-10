@extends('layouts.app')
@section('title', 'تذاكر الدعم')
@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><i class="bi bi-headset me-2"></i>تذاكر الدعم</h1>
        <a href="{{ route('support.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> تذكرة جديدة</a>
    </div>

    <div class="row mb-4">
        <div class="col-md-4"><a href="{{ route('support.faq') }}" class="card text-decoration-none h-100"><div class="card-body text-center"><i class="bi bi-question-circle display-4 text-primary"></i><h5 class="mt-2">الأسئلة الشائعة</h5></div></a></div>
        <div class="col-md-4"><a href="{{ route('support.help') }}" class="card text-decoration-none h-100"><div class="card-body text-center"><i class="bi bi-book display-4 text-success"></i><h5 class="mt-2">قاعدة المعرفة</h5></div></a></div>
        <div class="col-md-4"><a href="#" class="card text-decoration-none h-100" onclick="openChatbot()"><div class="card-body text-center"><i class="bi bi-chat-dots display-4 text-info"></i><h5 class="mt-2">المحادثة الآلية</h5></div></a></div>
    </div>

    <div class="card">
        <div class="card-header"><h5 class="mb-0">تذاكري</h5></div>
        <div class="card-body p-0">
            @if($tickets->isEmpty())
                <div class="text-center py-5"><i class="bi bi-ticket display-4 text-muted"></i><p class="text-muted mt-2">لا توجد تذاكر</p></div>
            @else
                <div class="table-responsive"><table class="table table-hover mb-0">
                    <thead><tr><th>رقم التذكرة</th><th>الموضوع</th><th>الفئة</th><th>الأولوية</th><th>الحالة</th><th>التاريخ</th><th></th></tr></thead>
                    <tbody>
                        @foreach($tickets as $ticket)
                            <tr>
                                <td><code>{{ $ticket->ticket_number }}</code></td>
                                <td>{{ Str::limit($ticket->subject, 40) }}</td>
                                <td>{{ $ticket->category }}</td>
                                <td><span class="badge bg-{{ $ticket->priority === 'urgent' ? 'danger' : ($ticket->priority === 'high' ? 'warning' : 'secondary') }}">{{ $ticket->priority_label }}</span></td>
                                <td><span class="badge bg-{{ $ticket->status === 'resolved' ? 'success' : ($ticket->status === 'open' ? 'primary' : 'info') }}">{{ $ticket->status_label }}</span></td>
                                <td>{{ $ticket->created_at->format('Y/m/d') }}</td>
                                <td><a href="{{ route('support.show', $ticket) }}" class="btn btn-sm btn-outline-primary">عرض</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table></div>
            @endif
        </div>
    </div>
    <div class="mt-4">{{ $tickets->links() }}</div>
</div>
@endsection
