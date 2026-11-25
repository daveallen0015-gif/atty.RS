<svg viewBox="0 0 48 48" aria-hidden="true" focusable="false">
    @switch($icon)
        @case('document')
            <path d="M12 5h16l9 9v29H12z"/><path d="M28 5v10h9M18 23h13M18 29h13M18 35h9"/>
            @break
        @case('family')
            <circle cx="17" cy="15" r="5"/><circle cx="32" cy="17" r="4"/><path d="M6 39c0-8 4-13 11-13s11 5 11 13m-1-9c2-2 4-3 7-3 6 0 9 4 9 11v1H29"/>
            @break
        @case('property')
            <path d="m5 22 19-16 19 16M10 20v22h28V20M19 42V28h10v14M16 23h4m8 0h4"/>
            @break
        @case('business')
            <rect x="6" y="15" width="36" height="27" rx="1"/><path d="M17 15V9h14v6M6 25h36M20 24v5h8v-5"/>
            @break
        @case('labor')
            <path d="m4 24 10-9 9 5 5-4a6 6 0 0 1 8 1l7-4 3 5-10 9 5 5-5 5-7-5-5 5-5-4-6 5-5-5 6-6-7-4-5 5-6-8Z"/>
            @break
        @case('shield')
            <path d="m24 4 16 6v12c0 11-7 18-16 22C15 40 8 33 8 22V10l16-6Z"/><path d="M24 13v13m0 8h.01"/>
            @break
        @case('estate')
            <path d="M24 42V13m0 9L12 13m12 17 12-11M24 16 34 6M12 13l-4 1 2 4m24-12v4m-24 6 3-1m14 24h14M17 42h14"/><circle cx="24" cy="9" r="3"/>
            @break
        @default
            <path d="M24 5v34m-15-27h30M14 12 4 29h20L14 12Zm20 0L24 29h20L34 12ZM18 40h12m-6-1v5m-13 0h26"/>
    @endswitch
</svg>
