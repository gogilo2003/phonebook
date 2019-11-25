<?php

namespace Ogilo\PhoneBook\Console;

use Illuminate\Console\Command;

use Ogilo\PhoneBook\Models\Contact;

class PhoneBookCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'phonebook:fix';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'PhoneBook console toolset';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $start = new \DateTime();
        $this->comment('[ '.$start->format('d-m-Y H:i:s').' ] Started cleaning contact names');

        $contacts = Contact::all();
        $count = false;
        $counter = 0;

        $bar = $this->output->createProgressBar(count($contacts));

        foreach ($contacts as $key => $contact) {
            $display_name = explode(" ",$contact->display_name);
            foreach ($display_name as $key => $value) {
                $display_name[$key] = trim($value);
            }
            $first_name = trim($contact->first_name);
            $last_name = trim($contact->last_name);

            if (!empty($first_name) && !empty($display_name[1])) {
                if (strlen($first_name) > strlen($display_name[1])) {
                    $display_name[1] = $first_name;
                }elseif(strlen($display_name[1]) > strlen($first_name)){
                    $first_name = $display_name[1];
                }
            }

            if (!empty($last_name) && !empty($display_name[0])) {
                if (strlen($last_name) > strlen($display_name[0])) {
                    $display_name[0] = $last_name;
                }elseif(strlen($display_name[0]) > strlen($last_name)){
                    $last_name = $display_name[0];
                }
            }

            $contact->first_name = $first_name;
            $contact->last_name = $last_name;
            $contact->display_name = implode(" ",$display_name);

            $contact->save();

            $bar->advance();
        }
        $bar->finish();

        $stop = new \DateTime();
        $duration = $stop->diff($start);

        $this->comment('[ '.$stop->format('d-m-Y H:i:s').' ] Finished cleaning '.$count.'contact names in '.$duration->format('%I Minute(s) %S Second(s)'));
    }
}
