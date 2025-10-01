@extends('layouts.app')

@section('title', 'My Service Requests')

@section('content')
<div class="container-fluid py-4">
  <div class="row justify-content-center">
    <div class="col-lg-10">

      <div class="card shadow-sm border-0 mb-3">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h1 class="h4 m-0 fw-bold">My Service Requests</h1>
            <a href="{{ route('support.service-requests.create') }}" class="btn btn-primary">
              <i class="fas fa-plus me-2"></i>New Request
            </a>
          </div>
        </div>
      </div>

      {{-- Filters --}}
      <form method="get" class="card shadow-sm border-0 mb-4">
        <div class="card-body">
          <div class="row g-3 align-items-end">

            <div class="col-sm-6 col-md-3">
              <label class="form-label">Status</label>
              <select name="status" class="form-select">
                @php $status = request('status','all'); @endphp
                <option value="all" {{ $status==='all'?'selected':'' }}>All Status</option>
                <option value="open" {{ $status==='open'?'selected':'' }}>Open</option>
                <option value="in_progress" {{ $status==='in_progress'?'selected':'' }}>In Progress</option>
                <option value="pending" {{ $status==='pending'?'selected':'' }}>Pending</option>
                <option value="resolved" {{ $status==='resolved'?'selected':'' }}>Resolved</option>
                <option value="closed" {{ $status==='closed'?'selected':'' }}>Closed</option>
                <option value="cancelled" {{ $status==='cancelled'?'selected':'' }}>Cancelled</option>
              </select>
            </div>

            <div class="col-sm-6 col-md-3">
              <label class="form-label">Priority</label>
              <select name="priority" class="form-select">
                @php $priority = request('priority','all'); @endphp
                <option value="all" {{ $priority==='all'?'selected':'' }}>All Priorities</option>
                <option value="low" {{ $priority==='low'?'selected':'' }}>Low</option>
                <option value="medium" {{ $priority==='medium'?'selected':'' }}>Medium</option>
                <option value="high" {{ $priority==='high'?'selected':'' }}>High</option>
                <option value="critical" {{ $priority==='critical'?'selected':'' }}>Critical</option>
              </select>
            </div>

            <div class="col-sm-6 col-md-3">
              <label class="form-label">From</label>
              <input type="date" name="from" value="{{ request('from') }}" class="form-control">
            </div>

            <div class="col-sm-6 col-md-3">
              <label class="form-label">To</label>
              <input type="date" name="to" value="{{ request('to') }}" class="form-control">
            </div>

            <div class="col-md-8">
              <label class="form-label">Search</label>
              <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Ticket #, subject...">
            </div>

            <div class="col-md-4 d-flex gap-2">
              <button class="btn btn-outline-secondary w-100" type="submit">
                <i class="fas fa-filter me-2"></i>Filter
              </button>
              <a href="{{ route('driver.service-requests.index') }}" class="btn btn-light w-100">
                <i class="fas fa-undo me-2"></i>Reset
              </a>
            </div>
          </div>
        </div>
      </form>

      {{-- Table --}}
      <div class="card shadow-sm border-0">
        <div class="table-responsive">
          <table class="table align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th>Ticket #</th>
                <th>Subject</th>
                <th>Vehicle</th>
                <th>Priority</th>
                <th>Status</th>
                <th>Created</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
            @forelse($requests as $r)
              <tr>
                <td class="fw-semibold">{{ $r->ticket_number }}</td>
                <td>
                  <div class="fw-semibold">{{ $r->subject }}</div>
                  <small class="text-muted text-capitalize">{{ $r->category }}</small>
                </td>
                <td>
                  @if($r->vehicle)
                    <small>{{ $r->vehicle->vehicle_number ?? $r->vehicle->license_plate }}</small>
                  @else
                    <small class="text-muted">—</small>
                  @endif
                </td>
                <td>
                  @php
                    $pcls = ['low'=>'bg-success-subtle text-success','medium'=>'bg-info-subtle text-info','high'=>'bg-warning-subtle text-warning','critical'=>'bg-danger-subtle text-danger'];
                  @endphp
                  <span class="badge {{ $pcls[$r->priority] ?? 'bg-secondary' }} text-uppercase">{{ $r->priority }}</span>
                </td>
                <td>
                  @php
                    $scls = ['open'=>'bg-danger-subtle text-danger','in_progress'=>'bg-primary-subtle text-primary','pending'=>'bg-warning-subtle text-warning','resolved'=>'bg-success-subtle text-success','closed'=>'bg-secondary','cancelled'=>'bg-dark'];
                  @endphp
                  <span class="badge {{ $scls[$r->status] ?? 'bg-secondary' }} text-capitalize">{{ str_replace('_',' ',$r->status) }}</span>
                </td>
                <td>
                  <small class="text-muted">{{ $r->created_at->format('M d, Y') }}<br>{{ $r->created_at->format('h:i A') }}</small>
                </td>
                <td class="text-end">
                  <a href="{{ route('support.service-requests.show', $r) }}" class="btn btn-sm btn-outline-success">
                    <i class="fas fa-eye"></i>
                  </a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="text-center text-muted py-5">
                  No requests found for the selected filters.
                </td>
              </tr>
            @endforelse
            </tbody>
          </table>
        </div>

        @if($requests->hasPages())
          <div class="card-body border-top">
            {{ $requests->links() }}
          </div>
        @endif
      </div>

    </div>
  </div>
</div>
@endsection
