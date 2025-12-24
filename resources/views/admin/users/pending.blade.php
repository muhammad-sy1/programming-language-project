@extends('layouts.admin')

@section('title', 'المستخدمين المعلقين')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">المستخدمين المعلقين للمراجعة</h5>
                <a href="{{ url('admin/users') }}" class="btn btn-primary">
                    <i class="fas fa-arrow-right"></i> جميع المستخدمين
                </a>
            </div>
            
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h2 class="text-primary">{{ $pendingUsers->count() }}</h2>
                                <p class="mb-0">مستخدمين بانتظار المراجعة</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                @if($pendingUsers->isEmpty())
                    <div class="text-center py-5">
                        <i class="fas fa-user-check fa-3x text-success mb-3"></i>
                        <h4 class="text-success">لا يوجد مستخدمين معلقين</h4>
                        <p class="text-muted">جميع طلبات التسجيل تمت مراجعتها</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>البيانات الشخصية</th>
                                    <th>معلومات الاتصال</th>
                                    <th>الوثائق المرفوعة</th>
                                    <th>تاريخ التسجيل</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingUsers as $user)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $user->profile_image ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=random' }}" 
                                                 class="user-avatar me-3" alt="{{ $user->name }}">
                                            <div>
                                                <strong>{{ $user->name }}</strong><br>
                                                <small class="text-muted">{{ $user->national_id ?? 'غير متوفر' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <i class="fas fa-envelope text-primary"></i> {{ $user->email }}<br>
                                        <i class="fas fa-phone text-secondary"></i> {{ $user->phone ?? 'غير متوفر' }}
                                    </td>
                                    <td>
                                        @if($user->documents && count($user->documents) > 0)
                                            @foreach($user->documents as $doc)
                                                <a href="{{ Storage::url($doc) }}" target="_blank" class="badge bg-info me-1">
                                                    <i class="fas fa-file"></i> وثيقة {{ $loop->iteration }}
                                                </a>
                                            @endforeach
                                        @else
                                            <span class="text-muted">لا توجد وثائق</span>
                                        @endif
                                    </td>
                                    <td>{{ $user->created_at->diffForHumans() }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-info" 
                                                    onclick="showUserDetails({{ $user->id }})">
                                                <i class="fas fa-eye"></i> عرض
                                            </button>
                                            
                                            <form action="{{ url('admin/users/' . $user->id . '/approve') }}" 
                                                  method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success">
                                                    <i class="fas fa-check"></i> موافقة
                                                </button>
                                            </form>
                                            
                                            <form action="{{ url('admin/users/' . $user->id . '/reject') }}" 
                                                  method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-danger" 
                                                        onclick="return confirm('هل أنت متأكد من رفض هذا المستخدم؟')">
                                                    <i class="fas fa-times"></i> رفض
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection