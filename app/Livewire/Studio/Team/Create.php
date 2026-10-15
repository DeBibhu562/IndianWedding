<?php

namespace App\Livewire\Studio\Team;

use App\Enums\Role;
use App\Models\OrganizationUser;
use App\Models\User;
use App\Support\RoleCatalog;
use App\Support\Tenant;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.studio')]
#[Title('Add team member')]
class Create extends Component
{
    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public string $whatsapp = '';

    public string $password = '';

    public string $password_confirmation = '';

    public string $role = 'photographer';

    public bool $is_active = true;

    public function save()
    {
        $this->authorize('create', OrganizationUser::class);

        $email = strtolower(trim($this->email));
        $existingUser = User::query()->where('email', $email)->first();

        $emailRules = ['required', 'email', 'max:120'];
        if (! $existingUser) {
            $emailRules[] = Rule::unique('users', 'email');
        }

        $rules = [
            'name' => ['required', 'string', 'max:120'],
            'email' => $emailRules,
            'phone' => ['nullable', 'string', 'max:30'],
            'whatsapp' => ['nullable', 'string', 'max:30'],
            'role' => ['required', 'string', 'max:120'],
            'is_active' => ['boolean'],
            'password' => $existingUser
                ? ['nullable', 'confirmed', 'min:10']
                : ['required', 'confirmed', 'min:10'],
        ];

        $data = $this->validate($rules);
        $data['email'] = $email;

        $roleValue = $data['role'];
        if ($roleValue === Role::Client->value || ! RoleCatalog::canAssign(auth()->user(), $roleValue)) {
            $this->addError('role', 'You cannot assign this role.');

            return;
        }

        $organizationId = Tenant::requireId();

        if ($existingUser) {
            $alreadyMember = OrganizationUser::query()
                ->where('organization_id', $organizationId)
                ->where('user_id', $existingUser->id)
                ->exists();

            if ($alreadyMember) {
                $this->addError('email', 'This person is already on your team. Edit their profile from the team list.');

                return;
            }
        }

        $userAttributes = [
            'name' => $data['name'],
            'phone' => trim($data['phone']) ?: null,
            'whatsapp' => trim($data['whatsapp']) ?: null,
            'is_active' => (bool) $data['is_active'],
        ];

        if ($existingUser) {
            if (trim($this->password) !== '') {
                $userAttributes['password'] = $this->password;
            }
            $existingUser->update($userAttributes);
            $user = $existingUser;
        } else {
            $user = User::query()->create([
                ...$userAttributes,
                'email' => $data['email'],
                'password' => $this->password,
            ]);
        }

        OrganizationUser::query()->updateOrCreate(
            [
                'organization_id' => $organizationId,
                'user_id' => $user->id,
            ],
            [
                'role' => $roleValue,
                'invited_at' => now(),
                'accepted_at' => now(),
            ],
        );

        session()->flash('status', 'Team member added. Share their login email and password securely.');

        return $this->redirect(route('app.team.index'), navigate: true);
    }

    public function render()
    {
        $this->authorize('create', OrganizationUser::class);

        return view('livewire.studio.team.create', [
            'roleOptions' => RoleCatalog::optionsForActor(),
        ]);
    }
}
