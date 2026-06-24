<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        $products = Product::with('category')
                    ->when($search, function($query) use ($search){
                        $query->where(
                            'nama_produk',
                            'like',
                            "%{$search}%"
                        );
                    })
                    ->latest()
                    ->paginate(5);

        return view(
            'products.index',
            compact('products')
        );
    }

    public function create()
    {
        $categories = Category::all();

        return view(
            'products.create',
            compact('categories')
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id'=>'required',
            'nama_produk'=>'required',
            'harga'=>'required|numeric',
            'stok'=>'required|numeric',
            'gambar'=>'required|image'
        ]);

        $gambar = null;

        if($request->hasFile('gambar'))
        {
            $gambar = $request
                    ->file('gambar')
                    ->store('products','public');
        }

        Product::create([
            'category_id'=>$request->category_id,
            'nama_produk'=>$request->nama_produk,
            'harga'=>$request->harga,
            'stok'=>$request->stok,
            'gambar'=>$gambar
        ]);

        return redirect()
            ->route('products.index');
    }

    public function edit(Product $product)
    {
        $categories = Category::all();

        return view(
            'products.edit',
            compact('product','categories')
        );
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'category_id'=>'required',
            'nama_produk'=>'required',
            'harga'=>'required',
            'stok'=>'required'
        ]);

        if($request->hasFile('gambar'))
        {
            $gambar = $request
                ->file('gambar')
                ->store('products','public');

            $product->gambar = $gambar;
        }

        $product->category_id = $request->category_id;
        $product->nama_produk = $request->nama_produk;
        $product->harga = $request->harga;
        $product->stok = $request->stok;

        $product->save();

        return redirect()
            ->route('products.index');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()
            ->route('products.index');
    }
}