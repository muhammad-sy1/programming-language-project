@extends('layouts.admin')

@section('title', 'إدارة المستخدمين')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">جميع المستخدمين</h5>
                <a href="{{ url('admin/users/pending') }}" class="btn btn-warning">
                    <i class="fas fa-user-clock"></i> المستخدمين المعلقين
                    @if($pendingCount > 0)
                        <span class="badge bg-danger">{{ $pendingCount }}</span>
                    @endif
                </a>
            </div>
            
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>الصورة</th>
                                <th> الاسم</th>
                                <th> الكنية </th>
                                <th>رقم الهاتف</th>
                                <th>الحالة</th>
                                <th>تاريخ التسجيل</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <img src="{{ $user->profile_image ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=random' }}" 
                                         class="user-avatar" alt="{{ $user->name }}">
                                </td>
                                <td>{{ $user->first_name }}</td>
                                <td>{{ $user->last_name }}</td>
                                <td>{{ $user->phone ?? 'غير متوفر' }}</td>
                                <td>
                                    @if($user->status == 'pending')
                                        <span class="status-badge status-pending">معلق</span>
                                    @elseif($user->status == 'approved')
                                        <span class="status-badge status-approved">مفعل</span>
                                    @elseif($user->status == 'rejected')
                                        <span class="status-badge status-rejected">مرفوض</span>
                                    @endif
                                </td>
                                <td>{{ $user->created_at->format('Y-m-d') }}</td>
                                <td>
                                    <button class="btn btn-sm btn-info btn-action" 
                                            onclick="showUserDetails({{ $user->id }})" 
                                            title="عرض التفاصيل">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    
                                    @if($user->status == 'pending')
                                        <form action="{{ url('admin/users/' . $user->id . '/approve') }}" 
                                              method="POST" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success btn-action" title="موافقة">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                        
                                        <form action="{{ url('admin/users/' . $user->id . '/reject') }}" 
                                              method="POST" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-danger btn-action" title="رفض">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    @endif
                                    
                                    <button class="btn btn-sm btn-danger btn-action" 
                                            onclick="confirmDelete({{ $user->id }}, '{{ $user->first_name }}')"
                                            title="حذف المستخدم">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    
                                    <!-- نموذج الحذف المخفي -->
                                    <form id="delete-form-{{ $user->id }}" 
                                          action="{{ url('admin/users/' . $user->id) }}" 
                                          method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                @if($users->isEmpty())
                    <div class="text-center py-5">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <p class="text-muted">لا يوجد مستخدمين لعرضهم</p>
                    </div>
                @endif
                
                <!-- الترقيم -->
                @if($users->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection