<?php

namespace App\Exports;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

use App\Models\Admin;
use Maatwebsite\Excel\Concerns\FromCollection;

class AdminsExport implements FromCollection, WithMapping, WithHeadings
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
        $admins = Admin::with('roles');
       $serchAdmin=$this->search;
        if ($this->search) {

            $admins->where(function ($query) use ($serchAdmin) {
                $query->where('first_name', 'like', '%' . $serchAdmin . '%')
                    ->orWhere('last_name', 'like', '%' . $serchAdmin . '%')
                    ->orWhere('email', 'like', '%' . $serchAdmin . '%');
            });
        }

          $admins1=  $admins->latest()->get();

            return $admins1;
    }

    public function map($admins): array
    {
        $data =
            [
                $admins->first_name,//2
                $admins->last_name,//3
                $admins->email,//4
                $admins->address,//5
                $admins->phone_number,//6
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
