<?php

namespace App\Livewire;

use App\Helpers\CartManagement;
use App\Livewire\Partials\Navbar;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Jantinnerezo\LivewireAlert\LivewireAlert;

#[Title('Products - DCodeMania')]
class ProductsPage extends Component
{
    use WithPagination;
    use LivewireAlert;

    #[Url]
    public $selected_categories = [];

    #[Url]
    public $selected_brands = [];

    #[Url]
    public $featured;

    #[Url]
    public $on_sale;

    #[Url]
    public $price_range = 9999;

    #[Url]
    public $sort = 'latest';

    public $maxPrice;

    public function addToCart($product_id)
    {
        $cartItems = CartManagement::getCartItemsFromCookie();
        
        if(count($cartItems) > 0){
            foreach($cartItems as $key => $item){
                if($item['product_id'] == $product_id){
                    $this->alert('warning', 'This product is already in the cart!', [
                        'position' => 'top-end',
                        'timer' => 3000,
                        'toast' => true
                    ]);
                    return;
                }
            }
        }
        
        $total_count = CartManagement::addItemToCart($product_id);
        $this->dispatch('update-cart-count', total_count: $total_count)->to(Navbar::class);

        $this->alert('success', 'Product added to the cart successfully!', [
            'position' => 'top-end',
            'timer' => 3000,
            'toast' => true
        ]);
    }

    public function render()
    {
        $getMaxPrice = Product::query()
            ->select('price')
            ->orderByDesc('price')
            ->first()
            ->price;

        $this->maxPrice = $getMaxPrice + 1000;

        $productQuery = Product::query()->where('is_active', 1);

        if(!empty($this->selected_categories)){
            $productQuery->whereIn('category_id', $this->selected_categories);
        }

        if(!empty($this->selected_brands)){
            $productQuery->whereIn('brand_id', $this->selected_brands);
        }

        if($this->featured){
            $productQuery->where('is_featured', 1);
        }

        if($this->on_sale){
            $productQuery->where('on_sale', 1);
        }

        if($this->price_range){
            $productQuery->whereBetween('price', [0, $this->price_range]);
        }

        if($this->sort == 'asc'){
            $productQuery->oldest();
        }else if($this->sort == 'price_desc'){
            $productQuery->orderBy('price', 'desc');
        }else if($this->sort == 'price_asc'){
            $productQuery->orderBy('price', 'asc');
        }else {
            $productQuery->latest();
        }

        return view('livewire.products-page', [
            'products' => $productQuery->paginate(9),
            'brands' => Brand::where('is_active', 1)->get(['id', 'name', 'slug']),
            'categories' => Category::where('is_active', 1)->get(['id', 'name', 'slug']),
        ]);
    }
}
