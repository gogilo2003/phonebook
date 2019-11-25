<?php

namespace Ogilo\PhoneBook\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use Ogilo\PhoneBook\Models\Contact;

use JeroenDesloovere\VCard\VCard;

use Validator;
use Auth;
use Storage;

class ContactController extends Controller
{
    function __construct(){
        $this->page = new \Ogilo\AdminMd\Models\Page;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getContacts(Request $request)
    {
        $contacts = $request->has('page') ? Contact::has('telephones')->orderBy('display_name')->paginate() : Contact::has('telephones')->orderBy('display_name')->paginate(9999);
        return view('phonebook::contacts.index',compact('contacts'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function postAdd(Request $request)
    {
        dd($request->all());
        $contacts = Contact::all();
        return view('phonebook::contacts.index',compact('contacts'));
    }

    public function postVcard(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'contact'=>'required'
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return redirect()
                    ->back()
                    ->withErrors($validator)
                    ->with('global-warning','<h4>You have a prloblem. Please check and try again</h4>'.make_html_list($errors,'ul'));
        }

        $contact = $request->contact;

        $contacts = Contact::has('telephones')->with('telephones','emails','addresses')->whereIn('id',$contact)->orderBy('display_name')->get();

        $contents = '';
        $headers = null;
        foreach ($contacts as $key => $contact) {
            $vcard = new VCard();

            $vcard->addName($contact->first_name,$contact->last_name);

            foreach ($contact->telephones as $key => $tel) {
                $vcard->addPhoneNumber($tel->value,$tel->type);
            }

            foreach ($contact->emails as $key => $email) {
                $vcard->addEmail($email->value);
            }

            $contents .= $vcard->getOutput();
            // return $vcard->download();
            // dd($contents);
            $headers = $vcard->getHeaders(true);
        }

        // $contents = view('phonebook::vcard.index',compact('contacts'));
        Storage::put('contacts.vcf', $contents);

        return response()->download(storage_path('app/contacts.vcf'));

    }

    public function postDelete($id)
    {
        $validator = Validator::make(compact('id'),[
            'id'=>'required|exists:contacts'
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            // dd($errors);
            return redirect()
                    ->back()
                    ->withErrors($validator)
                    ->with('global-warning','<h4>Validation error</h4>'.make_html_list($errors,'ul'));
        }

        $contact = Contact::find($id);

        $contact->telephones()->delete();
        $contact->emails()->delete();
        $contact->addresses()->delete();

        $contact->delete();

        return redirect()
                    ->back()
                    ->with('global-success','Contact Deleted');
    }

}
