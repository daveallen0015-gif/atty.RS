@php($icon = match (true) {
    str_contains($category->slug, 'family') => '♡',
    str_contains($category->slug, 'criminal') => '⚖',
    str_contains($category->slug, 'labor') => '⌁',
    str_contains($category->slug, 'business') => '▤',
    str_contains($category->slug, 'property') => '⌂',
    str_contains($category->slug, 'taxation') => '₱',
    str_contains($category->slug, 'human-rights') => '◇',
    str_contains($category->slug, 'constitutional') => '§',
    str_contains($category->slug, 'civil') => '▱',
    default => '▣',
})
<span>{{ $icon }}</span>
