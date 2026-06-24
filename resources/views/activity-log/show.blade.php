{{-- resources/views/activity-log/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Detail Log')
@section('page-title', 'Detail Log Aktivitas')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('activity-log.index') }}">Activity Log</a></li>
    <li class="breadcrumb-item active">#{{ $activityLog->id }}</li>
@endsection

@section('content')
<div class="row justify-content-center">
<div class="col-md-9">

<div class="card card-outline card-secondary">
    <div class="card-header">
        <h3 class="card-title">
            {!! $activityLog->action_badge !!}
            <span class="ml-2">{{ $activityLog->description }}</span>
        </h3>
    </div>
    <div class="card-body">
        <table class="table table-sm table-borderless mb-4">
            <tr>
                <td class="text-muted" width="25%">Waktu</td>
                <td>{{ $activityLog->created_at->format('d F Y, H:i:s') }}</td>
            </tr>
            <tr>
                <td class="text-muted">User</td>
                <td>{{ $activityLog->user?->name ?? 'System' }}
                    @if($activityLog->user)
                    <small class="text-muted">({{ $activityLog->user->role }})</small>
                    @endif
                </td>
            </tr>
            <tr>
                <td class="text-muted">IP Address</td>
                <td><code>{{ $activityLog->ip_address ?? '—' }}</code></td>
            </tr>
            <tr>
                <td class="text-muted">Modul</td>
                <td>{{ $activityLog->model_label }} #{{ $activityLog->model_id }}</td>
            </tr>
        </table>

        @if($activityLog->old_values || $activityLog->new_values)
        <h6 class="font-weight-bold text-uppercase text-muted mb-3"
            style="font-size:11px;letter-spacing:.08em">
            <i class="fas fa-exchange-alt mr-1"></i> Detail Perubahan
        </h6>

        <table class="table table-bordered table-sm">
            <thead class="thead-light">
                <tr>
                    <th width="30%">Field</th>
                    <th>Nilai Lama</th>
                    <th>Nilai Baru</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $allKeys = array_unique(array_merge(
                        array_keys($activityLog->old_values ?? []),
                        array_keys($activityLog->new_values ?? [])
                    ));
                @endphp
                @foreach($allKeys as $key)
                @php
                    $old = $activityLog->old_values[$key] ?? null;
                    $new = $activityLog->new_values[$key] ?? null;
                    $changed = $old !== $new;
                @endphp
                <tr class="{{ $changed ? 'table-warning' : '' }}">
                    <td><code>{{ $key }}</code></td>
                    <td class="text-danger">
                        {{ is_array($old) ? json_encode($old) : ($old ?? '—') }}
                    </td>
                    <td class="text-success font-weight-bold">
                        {{ is_array($new) ? json_encode($new) : ($new ?? '—') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p class="text-muted text-center py-3">Tidak ada detail perubahan untuk log ini.</p>
        @endif
    </div>
    <div class="card-footer">
        <a href="{{ route('activity-log.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left mr-1"></i> Kembali
        </a>
    </div>
</div>

</div>
</div>
@endsection
