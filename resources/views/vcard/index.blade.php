@foreach ($contacts as $contact)
@include('phonebook::vcard.contact', compact('contact'))
@endforeach