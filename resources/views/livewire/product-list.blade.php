<?php

use App\Models\Product;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
   use WithPagination;

   public $search = '';
   public $showForm = false;
   public $editingId = null;
   public $name = '';
   public $description = '';
   public $price = '';
   public $quantity = '';

   public function with(): array
   {
      return [
         'products' => Product::where('name', 'like', '%' . $this->search . '%')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
      ];
   }

   public function updatedSearch()
   {
      $this->resetPage();
   }

   public function create()
   {
      $this->resetForm();
      $this->showForm = true;
      $this->editingId = null;
   }

   public function edit($id)
   {
      $product = Product::find($id);
      $this->name = $product->name;
      $this->description = $product->description;
      $this->price = $product->price;
      $this->quantity = $product->quantity;
      $this->editingId = $id;
      $this->showForm = true;
   }

   public function save()
   {
      $this->validate([
         'name' => 'required|min:3',
         'description' => 'nullable|string',
         'price' => 'required|numeric|min:0',
         'quantity' => 'required|integer|min:0'
      ]);

      if ($this->editingId) {
         Product::find($this->editingId)->update([
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'quantity' => $this->quantity
         ]);
         session()->flash('message', 'Product updated successfully!');
      } else {
         Product::create([
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'quantity' => $this->quantity
         ]);
         session()->flash('message', 'Product created successfully!');
      }

      $this->resetForm();
      $this->showForm = false;
      $this->resetPage();
   }

   public function delete($id)
   {
      Product::find($id)->delete();
      session()->flash('message', 'Product deleted successfully!');
   }

   public function cancelEdit()
   {
      $this->resetForm();
      $this->showForm = false;
   }

   public function resetForm()
   {
      $this->name = '';
      $this->description = '';
      $this->price = '';
      $this->quantity = '';
      $this->editingId = null;
   }
}; ?>

<div class="max-w-6xl mx-auto p-6">
   <div class="bg-white shadow rounded-lg">
      <!-- Header -->
      <div class="p-6 border-b">
         <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-900">All Products</h1>
            <button wire:click="create" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
               Add Products
            </button>
         </div>
      </div>

      <!-- Flash Message -->
      @if (session()->has('message'))
         <div class="p-4 m-4 bg-green-100 border border-green-400 text-green-700 rounded">
            {{ session('message') }}
         </div>
      @endif

      <!-- Search -->
      <div class="p-6 border-b">
         <input type="text" wire:model.live="search" placeholder="Search products..."
            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
      </div>

      <!-- Form Modal -->
      @if ($showForm)
         <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
               <div class="mt-3">
                  <h3 class="text-lg font-medium text-gray-900 mb-4">
                     {{ $editingId ? 'Edit Product' : 'Add New Product' }}
                  </h3>

                  <form wire:submit="save">
                     <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Name</label>
                        <input type="text" wire:model="name"
                           class="mt-1 block w-full px-3 py-2 border rounded-md focus:ring-blue-500 focus:border-blue-500">
                        @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                     </div>

                     <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea wire:model="description" rows="3"
                           class="mt-1 block w-full px-3 py-2 border rounded-md focus:ring-blue-500 focus:border-blue-500"></textarea>
                        @error('description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                     </div>

                     <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Price</label>
                        <input type="number" step="0.01" wire:model="price"
                           class="mt-1 block w-full px-3 py-2 border rounded-md focus:ring-blue-500 focus:border-blue-500">
                        @error('price') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                     </div>

                     <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Quantity</label>
                        <input type="number" wire:model="quantity"
                           class="mt-1 block w-full px-3 py-2 border rounded-md focus:ring-blue-500 focus:border-blue-500">
                        @error('quantity') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                     </div>

                     <div class="flex justify-end space-x-2">
                        <button type="button" wire:click="cancelEdit"
                           class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                           Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                           {{ $editingId ? 'Update' : 'Save' }}
                        </button>
                     </div>
                  </form>
               </div>
            </div>
         </div>
      @endif

      <!-- Products Table -->
      <div class="overflow-x-auto">
         <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
               <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions
                  </th>
               </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
               @forelse ($products as $product)
                  <tr>
                     <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ $product->name }}
                     </td>
                     <td class="px-6 py-4 text-sm text-gray-500">
                        {{ Str::limit($product->description, 50) }}
                     </td>
                     <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        ${{ number_format($product->price, 2) }}
                     </td>
                     <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $product->quantity }}
                     </td>
                     <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <button wire:click="edit({{ $product->id }})" class="text-blue-600 hover:text-blue-900 mr-3">
                           Edit
                        </button>
                        <button wire:click="delete({{ $product->id }})"
                           wire:confirm="Are you sure you want to delete this product?"
                           class="text-red-600 hover:text-red-900">
                           Delete
                        </button>
                     </td>
                  </tr>
               @empty
                  <tr>
                     <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                        No products found.
                     </td>
                  </tr>
               @endforelse
            </tbody>
         </table>
      </div>

      <!-- Pagination -->
      <div class="p-6 border-t">
         {{ $products->links() }}
      </div>
   </div>
</div>