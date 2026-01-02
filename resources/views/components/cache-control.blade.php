{{-- Cache Control Component for Admin --}}
@if(auth()->guard('web')->check())
<div class="cache-control-panel" style="position: fixed; bottom: 20px; left: 20px; z-index: 9999; background: white; padding: 15px; border-radius: 10px; box-shadow: 0 5px 20px rgba(0,0,0,0.2);">
    <h6 class="mb-2"><i class="fas fa-bolt"></i> Cache Control</h6>
    <form action="{{ route('admin.cache.clear') }}" method="POST" style="margin-bottom: 10px;">
        @csrf
        <button type="submit" class="btn btn-sm btn-danger w-100">
            <i class="fas fa-trash"></i> Clear All Cache
        </button>
    </form>
    <form action="{{ route('admin.cache.clearSpecific') }}" method="POST">
        @csrf
        <select name="type" class="form-select form-select-sm mb-2">
            <option value="lectures">Lectures Cache</option>
            <option value="stats">Stats Cache</option>
        </select>
        <button type="submit" class="btn btn-sm btn-warning w-100">
            <i class="fas fa-sync"></i> Clear Specific
        </button>
    </form>
</div>
@endif

<style>
.cache-control-panel {
    display: none; /* Hidden by default, show with CSS or JS */
}
@media (min-width: 1200px) {
    .cache-control-panel {
        display: block;
    }
}
</style>




