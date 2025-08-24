<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Afficher la liste des produits
     */
    public function index()
    {
        $products = Product::all();
        return Inertia::render('products/index', [
            'products' => $products
        ]);
    }
    
    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        return Inertia::render('products/create', []);
    }
    
    /**
     * Stocker un nouveau produit
     */
    public function store(Request $request)
    {
        // Validation des données
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'quantite' => 'required|integer|min:0',
        ], [
            'name.required' => 'Le nom du produit est requis',
            'name.max' => 'Le nom ne peut pas dépasser 255 caractères',
            'description.required' => 'La description est requise',
            'description.max' => 'La description ne peut pas dépasser 1000 caractères',
            'quantite.required' => 'La quantité est requise',
            'quantite.integer' => 'La quantité doit être un nombre entier',
            'quantite.min' => 'La quantité ne peut pas être négative',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            // Création du produit en base de données
            $product = Product::create([
                'name' => $request->name,
                'description' => $request->description,
                'quantite' => $request->quantite,
            ]);

            return redirect()->route('products.index')
                ->with('success', 'Produit créé avec succès !');
                
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors de la création du produit: ' . $e->getMessage()])->withInput();
        }
    }
}
