<?php

namespace Ogilo\PhoneBook\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use Ogilo\PhoneBook\Models\Contact;
use Ogilo\PhoneBook\Models\Telephone;
use Ogilo\PhoneBook\Models\Email;
use Ogilo\PhoneBook\Models\Address;

use Validator;
use Auth;

class PhoneBookController extends Controller
{
    function __construct(){
        $this->page = new \Ogilo\AdminMd\Models\Page;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getDashboard()
    {
        $contacts = Contact::with('telephones')->orderBy('first_name','ASC')->get();
        // dump($contacts);
        return view('phonebook::dashboard', compact('contacts'));
    }

    public function postUpload(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'file'=>'required|file'
        ]);

        if ($validator->fails()) {
            return redirect()
                    ->back()
                    ->withErrors($validator)
                    ->with('global-warning','Your file failed validation. Please check and try again');
        }

        $file = $request->file('file');

        $ext = $file->guessClientExtension();
        // dd($ext);

        if ($ext == 'csv') {
            $str = file_get_contents($file->getRealPath());
            $contacts = preg_split("/[\n]/", $str);

            $headers = explode(', ', array_slice($contacts, 0,1)[0]);
            // dd($headers);
            $contacts = array_slice($contacts, 1,count($contacts));

            foreach ($contacts as $key => $value) {
                try {
                    if($value){
                        $row = str_getcsv($value);

                        if(!(empty($row[0]) && empty($row[1]) && empty($row[2]))){

                            $contact = Contact::where('first_name',$row[0] ?? '')
                                                ->where('last_name',$row[1] ?? '')
                                                ->where('display_name',$row[2] ?? '')
                                                ->first();
                            // dd(compact('row','headers'));
                            if(is_null($contact)){
                                $contact = new Contact;
                                $first_name = "";
                                if(empty($row[0])){
                                    $n = explode(' ', trim(trim($row[2],'.')));
                                    $first_name = $n[array_key_first($n)];
                                }else{
                                    $first_name = $row[0];
                                }
                                $contact->first_name =  $first_name;

                                $last_name = "";
                                if(empty($row[1])){
                                    if(isset($row[2])){
                                        $n = explode(' ', trim(trim($row[2],'.')));
                                        $last_name = array_key_last($n)==1 ? $n[array_key_last($n)] : '';
                                    }
                                }else{
                                    $last_name = $row[1];
                                }
                                $contact->last_name = $last_name;

                                $contact->display_name = isset($row[2]) && !empty($row[2]) ? $row[2] : trim($row[0].' '. (isset($row[1]) ? $row[1] : ''));
                                $contact->save();
                            }
                            if(isset($row[7]) && !empty($row[7])){
                                $tel = Telephone::where('value',clean_isdn(str_replace('-','',$row[7])))->get();
                                if($tel->count() === 0){
                                    $telephone = new Telephone;
                                    $telephone->value = clean_isdn(str_replace('-','',$row[7]));
                                    $telephone->type = 'Work Phone';
                                    $contact->telephones()->save($telephone);
                                }
                            }
                            if(isset($row[8]) && !empty($row[8])){
                                $tel = Telephone::where('value',clean_isdn(str_replace('-','',$row[8])))->get();
                                if($tel->count() === 0){
                                    $telephone = new Telephone;
                                    $telephone->value = clean_isdn(str_replace('-','',$row[8]));
                                    $telephone->type = 'Home Phone';
                                    $contact->telephones()->save($telephone);
                                }
                            }
                            if(isset($row[11]) && !empty($row[11])){
                                $tel = Telephone::where('value',clean_isdn(str_replace('-','',$row[11])))->get();
                                if($tel->count() === 0){
                                    $telephone = new Telephone;
                                    $telephone->value = clean_isdn(str_replace('-','',$row[11]));
                                    $telephone->type = 'Mobile Number';
                                    $contact->telephones()->save($telephone);
                                }
                            }
                        }
                    }
                } catch (Exception $e) {
                    dump($e);
                }
                    

                // if($key == 199)
                //     break;
            }
        } elseif($ext=='vcard') {
            $str = file_get_contents($file->getRealPath());
            $contacts = preg_split("/(END:VCARD)/", preg_replace("/(BEGIN:VCARD\n)/","",$str));
            
            // dd($contacts);

            foreach ($contacts as $key => $value) {

                try {
                    

                    if($value){
                        $row = preg_split("/[\n]/", $value);
                        $tels = array_values(preg_grep("/^TEL:*/i", $row));
                        $emails = preg_grep("/^EMAIL:*/i", $row);
                        $addresses = preg_grep("/^ADR;*/i", $row);
                        
                        $ar_display_name = array_values(preg_grep("/^FN:*/i", $row));
                        $display_name = ltrim(isset($ar_display_name[0]) ? $ar_display_name[0] : '',"FN:");
                        
                        $names = array_values(preg_grep("/^N:*/i", $row));
                        
                        $names = preg_split("/[;]/",ltrim(current($names),"N:"));

                        // dd($names);
                        $pt = preg_split("/PHOTO;/",$value);
                        $photo = isset($pt[1]) ? $pt[1] : '';

                        if(!(empty($names[0]) && empty($names[1]) && empty($display_name))){

                            $first_name = "";
                            if(empty($names[0])){
                                $n = explode(' ', trim(trim($display_name,'.')));
                                $first_name = $n[array_key_first($n)];
                            }else{
                                $first_name = $names[0];
                            }
                            $last_name = "";
                            if(empty($names[1])){
                                if(isset($display_name)){
                                    $n = explode(' ', trim(trim($display_name,'.')));
                                    $last_name = array_key_last($n)==1 ? $n[array_key_last($n)] : '';
                                }
                            }else{
                                $last_name = $names[1];
                            }

                            $contact = Contact::where('first_name',$first_name)
                                                ->where('last_name',$last_name)
                                                ->where('display_name',$display_name)
                                                ->first();
                            // dd(compact('row','headers'));

                            if(is_null($contact)){
                                $contact = new Contact;
                                
                                $contact->first_name =  $first_name;
                                $contact->last_name = $last_name;

                                $contact->display_name = isset($display_name) && !empty($display_name) ? $display_name : trim($names[0].' '. (isset($names[1]) ? $names[1] : ''));

                                $contact->photo = $photo;
                                $contact->save();
                            }

                            foreach ($tels as $key => $item) {
                                $tl = preg_split("/[:]/",ltrim($item,"TEL;"));
                                // dd($tl);
                                $phone_no = $tl[1];
                                $type = $tl[0];

                                if(isset($phone_no) && !empty($type)){
                                    $tel = Telephone::where('value',clean_isdn($phone_no))->get();
                                    if($tel->count() === 0){
                                        $telephone = new Telephone;
                                        $telephone->value = clean_isdn($phone_no);
                                        $telephone->type = $type;
                                        $contact->telephones()->save($telephone);
                                    }
                                }
                            }

                            foreach ($emails as $key => $item) {
                                $m = preg_split("/[:]/",$item);
                                $mail = $m[1];
                                $type = $m[0];

                                if(isset($mail) && !empty($type)){
                                    $email = Email::where('value',$mail)->get();
                                    if($email->count() === 0){
                                        $email = new Email;
                                        $email->value = $mail;
                                        $email->type = $type;
                                        $contact->emails()->save($email);
                                    }
                                }
                            }

                            foreach ($addresses as $key => $item) {
                                $m = preg_split("/[:]/",$item);
                                $adr = $m[1];
                                $type = $m[0];

                                if(isset($adr) && !empty($type)){
                                    $address = Address::where('value',$adr)->get();
                                    if($address->count() === 0){
                                        $address = new Address;
                                        $address->value = $adr;
                                        $address->type = $type;
                                        $contact->addresses()->save($address);
                                    }
                                }
                            }

                        }
                    }
                } catch (Exception $e) {
                    dump($e);
                }
                    

                // if($key == 199)
                //     break;
            }
        }
        
        return redirect()
                ->back()
                ->with('global-success','Contacts Upload complete');
        // dd($contacts);
    }
}
