<?php

namespace App\Repositories;

use App\Models\Admin;
use App\Models\Center;
use App\Models\Contact;
use App\Models\Image;
use App\Models\OpenDay;
use App\Models\MemberDay;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Validator;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class  CenterRepository
{

    public function add(Request $request) //: User
    {
        //first add Owner of center

        // secound create new center and assign to owner
        $center = new Center(populateModelData($request, Center::class));
        $center->save();
        if ($request->hasFile('images')) {
            $photos = uploadMultiImages("images", "centers");
        }

        $image = new Image(populateModelData($request, Image::class));


        if ($request->hasFile('logo'))
            $image->logo = Storage::disk('public')->put('center_logo', $request->file('logo'));

        $image->images = $photos;

        $image->center()->associate($center->id);
        $image->save();
        if ($whatsapp = $request->get('whatsapp')) {
            $contact = new Contact();
            $contact->key = "WHATSAPP";
            $contact->value = $whatsapp;
            $contact->center()->associate($center->id);
            $contact->save();
        }
        if ($X = $request->get('X')) {
            $contact = new Contact();
            $contact->key = "X";
            $contact->value = $X;
            $contact->center()->associate($center->id);
            $contact->save();
        }
        if ($telegram = $request->get('telegram')) {
            $contact = new Contact();
            $contact->key = "TELEGRAM";
            $contact->value = $telegram;
            $contact->center()->associate($center->id);
            $contact->save();
        }
        if ($facebook = $request->get('facebook')) {
            $contact = new Contact();
            $contact->key = "FACEBOOK";
            $contact->value = $facebook;
            $contact->center()->associate($center->id);
            $contact->save();
        }


        // saturdayOpen
        // saturdayClose
        if ($saturdayOpen = $request->get('saturdayOpen')) {
            $day = new OpenDay();
            $day->day = "Saturday";
            $day->open = $saturdayOpen;
            $day->close = $request->get('saturdayClose');
            $day->center()->associate($center->id);
            $day->save();
        }
        // sundayOpen
        // sundayClose
        if ($sundayOpen = $request->get('sundayOpen')) {
            $day = new OpenDay();
            $day->day = "Sunday";
            $day->open = $sundayOpen;
            $day->close = $request->get('sundayClose');
            $day->center()->associate($center->id);
            $day->save();
        }
        // mondayOpen
        // mondayClose
        if ($mondayOpen = $request->get('mondayOpen')) {
            $day = new OpenDay();
            $day->day = "Monday";
            $day->open = $mondayOpen;
            $day->close = $request->get('mondayClose');
            $day->center()->associate($center->id);
            $day->save();
        }
        // tuesdayOpen
        // tuesdayClose
        if ($tuesdayOpen = $request->get('tuesdayOpen')) {
            $day = new OpenDay();
            $day->day = "Tuesday";
            $day->open = $tuesdayOpen;
            $day->close = $request->get('tuesdayClose');
            $day->center()->associate($center->id);
            $day->save();
        }
        // wednesdayOpen
        // wednesdayClose
        if ($wednesdayOpen = $request->get('wednesdayOpen')) {
            $day = new OpenDay();
            $day->day = "Wednesday";
            $day->open = $wednesdayOpen;
            $day->close = $request->get('wednesdayClose');
            $day->center()->associate($center->id);
            $day->save();
        }
        // thursdayOpen
        // thursdayClose
        if ($thursdayOpen = $request->get('thursdayOpen')) {
            $day = new OpenDay();
            $day->day = "Thursday";
            $day->open = $thursdayOpen;
            $day->close = $request->get('thursdayClose');
            $day->center()->associate($center->id);
            $day->save();
        }
        // fridayOpen
        // fridayClose

        if ($fridayOpen = $request->get('fridayOpen')) {
            $day = new OpenDay();
            $day->day = "Friday";
            $day->open = $fridayOpen;
            $day->close = $request->get('fridayClose');
            $day->center()->associate($center->id);
            $day->save();
        }


        $owner = new Admin();
        $owner->first_name = $request->ownerName;
        $owner->last_name = $request->ownerName;
        $owner->email = $request->ownerMail;
        $owner->password = bcrypt($request->password);
        $owner->status = 1;
        $owner->center_position = 'owner';
        $owner->is_admin = 0;
        $owner->center()->associate($center->id);
        $owner->save();
        $owner->assignRole('Center');
    }

    public function update(Request $request, Center $center)
    {
        $center->update(populateModelData($request, Center::class));
        $images_obj = $center->images;
        if ($request->hasFile('logo')) {
            // if there is an old image delete it
            if ($request->hasFile('logo')) {
                // if there is an old logo delete it
                if ($images_obj->logo != null) {
                    $images_obj->logo = Storage::disk('public')->delete($images_obj->logo);
                }
            }
            // store the new image
            $images_obj->logo = Storage::disk('public')->put('center_logo', $request->file('logo'));

            $images_obj->save();
        }
        $images = $center->images->images;
        if ($request->has('images')) {
            //upload new images
            $photos = uploadMultiImages("images", "centers");
            //check old images and delete
            if ($request->has("old_images")) {
                $old_images = $request->get("old_images");
                foreach ($images as $image) {
                    if (!in_array($image, $old_images)) {
                        Storage::disk('public')->delete($image);
                    }
                }
                $images_obj->images = array_merge($old_images, $photos);
                $images_obj->save();
            } else {
                $images_obj->images = $photos;
                $images_obj->save();
            }
        }

        $new_contacts = json_decode($request->get('contacts'));
        if (count($new_contacts) > 0) {

            foreach ($new_contacts as $contact) {

                $cont = Contact::find($contact->id);

                $cont->value = $contact->value;

                $cont->save();
            }
        }

        if ($request->has('contactDelete'))
            Contact::whereIn('id', $request->get("contactDelete"))->delete();



        $deleteDays = OpenDay::where('center_id', $center->id)->delete();
        // saturdayClose
        if ($saturdayOpen = $request->get('saturdayOpen')) {
            $day = new OpenDay();
            $day->day = "Saturday";
            $day->open = $saturdayOpen;
            $day->close = $request->get('saturdayClose');
            $day->center()->associate($center->id);
            $day->save();
        }
        // sundayClose
        if ($sundayOpen = $request->get('sundayOpen')) {
            $day = new OpenDay();
            $day->day = "Sunday";
            $day->open = $sundayOpen;
            $day->close = $request->get('sundayClose');
            $day->center()->associate($center->id);
            $day->save();
        }
        // mondayClose
        if ($mondayOpen = $request->get('mondayOpen')) {
            $day = new OpenDay();
            $day->day = "Monday";
            $day->open = $mondayOpen;
            $day->close = $request->get('mondayClose');
            $day->center()->associate($center->id);
            $day->save();
        }
        // tuesdayClose
        if ($tuesdayOpen = $request->get('tuesdayOpen')) {
            $day = new OpenDay();
            $day->day = "Tuesday";
            $day->open = $tuesdayOpen;
            $day->close = $request->get('tuesdayClose');
            $day->center()->associate($center->id);
            $day->save();
        }
        // wednesdayClose
        if ($wednesdayOpen = $request->get('wednesdayOpen')) {
            $day = new OpenDay();
            $day->day = "Wednesday";
            $day->open = $wednesdayOpen;
            $day->close = $request->get('wednesdayClose');
            $day->center()->associate($center->id);
            $day->save();
        }
        // thursdayClose
        if ($thursdayOpen = $request->get('thursdayOpen')) {
            $day = new OpenDay();
            $day->day = "Thursday";
            $day->open = $thursdayOpen;
            $day->close = $request->get('thursdayClose');
            $day->center()->associate($center->id);
            $day->save();
        }

        if ($fridayOpen = $request->get('fridayOpen')) {
            $day = new OpenDay();
            $day->day = "Friday";
            $day->open = $fridayOpen;
            $day->close = $request->get('fridayClose');
            $day->center()->associate($center->id);
            $day->save();
        }


        $center->save();
    }

    public function delete(Center $center)
    {


        $center->delete();
    }
    public function getCenters(Request $request)
    {
        $centers = Center::with('images')->with('categories');
        if ($search = $request->get('search')) {
            $centers->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }



        return $centers->latest();
    }

    public  function getCenter($center)
    {
        $centerDetails = Center::with(['images', 'contacts', 'days', 'admins', 'reviews'])->findOrFail($center);
        return $centerDetails;
    }
    public function newMember(Request $request)
    {
        $owner = Admin::with('center')->find(auth('sanctum')->id());
        $center = $owner->center;

        $member = new Admin();
        $member->first_name = $request->first_name;
        $member->last_name = $request->last_name;
        $member->phone_number = $request->phone_number;
        $member->email = $request->email;
        $member->password = bcrypt($request->password);
        $member->status = 1;
        $member->center_position = 'member';
        $member->is_admin = 0;
        $member->center()->associate($center->id);
        $member->save();


        //AVALIABLE MEMBER DAYS
        // saturdayClose
        if ($saturdayStart = $request->get('saturdayStart')) {
            $day = new MemberDay();
            $day->day = "Saturday";
            $day->start = $saturdayStart;
            $day->end = $request->get('saturdayEnd');
            $day->admin()->associate($member->id);

            $day->save();
        }

        // sundayStart
        // sundayClose
        if ($sundayStart = $request->get('sundayStart')) {
            $day = new MemberDay();
            $day->day = "Sunday";
            $day->start = $sundayStart;
            $day->end = $request->get('sundayEnd');
            $day->admin()->associate($member->id);

            $day->save();
        }
        // mondayOpen
        // mondayClose
        if ($mondayStart = $request->get('mondayStart')) {
            $day = new MemberDay();
            $day->day = "Monday";
            $day->start = $mondayStart;
            $day->end = $request->get('mondayEnd');
            $day->admin()->associate($member->id);

            $day->save();
        }
        // tuesdayOpen
        // tuesdayClose
        if ($tuesdayStart = $request->get('tuesdayEnd')) {
            $day = new MemberDay();
            $day->day = "Tuesday";
            $day->start = $tuesdayStart;
            $day->end = $request->get('tuesdayEnd');
            $day->admin()->associate($member->id);

            $day->save();
        }
        // wednesdayOpen
        // wednesdayClose
        if ($wednesdayStart = $request->get('wednesdayStart')) {
            $day = new MemberDay();
            $day->day = "Wednesday";
            $day->start = $wednesdayStart;
            $day->end = $request->get('wednesdayEnd');
            $day->admin()->associate($member->id);

            $day->save();
        }
        // thursdayOpen
        // thursdayClose
        if ($thursdayStart = $request->get('thursdayStart')) {
            $day = new MemberDay();
            $day->day = "Thursday";
            $day->start = $thursdayStart;
            $day->end = $request->get('thursdayEnd');
            $day->admin()->associate($member->id);

            $day->save();
        }
        // fridayOpen
        // fridayClose

        if ($fridayStart = $request->get('fridayStart')) {
            $day = new MemberDay();
            $day->day = "Friday";
            $day->start = $fridayStart;
            $day->end = $request->get('fridayEnd');
            $day->admin()->associate($member->id);
            $day->save();
        }



        return $member;
    }
    public function newContact($request)
    {
        $admin = Admin::with('center')->find(auth('sanctum')->id());
        $center = $admin->center;
        $contact = new Contact();
        $contact->key = $request->key;
        $contact->value = $request->value;
        $contact->center()->associate($center->id);
        $contact->save();
        return $contact;
    }
    public function updateDays($request, Center $center)
    {

        $deleteDays = OpenDay::where('center_id', $center->id)->delete();
        // saturdayClose
        if ($saturdayOpen = $request->get('saturdayOpen')) {
            $day = new OpenDay();
            $day->day = "Saturday";
            $day->open = $saturdayOpen;
            $day->close = $request->get('saturdayClose');
            $day->center()->associate($center->id);
            $day->save();
        }
        // sundayClose
        if ($sundayOpen = $request->get('sundayOpen')) {
            $day = new OpenDay();
            $day->day = "Sunday";
            $day->open = $sundayOpen;
            $day->close = $request->get('sundayClose');
            $day->center()->associate($center->id);
            $day->save();
        }
        // mondayClose
        if ($mondayOpen = $request->get('mondayOpen')) {
            $day = new OpenDay();
            $day->day = "Monday";
            $day->open = $mondayOpen;
            $day->close = $request->get('mondayClose');
            $day->center()->associate($center->id);
            $day->save();
        }
        // tuesdayClose
        if ($tuesdayOpen = $request->get('tuesdayOpen')) {
            $day = new OpenDay();
            $day->day = "Tuesday";
            $day->open = $tuesdayOpen;
            $day->close = $request->get('tuesdayClose');
            $day->center()->associate($center->id);
            $day->save();
        }
        // wednesdayClose
        if ($wednesdayOpen = $request->get('wednesdayOpen')) {
            $day = new OpenDay();
            $day->day = "Wednesday";
            $day->open = $wednesdayOpen;
            $day->close = $request->get('wednesdayClose');
            $day->center()->associate($center->id);
            $day->save();
        }
        // thursdayClose
        if ($thursdayOpen = $request->get('thursdayOpen')) {
            $day = new OpenDay();
            $day->day = "Thursday";
            $day->open = $thursdayOpen;
            $day->close = $request->get('thursdayClose');
            $day->center()->associate($center->id);
            $day->save();
        }

        if ($fridayOpen = $request->get('fridayOpen')) {
            $day = new OpenDay();
            $day->day = "Friday";
            $day->open = $fridayOpen;
            $day->close = $request->get('fridayClose');
            $day->center()->associate($center->id);
            $day->save();
        }
    }
}
