<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

use App\Models\Center;
use Maatwebsite\Excel\Concerns\FromCollection;

class CentersExport implements FromCollection, WithMapping, WithHeadings
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
        $centers = Center::query();
        $searchCenter=$this->search;
        if ($this->search) {

            $centers->where(function ($query) use ($searchCenter) {
                $query->where('name', 'like', '%' . $searchCenter . '%')
                    ->orWhere('currency', 'like', '%' . $searchCenter . '%')
                    ->orWhere('email', 'like', '%' . $searchCenter . '%');
            });
        }

          return  $centers->latest()->get();

    }

    public function map($centers): array
    {
        $data =
            [
                $centers->name,//2
                $centers->currency,//3
                $centers->email,//4
                $centers->address,//5
                $centers->phone_number,//6
            ];
        return $data;
    }

    public function headings(): array
    {
        $titles = [
            'name',//1
            'currency',//2
            'Email',//3
            'address',//4
            'phone number',//5
        ];


        return $titles;
    }
}
