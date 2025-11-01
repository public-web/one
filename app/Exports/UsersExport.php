<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UsersExport implements FromQuery, WithHeadings, WithMapping, WithStyles
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = User::withTrashed()->with('roles');

        // Apply the same filters as in the index method
        if (isset($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if (isset($this->filters['role'])) {
            $query->whereHas('roles', function ($q) {
                $q->where('name', $this->filters['role']);
            });
        }

        if (isset($this->filters['status'])) {
            switch ($this->filters['status']) {
                case 'active':
                    $query->where('active', true)->whereNull('deleted_at');
                    break;
                case 'inactive':
                    $query->where('active', false)->whereNull('deleted_at');
                    break;
                case 'deleted':
                    $query->onlyTrashed();
                    break;
            }
        }

        if (isset($this->filters['expiring'])) {
            if ($this->filters['expiring'] === 'soon') {
                $query->whereBetween('expires_at', [now(), now()->addDays(30)]);
            } elseif ($this->filters['expiring'] === 'expired') {
                $query->where('expires_at', '<', now());
            }
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Email',
            'Role',
            'Active',
            'Requires 2FA',
            'Expires At',
            'Created At',
            'Deleted At',
        ];
    }

    public function map($user): array
    {
        return [
            $user->id,
            $user->name,
            $user->email,
            $user->roles->pluck('name')->join(', '),
            $user->active ? 'Yes' : 'No',
            $user->require_2fa ? 'Yes' : 'No',
            $user->expires_at ? $user->expires_at->format('Y-m-d') : '',
            $user->created_at->format('Y-m-d H:i:s'),
            $user->deleted_at ? $user->deleted_at->format('Y-m-d H:i:s') : '',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold
            1 => ['font' => ['bold' => true]],
        ];
    }
}
