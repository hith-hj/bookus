<?php

namespace App\Exports;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;

class UsersExport implements FromCollection, WithMapping, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    private $search=null;
    public function __construct($search=null){
        $this->search=$search;
    }
    public function collection()
    {
        $users = User::query();
        $searchUser=$this->search;
        if ($this->search) {

            $users->where(function ($query) use ($searchUser) {
                $query->where('first_name', 'like', '%' . $searchUser . '%')
                    ->orWhere('last_name', 'like', '%' . $searchUser . '%')
                    ->orWhere('email', 'like', '%' . $searchUser . '%');
            });
        }

          return  $users->latest()->get();

    }

    public function map($users): array
    {
        $data =
            [
                $users->first_name,//2
                $users->last_name,//3
                $users->email,//4
                $users->address,//5
                $users->phone_number,//6
            ];
        return $data;
    }

    public function headings(): array
    {
        $titles = [
            'first name',//1
            'last name',//2
            'Email',//3
            'address',//4
            'phone number',//5
        ];


        return $titles;
    }
}
