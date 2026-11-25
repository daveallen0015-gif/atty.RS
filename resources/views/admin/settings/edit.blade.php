@extends('admin.layouts.app')

@section('title', 'System Settings')
@section('section', 'System Settings')

@section('content')
    <div class="page-heading"><div><p class="eyebrow">Public website</p><h1>System Settings</h1></div></div>
    <form method="POST" action="{{ route('admin.settings.update') }}" class="admin-form settings-editor" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="settings-tabs" role="tablist" aria-label="Landing page content categories">
            @foreach ($groups as $key => $group)
                <button id="settings-tab-{{ $key }}" class="settings-tab {{ $loop->first ? 'active' : '' }}" type="button" role="tab" aria-controls="settings-panel-{{ $key }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}" tabindex="{{ $loop->first ? '0' : '-1' }}">{{ $group['label'] }}</button>
            @endforeach
        </div>

        @foreach ($groups as $key => $group)
            <section id="settings-panel-{{ $key }}" class="settings-panel {{ $loop->first ? 'active' : '' }}" role="tabpanel" aria-labelledby="settings-tab-{{ $key }}" @if (!$loop->first) hidden @endif>
                <div class="settings-panel-heading"><h2>{{ $group['label'] }}</h2><p>{{ $group['description'] }}</p></div>
                <div class="row g-4">
                    @foreach ($group['fields'] as $fieldKey => $label)
                        @php($isLongText = str_ends_with($fieldKey, '_body') || str_ends_with($fieldKey, '_intro') || in_array($fieldKey, ['footer_note', 'site_description'], true))
                        <div class="col-12"><label for="content_{{ $fieldKey }}" class="form-label">{{ $label }}</label>
                            @if ($isLongText)
                                <textarea id="content_{{ $fieldKey }}" name="content[{{ $fieldKey }}]" rows="{{ $fieldKey === 'hero_body' || $fieldKey === 'about_body' ? 5 : 3 }}" class="form-control @error('content.'.$fieldKey) is-invalid @enderror">{{ old('content.'.$fieldKey, $contents->get($fieldKey)) }}</textarea>
                            @else
                                <input id="content_{{ $fieldKey }}" name="content[{{ $fieldKey }}]" @if(str_starts_with($fieldKey, 'social_')) type="url" placeholder="https://..." @endif value="{{ old('content.'.$fieldKey, $contents->get($fieldKey)) }}" class="form-control @error('content.'.$fieldKey) is-invalid @enderror">
                            @endif
                            @error('content.'.$fieldKey)<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    @endforeach
                </div>
                @if (!empty($group['background']))
                    <div class="settings-background">
                        <h3>Home background</h3>
                        <p class="form-hint">Choose a wide courthouse or office photo. JPG, PNG, or WebP up to 8 MB.</p>
                        @if ($contents->get('hero_background'))
                            <figure class="background-preview"><img src="{{ str_starts_with($contents->get('hero_background'), 'http') ? $contents->get('hero_background') : asset('storage/'.$contents->get('hero_background')) }}" alt="Current home background"></figure>
                        @endif
                        <label for="hero_background" class="form-label">Home hero background photo</label>
                        <input id="hero_background" type="file" name="hero_background" accept="image/jpeg,image/png,image/webp" class="form-control @error('hero_background') is-invalid @enderror">
                        @error('hero_background')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                @endif
                @if (!empty($group['navigation_logo']))
                    <div class="settings-background navigation-logo-settings">
                        <h3>Navigation bar logo</h3>
                        <p class="form-hint">Upload the image shown beside the lawyer's name in the public navigation bar. JPG, PNG, or WebP up to 8 MB.</p>
                        @if ($contents->get('navigation_logo'))
                            <figure class="navigation-logo-preview"><img src="{{ str_starts_with($contents->get('navigation_logo'), 'http') ? $contents->get('navigation_logo') : asset('storage/'.$contents->get('navigation_logo')) }}" alt="Current navigation logo"></figure>
                        @endif
                        <label for="navigation_logo" class="form-label">Navigation logo image</label>
                        <input id="navigation_logo" type="file" name="navigation_logo" accept="image/jpeg,image/png,image/webp" class="form-control @error('navigation_logo') is-invalid @enderror">
                        @error('navigation_logo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                @endif
            </section>
        @endforeach

        <div class="settings-save-bar"><span>Changes are saved together across all categories.</span><button class="btn btn-ink">Save Changes</button></div>
    </form>
    <script>
        (() => {
            const tabs = [...document.querySelectorAll('.settings-tab')];
            const panels = [...document.querySelectorAll('.settings-panel')];

            const activate = (tab, moveFocus = false) => {
                tabs.forEach((item) => {
                    const selected = item === tab;
                    item.classList.toggle('active', selected);
                    item.setAttribute('aria-selected', selected ? 'true' : 'false');
                    item.tabIndex = selected ? 0 : -1;
                });
                panels.forEach((panel) => {
                    const selected = panel.id === tab.getAttribute('aria-controls');
                    panel.hidden = !selected;
                    panel.classList.toggle('active', selected);
                });
                if (moveFocus) tab.focus();
            };

            tabs.forEach((tab, index) => {
                tab.addEventListener('click', () => activate(tab));
                tab.addEventListener('keydown', (event) => {
                    let nextIndex = index;
                    if (event.key === 'ArrowRight') nextIndex = (index + 1) % tabs.length;
                    else if (event.key === 'ArrowLeft') nextIndex = (index - 1 + tabs.length) % tabs.length;
                    else if (event.key === 'Home') nextIndex = 0;
                    else if (event.key === 'End') nextIndex = tabs.length - 1;
                    else return;
                    event.preventDefault();
                    activate(tabs[nextIndex], true);
                });
            });

            const invalidField = document.querySelector('.settings-panel .is-invalid');
            if (invalidField) {
                const panel = invalidField.closest('.settings-panel');
                const tab = tabs.find((item) => item.getAttribute('aria-controls') === panel.id);
                if (tab) activate(tab);
            }
        })();
    </script>
@endsection
