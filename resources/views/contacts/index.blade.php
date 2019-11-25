@extends('admin::layout.main')

@section('title')
    PhoneBook::Contacts
@endsection

@section('page_title')
    <i class="material-icons">contacts</i> Contacts
@endsection

@section('sidebar')
    @parent
    @include('phonebook::sidebar')
@endsection

@section('content')
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Add Edit Contact</h5>
                    <div class="card-text">
                        <form method="post" action="{{ route('admin-phonebook-contacts-add') }}">
                            <div class="form-group">
                                <label for="first_name">First Name</label>
                                <input id="first_name" class="form-control" type="text" name="first_name">
                            </div>
                            <div class="form-group">
                                <label for="middle_name">Middle Name</label>
                                <input id="middle_name" class="form-control" type="text" name="middle_name">
                            </div>
                            <div class="form-group">
                                <label for="last_name">Last Name</label>
                                <input id="last_name" class="form-control" type="text" name="last_name">
                            </div>
                            <div class="form-group">
                                <label for="title">Title</label>
                                <input id="title" class="form-control" type="text" name="title">
                            </div>
                            <div class="form-group">
                                <label for="prefix">Prefix</label>
                                <input id="prefix" class="form-control" type="text" name="prefix">
                            </div>
                            <div class="form-group">
                                <label for="organisation">Organisation</label>
                                <input id="organisation" class="form-control" type="text" name="organisation">
                            </div>
                            <div class="card" id="telephone_numbers">
                                <div class="card-header">
                                    <h4 class="card-title">Telephone Numbers</h4>
                                </div>
                                <div class="telephone card-body">
                                    <div class="form-group">
                                        <label for="telephone_1">Telephone 1</label>
                                        <input id="telephone_1" class="form-control" type="text" name="telephone[0]['value']">
                                    </div>
                                    <div class="form-group">
                                        <label for="telephone_1_type">Telephone Type</label>
                                        <select id="telephone_1_type" class="form-control selectpicker" name="telephone[0]['type']" data-style="btn btn-link">
                                            <option>Home</option>
                                            <option>Work</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <a href="javascript:addTelephone()" class="btn btn-primary btn-fab btn-round"><span class="material-icons">add</span></a>
                                </div>
                            </div>
                            {{ csrf_field() }}
                            <button class="btn btn-primary btn-round" type="submit"><span class="material-icons">save</span> Save</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <form method="post" action="{{route('admin-phonebook-contacts-vcard')}}" role="form" accept-charset="UTF-8" enctype="multipart/form-data">
                <input type="hidden" name="_token" value="{{csrf_token()}}">
                <div class="row no-gutters">
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary btn-round"><span class="material-icons">contacts</span> Download</button>
                    </div>
                    <div class="col-ms-9"> 
                        {{ $contacts->render() }}
                    </div>
                </div>
                <table class="table table-bordered table-hover" id="contactsDataTable">
                    <thead class="thead-light text-uppercase">
                        <tr>
                            <th>#</th>
                            <th><a class="checkAll btn btn-link">All</a></th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Display Name</th>
                            <th>Phones</th>
                            <th>Emails</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($contacts as $key => $contact)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><input class="chkContact" type="checkbox" name="contact[{{ $key }}]" value="{{ $contact->id }}"></td>
                            <td>{{ $contact->first_name }}</td>
                            <td>{{ $contact->last_name }}</td>
                            <td>{{ $contact->display_name }}</td>
                            <td>{{ implode(', ', $contact->telephones->pluck('value')->toArray() ) }}</td>
                            <td>{{ implode(', ', $contact->emails->pluck('value')->toArray() ) }}</td>
                            <td>
                                <a class="btn btn-outline-danger btn-fab btn-round btn-sm" href="{{ route('admin-phonebook-contacts-delete',$contact->id) }}"><span class="material-icons">delete</span></a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

            </form>
            {{ $contacts->render() }}
        </div>
    </div>
@endsection

@section('scripts_bottom')
<script type="text/javascript">
    var i = 1
    function addTelephone(){
        i++
        let telephone = document.createElement('div')
        telephone.className = 'telephone'

        let formGroupTel = document.createElement('div')
        formGroupTel.className = 'form-group'

        let fieldId = 'telephone_'+i
        let labelCaption = 'Telephone '+i
        let inputCaption = 'Telephone '+i+' Type'

        let labelTelephone = document.createElement('label')
        labelTelephone.setAttribute('for',fieldId)
        labelTelephone.innerHTML = labelCaption
        formGroupTel.appendChild(labelTelephone)

        let inputTelephone = document.createElement('input')
        inputTelephone.setAttribute('type','text')
        inputTelephone.className = 'form-control'
        inputTelephone.setAttribute('id',fieldId)
        let name = 'telephone['+(i-1)+'][\'value\']'
        inputTelephone.setAttribute('name',name)
        formGroupTel.appendChild(inputTelephone)

        telephone.appendChild(formGroupTel)



        // `
        // <div class="telephone">
        //     <div class="form-group">
        //         <label for="telephone_${ i }">Telephone 1</label>
        //         <input id="telephone_1" class="form-control" type="text" name="telephone[0]['value']">
        //     </div>
        //     <div class="form-group">
        //         <label for="telephone_1_type">Telephone Type</label>
        //         <select id="telephone_1_type" class="form-control selectpicker" name="telephone[0]['type']" data-style="btn btn-link">
        //             <option>Home</option>
        //             <option>Work</option>
        //         </select>
        //     </div>
        // </div>
        // `

        let tn = document.getElementById('telephone_numbers')

        tn.appendChild(telephone)
    }
    // $('#contactsDataTable').dataTable();
    var checked = false;
    $(document).ready(function(){
        $('.checkAll').click(function(){
            checked = !checked
            $('.chkContact').attr('checked',checked)
        })
    })
</script>
@endsection
