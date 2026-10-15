<?php

namespace App\Livewire\Studio\Team;

use App\Enums\Role;
use App\Models\OrganizationUser;
use App\Support\AssignableTeamRoles;
use App\Support\Tenant;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.studio')]
#[Title('Edit team member')]
class Edit extends Component
{
    public OrganizationUser $member;

    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public string $whatsapp = '';

    public string $password = '';

    public string $password_confirmation = '';

    public string $role = 'photographer';

    public bool $is_active = true;

    public function mount(OrganizationUser $member): void
    {
        $this->authorize('update', $member);
        abort_if($member->is_owner, 403);

        $actor = auth()->user();
        if ($member->role === Role::StudioAdmin && ! AssignableTeamRoles::canAssign($actor, Role::StudioAdmin)) {
            abort(403, 'Only Studio Admin can edit another Studio Admin.');
        }

        $this->member = $member->load('user');
        $this->name = $member->user?->name ?? '';
        $this->email = $member->user?->email ?? '';
        $this->phone = $member->user?->phone ?? '';
        $this->whatsapp = $member->user?->whatsapp ?? '';
        $this->role = $member->role->value;
        $this->is_active = (bool) ($member->user?->is_active ?? true);
    }

    public function save()
    {
        $this->authorize('update', $this->member);

        $rules = [
            'name' => ['required', 'string', 'max:120'],
            'email' => [
                'required',
                'email',
                'max:120',
                Rule::unique('users', 'email')->ignore($this->member->user_id),
            ],
            'phone' => ['nullable', 'string', 'max:30'],
            'whatsapp' => ['nullable', 'string', 'max:30'],
            'role' => ['required', Rule::enum(Role::class)],
            'is_active' => ['boolean'],
        ];

        if (trim($this->password) !== '') {
            $rules['password'] = ['required', 'confirmed', 'min:10'];
        }

        $data = $this->validate($rules);

        $role = Role::from($data['role']);
        if ($role === Role::StudioAdmin || $role === Role::Client) {
            $this->addError('role', 'Choose a staff role for this member.');

            return;
        }

        $userPayload = [
            'name' => $data['name'],
            'email' => strtolower(trim($data['email'])),
            'phone' => trim($data['phone']) ?: null,
            'whatsapp' => trim($data['whatsapp']) ?: null,
            'is_active' => (bool) $data['is_active'],
        ];

        if (trim($this->password) !== '') {
            $userPayload['password'] = $this->password;
        }

        $this->member->user?->update($userPayload);
        $this->member->update(['role' => $role]);

        session()->flash('status', 'Team member updated.');

        return $this->redirect(route('app.team.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.studio.team.edit', [
            'roles' => array_values(array_filter(
                Role::cases(),
                fn (Role $role) => $role->isStaff() && $role !== Role::StudioAdmin,
            )),
        ]);
    }
}
