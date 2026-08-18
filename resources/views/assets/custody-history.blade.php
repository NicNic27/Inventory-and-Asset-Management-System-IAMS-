<div class="custody-history-panel">
    <div class="d-flex justify-content-between align-items-start border-bottom pb-3 mb-3">
        <div>
            <div class="text-uppercase small fw-bold text-primary">Asset Custody</div>
            <h5 class="fw-bold mb-1">{{ $asset->article }}</h5>
            <div class="font-monospace text-muted small">{{ $asset->barcode_id }}</div>
        </div>
        <span class="badge {{ $asset->status === 'Serviceable' ? 'bg-success' : 'bg-danger' }}">{{ $asset->status }}</span>
    </div>

    @forelse($history as $custody)
        @php
            $wasTransferred = $custody->transaction_type === 'Transferred'
                || str_contains(strtolower((string) $custody->remarks), 'transferred');
            $state = $custody->returned_at ? ($wasTransferred ? 'Transferred' : 'Returned') : 'Currently out';
            $stateClass = $custody->returned_at ? ($wasTransferred ? 'text-primary' : 'text-success') : 'text-warning';
        @endphp
        <article class="border rounded-3 p-3 mb-2 bg-white">
            <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                <div>
                    <div class="fw-bold">{{ $custody->holder_name ?: 'Unknown holder' }}</div>
                    <div class="small text-muted">{{ $custody->holder_position ?: 'Position not recorded' }}</div>
                </div>
                <span class="small fw-bold {{ $stateClass }}">{{ $state }}</span>
            </div>
            <div class="row g-2 small">
                <div class="col-6"><span class="text-muted d-block">Action</span><strong>{{ $wasTransferred ? 'Transferred' : $custody->transaction_type }}</strong></div>
                <div class="col-6"><span class="text-muted d-block">Issued / Transfer Date</span><strong>{{ $custody->issued_at?->format('M d, Y') ?: 'N/A' }}</strong></div>
                <div class="col-6"><span class="text-muted d-block">Department</span><strong>{{ $custody->department ?: 'N/A' }}</strong></div>
                <div class="col-6"><span class="text-muted d-block">Unit / Office</span><strong>{{ $custody->unit ?: 'N/A' }}</strong></div>
                @if($custody->due_at)
                    <div class="col-6"><span class="text-muted d-block">Expected Return</span><strong>{{ $custody->due_at->format('M d, Y') }}</strong></div>
                @endif
                @if($custody->returned_at)
                    <div class="col-6"><span class="text-muted d-block">{{ $wasTransferred ? 'Transferred On' : 'Returned On' }}</span><strong>{{ $custody->returned_at->format('M d, Y') }}</strong></div>
                @endif
            </div>
            @if($custody->remarks)
                <div class="small text-muted border-top mt-2 pt-2">{{ $custody->remarks }}</div>
            @endif
        </article>
    @empty
        <div class="text-center text-muted py-5">
            <i class="fas fa-clipboard-check fa-2x mb-2 opacity-50"></i>
            <p class="mb-0">No borrowing or transfer history recorded.</p>
        </div>
    @endforelse
</div>
