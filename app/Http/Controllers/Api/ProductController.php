<?php

namespace App\Http\Controllers\Api;

use App\Entities\Product;
use App\Entities\User;
use App\Http\Controllers\Controller;
use App\Repositories\ProductRepository;
use App\Http\Requests\Product\ProductRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Doctrine\ORM\EntityManagerInterface;

class ProductController extends Controller
{
    private EntityManagerInterface $entityManager;
    private ProductRepository $productRepository;

    public function __construct(ProductRepository $productRepository, EntityManagerInterface $entityManager)
    {
        $this->productRepository = $productRepository;
        $this->entityManager = $entityManager;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $page = max(1, (int) $request->query('page', 1));
        $limit = max(1, (int) $request->query('limit', 10));
    
        $products = $this->productRepository->findPaginated($page, $limit);
    
        if (empty($products)) {
            return response()->json(['message' => 'Nenhum produto encontrado', 'page' => $page, 'limit' => $limit], 200);
        }
    
        return response()->json($products);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request)
    {
        $validated = $request->validated();
        
        $userId = Auth::id();
        $user = $this->entityManager->getReference(User::class, $userId);

        $product = new Product(
            $validated['name'],
            $validated['description'],
            $validated['price'],
            $validated['category'],
            $validated['quantity'],
            $user
        );
        
        $this->productRepository->save($product);
    
        return response()->json($product, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = $this->productRepository->findById($id);

        if(!product){
            return response()->json(['message' => 'Produto não encontrado'], 404);
        }

        return response()->json($product, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $product = $this->productRepository->findById($id);

        if(!$product){
            return response()->json(['message' => 'Produto não encontrado'], 404);
        }

        $validated = $request->validate([
            'name' => 'string|nullable',
            'description' => 'string|nullable',
            'price' => 'numeric|nullable',
            'category' => 'string|nullable',
            'quantity' => 'integer|nullable',
        ]);

        $product->updateWithValidatedData($validated);
        $this->productRepository->save($product);
        
        return response()->json($product, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = $this->productRepository->findById($id);

        if (!$product) {
            return response()->json(['message' => 'Produto não encontrado.'], 404);
        }

        $this->productRepository->delete($product);

        return response()->json(['message' => 'Produto deletado com sucesso.'], 200);
    }
}
