@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Maintenance Record</h1>

    <form action="{{ route('maintenance.records.update', $record->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="vehicle_id">Vehicle</label>
            <select name="vehicle_id" id="vehicle_id" class="form-control" required>
                @foreach($vehicles as $vehicle)
                    <option value="{{ $vehicle->id }}" {{ $record->vehicle_id == $vehicle->id ? 'selected' : '' }}>
                        {{ $vehicle->vehicle_number }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="technician_id">Technician</label>
            <select name="technician_id" id="technician_id" class="form-control" required>
                @foreach($technicians as $technician)
                    <option value="{{ $technician->id }}" {{ $record->technician_id == $technician->id ? 'selected' : '' }}>
                        {{ $technician->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="maintenance_type">Maintenance Type</label>
            <input type="text" name="maintenance_type" id="maintenance_type" class="form-control" value="{{ old('maintenance_type', $record->maintenance_type) }}" required>
        </div>

        <div class="form-group">
            <label for="service_date">Service Date</label>
            <input type="date" name="service_date" id="service_date" class="form-control" value="{{ old('service_date', $record->service_date->format('Y-m-d')) }}" required>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea name="description" id="description" class="form-control" required>{{ old('description', $record->description) }}</textarea>
        </div>

        <div class="form-group">
            <label for="mileage_at_service">Mileage at Service</label>
            <input type="number" name="mileage_at_service" id="mileage_at_service" class="form-control" value="{{ old('mileage_at_service', $record->mileage_at_service) }}" required>
        </div>

        <div class="form-group">
            <label for="parts_cost">Parts Cost</label>
            <input type="number" step="0.01" name="parts_cost" id="parts_cost" class="form-control" value="{{ old('parts_cost', $record->parts_cost) }}" required>
        </div>

        <div class="form-group">
            <label for="labor_cost">Labor Cost</label>
            <input type="number" step="0.01" name="labor_cost" id="labor_cost" class="form-control" value="{{ old('labor_cost', $record->labor_cost) }}" required>
        </div>

        <div class="form-group">
            <label for="notes">Notes</label>
            <textarea name="notes" id="notes" class="form-control">{{ old('notes', $record->notes) }}</textarea>
        </div>

        <div class="form-group">
            <label for="next_service_date">Next Service Date</label>
            <input type="date" name="next_service_date" id="next_service_date" class="form-control" value="{{ old('next_service_date', optional($record->next_service_date)->format('Y-m-d')) }}">
        </div>

        <div class="form-group">
            <label for="status">Status</label>
            <select name="status" id="status" class="form-control" required>
                <option value="scheduled" {{ $record->status == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                <option value="in_progress" {{ $record->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                <option value="completed" {{ $record->status == 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="cancelled" {{ $record->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary mt-3">Update Record</button>
        <a href="{{ route('maintenance.records') }}" class="btn btn-secondary mt-3">Cancel</a>
    </form>
</div>
@endsection
