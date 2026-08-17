@props(['status'])

@php
    $status = strtolower(trim($status ?? ''));
    
    switch ($status) {
        case 'approved':
        case 'active':
        case 'online':
            $classes = 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20';
            $label = $status === 'active' || $status === 'online' ? 'Active' : 'Approved';
            $icon = 'bi-check-circle-fill';
            break;
            
        case 'pending':
            $classes = 'bg-amber-500/10 text-amber-400 border-amber-500/20';
            $label = 'Pending Review';
            $icon = 'bi-clock-fill';
            break;
            
        case 'rejected':
            $classes = 'bg-rose-500/10 text-rose-450 border-rose-500/20';
            $label = 'Rejected';
            $icon = 'bi-x-circle-fill';
            break;
            
        case 'inactive':
        case 'offline':
            $classes = 'bg-slate-700/30 text-slate-400 border-slate-700/50';
            $label = $status === 'offline' ? 'Offline' : 'Inactive';
            $icon = 'bi-dash-circle-fill';
            break;
            
        case 'maintenance':
            $classes = 'bg-blue-500/10 text-blue-400 border-blue-500/20';
            $label = 'Maintenance';
            $icon = 'bi-tools';
            break;
            
        case 'draft':
            $classes = 'bg-slate-500/10 text-slate-300 border-slate-500/20';
            $label = 'Draft';
            $icon = 'bi-pencil-fill';
            break;
            
        case 'suspended':
            $classes = 'bg-purple-500/10 text-purple-400 border-purple-500/20';
            $label = 'Suspended';
            $icon = 'bi-slash-circle-fill';
            break;

        case 'archived':
            $classes = 'bg-slate-800 text-slate-500 border-slate-750';
            $label = 'Archived';
            $icon = 'bi-archive-fill';
            break;
            
        default:
            $classes = 'bg-slate-800 text-slate-400 border-slate-700';
            $label = ucfirst($status);
            $icon = 'bi-info-circle-fill';
            break;
    }
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 px-3 py-1 text-xs font-semibold border rounded-full transition-all duration-300 {$classes}"]) }}>
    <i class="bi {{ $icon }} text-[10px]"></i>
    <span>{{ $label }}</span>
</span>
