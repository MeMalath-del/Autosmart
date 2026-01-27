<?php

namespace App\Livewire\Shop;

use App\Models\Address;
use Livewire\Component;

class AddressManager extends Component
{
    public $addresses = [];

    public ?int $selectedAddressId = null;

    public bool $showForm = false;

    // Form fields
    public string $label = 'المنزل';

    public string $name = '';

    public string $phone = '';

    public string $address = '';

    public string $city = '';

    public string $district = '';

    public string $postal_code = '';

    public ?int $editingId = null;

    protected $rules = [
        'label' => 'required|string|max:50',
        'name' => 'required|string|max:255',
        'phone' => 'required|string|max:20',
        'address' => 'required|string|max:500',
        'city' => 'required|string|max:100',
        'district' => 'nullable|string|max:100',
        'postal_code' => 'nullable|string|max:20',
    ];

    public function mount()
    {
        $this->loadAddresses();
        $default = Address::getDefaultForUser(auth()->id());
        $this->selectedAddressId = $default?->id;
    }

    public function loadAddresses()
    {
        $this->addresses = Address::where('user_id', auth()->id())->get();
    }

    public function selectAddress(int $id)
    {
        $this->selectedAddressId = $id;
        $this->dispatch('addressSelected', addressId: $id);
    }

    public function showAddForm()
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function editAddress(int $id)
    {
        $address = Address::findOrFail($id);
        $this->editingId = $id;
        $this->label = $address->label;
        $this->name = $address->name;
        $this->phone = $address->phone;
        $this->address = $address->address;
        $this->city = $address->city;
        $this->district = $address->district ?? '';
        $this->postal_code = $address->postal_code ?? '';
        $this->showForm = true;
    }

    public function saveAddress()
    {
        $this->validate();

        $data = [
            'user_id' => auth()->id(),
            'label' => $this->label,
            'name' => $this->name,
            'phone' => $this->phone,
            'address' => $this->address,
            'city' => $this->city,
            'district' => $this->district ?: null,
            'postal_code' => $this->postal_code ?: null,
        ];

        if ($this->editingId) {
            Address::where('id', $this->editingId)->update($data);
        } else {
            $address = Address::create($data);
            if ($this->addresses->isEmpty()) {
                $address->setAsDefault();
            }
            $this->selectedAddressId = $address->id;
        }

        $this->loadAddresses();
        $this->showForm = false;
        $this->resetForm();
        $this->dispatch('notify', ['type' => 'success', 'message' => 'تم حفظ العنوان']);
    }

    public function deleteAddress(int $id)
    {
        Address::where('id', $id)->where('user_id', auth()->id())->delete();
        $this->loadAddresses();

        if ($this->selectedAddressId === $id) {
            $this->selectedAddressId = $this->addresses->first()?->id;
        }
    }

    public function setDefault(int $id)
    {
        $address = Address::findOrFail($id);
        $address->setAsDefault();
        $this->loadAddresses();
    }

    public function resetForm()
    {
        $this->editingId = null;
        $this->label = 'المنزل';
        $this->name = auth()->user()->name ?? '';
        $this->phone = auth()->user()->phone ?? '';
        $this->address = '';
        $this->city = '';
        $this->district = '';
        $this->postal_code = '';
    }

    public function cancel()
    {
        $this->showForm = false;
        $this->resetForm();
    }

    public function render()
    {
        return view('livewire.shop.address-manager');
    }
}
