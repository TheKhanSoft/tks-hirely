@props(['type' => 'success', 'message' => null])

<div class="card" style="border-color: var(--{{ $type === 'success' ? 'success' : 'danger' }}); background: rgba({{ $type === 'success' ? '16, 185, 129' : '239, 68, 68' }}, 0.1); margin-bottom: 2rem; position: relative;">
    <p style="color: var(--{{ $type === 'success' ? 'success' : 'danger' }}); font-weight: 600; margin: 0; display: flex; align-items: center; justify-content: space-between;">
        <span>{{ $message ?? $slot }}</span>
        <button type="button" style="background: none; border: none; color: inherit; font-size: 1.2rem; cursor: pointer; line-height: 1;" onclick="this.parentElement.parentElement.remove()">×</button>
    </p>
</div>
