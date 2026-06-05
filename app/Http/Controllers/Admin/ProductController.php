<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductRequest;
use App\Http\Requests\Admin\SizeRequest;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductSize;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        $products = Product::withCount('sizes')
            ->with(['images' => fn ($q) => $q->where('is_primary', true)])
            ->latest()
            ->paginate(20);

        return view('admin.products.index', compact('products'));
    }

    public function create(): View
    {
        return view('admin.products.create');
    }

    public function store(ProductRequest $request): RedirectResponse
    {
        $product = Product::create($request->validated());

        return redirect()->route('admin.products.edit', $product)
            ->with('status', 'Product created. Add sizes and images below.');
    }

    public function edit(Product $product): View
    {
        $product->load('sizes', 'images');

        return view('admin.products.edit', compact('product'));
    }

    public function update(ProductRequest $request, Product $product): RedirectResponse
    {
        $product->update($request->validated());

        return back()->with('status', 'Product updated.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        if (OrderItem::where('product_id', $product->id)->exists()) {
            return back()->with('error', 'Cannot delete a product that has been ordered. Deactivate it instead.');
        }

        $product->load('images');
        foreach ($product->images as $image) {
            Storage::disk(config('filesystems.default'))->delete($image->path);
        }
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('status', 'Product deleted.');
    }

    public function toggle(Product $product): RedirectResponse
    {
        $product->update([
            'status' => $product->status === 'active' ? 'inactive' : 'active',
        ]);

        return back()->with('status', 'Product status updated.');
    }

    public function storeSize(SizeRequest $request, Product $product): RedirectResponse
    {
        $product->sizes()->create($request->validated());

        return back()->with('status', 'Size added.');
    }

    public function updateSize(SizeRequest $request, Product $product, ProductSize $size): RedirectResponse
    {
        abort_if($size->product_id !== $product->id, 404);
        $size->update($request->validated());

        return back()->with('status', 'Size updated.');
    }

    public function destroySize(Product $product, ProductSize $size): RedirectResponse
    {
        abort_if($size->product_id !== $product->id, 404);
        $size->delete();

        return back()->with('status', 'Size removed.');
    }

    public function storeImage(Request $request, Product $product): RedirectResponse
    {
        $request->validate([
            'image' => ['required', 'image', 'max:5120'],
        ]);

        $file = $request->file('image');
        $path = Storage::disk(config('filesystems.default'))
            ->putFileAs('products', $file, Str::uuid() . '.' . $file->extension());

        $maxOrder = $product->images()->max('sort_order') ?? 0;
        $isPrimary = $product->images()->count() === 0;

        $product->images()->create([
            'path'       => $path,
            'is_primary' => $isPrimary,
            'sort_order' => $maxOrder + 1,
        ]);

        return back()->with('status', 'Image uploaded.');
    }

    public function setPrimaryImage(Product $product, ProductImage $image): RedirectResponse
    {
        abort_if($image->product_id !== $product->id, 404);

        $product->images()->update(['is_primary' => false]);
        $image->update(['is_primary' => true]);

        return back()->with('status', 'Primary image updated.');
    }

    public function destroyImage(Product $product, ProductImage $image): RedirectResponse
    {
        abort_if($image->product_id !== $product->id, 404);

        Storage::disk(config('filesystems.default'))->delete($image->path);
        $wasPrimary = $image->is_primary;
        $image->delete();

        if ($wasPrimary) {
            $product->images()->oldest('sort_order')->first()?->update(['is_primary' => true]);
        }

        return back()->with('status', 'Image deleted.');
    }
}
