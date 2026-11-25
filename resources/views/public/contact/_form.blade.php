<form method="POST" action="{{ route('contact.store') }}" class="public-contact-form" data-confirm-title="Send this inquiry?" data-confirm-message="Your message will be sent to the lawyer's office." data-confirm-button="Yes, send inquiry">
    @csrf
    @if (session('status'))<p class="form-success auto-dismiss-message" role="status">{{ session('status') }}</p>@endif
    <div class="row g-4">
        <div class="col-md-6"><label for="name" class="form-label">Name</label><input id="name" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" required autocomplete="name">@error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="col-md-6"><label for="email" class="form-label">Email</label><input id="email" type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" required autocomplete="email">@error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="col-md-6"><label for="phone" class="form-label">Phone <span>(optional)</span></label><input id="phone" name="phone" value="{{ old('phone') }}" class="form-control @error('phone') is-invalid @enderror" autocomplete="tel">@error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="col-md-6"><label for="subject" class="form-label">Subject</label><select id="subject" name="subject" class="form-select @error('subject') is-invalid @enderror" required><option value="">Choose a subject</option>@foreach ($subjectOptions as $subjectOption)<option value="{{ $subjectOption }}" @selected(old('subject', $serviceTitle ?? '') === $subjectOption)>{{ $subjectOption }}</option>@endforeach</select>@error('subject')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div id="other-subject-field" class="col-12" @if(old('subject') !== 'Other') hidden @endif><label for="other_subject" class="form-label">Tell us your subject</label><input id="other_subject" name="other_subject" value="{{ old('other_subject') }}" maxlength="255" class="form-control @error('other_subject') is-invalid @enderror" @required(old('subject') === 'Other')>@error('other_subject')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="col-12"><label for="message" class="form-label">Message</label><textarea id="message" name="message" rows="7" class="form-control @error('message') is-invalid @enderror" required>{{ old('message') }}</textarea>@error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="col-12"><button class="btn btn-ink">Send inquiry</button></div>
    </div>
    <script>
        (() => {
            const subject = document.querySelector('#subject');
            const otherField = document.querySelector('#other-subject-field');
            const otherInput = document.querySelector('#other_subject');

            const toggleOtherSubject = () => {
                const isOther = subject.value === 'Other';
                otherField.hidden = !isOther;
                otherInput.required = isOther;
                if (!isOther) otherInput.value = '';
            };

            subject.addEventListener('change', toggleOtherSubject);
            toggleOtherSubject();
        })();
    </script>
</form>
