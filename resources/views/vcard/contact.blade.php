BEGIN:VCARD
VERSION:2.1
N:{!! $contact->last_name !!};{{ $contact->first_name }};;;
FN:{!! $contact->display_name !!}
@foreach ($contact->telephones as $tel)
TEL;{{ $tel->type }}:{!! $tel->value !!}
@endforeach
@foreach ($contact->emails as $email)
{{ $email->type }}:{!! $email->value !!}
@endforeach
END:VCARD