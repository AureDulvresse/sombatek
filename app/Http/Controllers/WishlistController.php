<?php

namespace App\Http\Controllers;

use App\Http\Requests\WishlistUpdateRequest;
use App\Models\Wishlist;
use App\Models\Product;
use App\Models\WishlistItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;

class WishlistController extends Controller
{
    public function index()
    {
        $wishlists = Auth::user()->wishlists()
            ->with(['items.product'])
            ->get();

        return Inertia::render('Wishlist/Index', [
            'wishlists' => $wishlists->map(function ($wishlist) {
                return [
                    'id' => $wishlist->id,
                    'name' => $wishlist->name,
                    'is_public' => $wishlist->is_public,
                    'description' => $wishlist->description,
                    'max_items' => $wishlist->max_items,
                    'items_count' => $wishlist->getItemCount(),
                    'items' => $wishlist->items->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'product_id' => $item->product_id,
                            'name' => $item->name,
                            'price' => $item->price,
                            'default_image' => $item->default_image,
                            'options' => $item->options,
                            'product' => [
                                'stock' => $item->product->stock,
                                'is_active' => $item->product->is_active,
                            ]
                        ];
                    })
                ];
            })
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'is_public' => 'boolean',
                'max_items' => 'nullable|integer|min:1'
            ]);

            $wishlist = Auth::user()->wishlists()->create($data);

            return response()->json([
                'success' => true,
                'message' => 'Liste de souhaits créée',
                'wishlist' => $wishlist
            ]);
        } catch (\Exception $e) {
            return $this->getErrorResponse($e);
        }
    }

    public function update(Request $request, Wishlist $wishlist): JsonResponse
    {
        try {
            $this->authorize('update', $wishlist);

            $data = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'is_public' => 'boolean',
                'max_items' => 'nullable|integer|min:1'
            ]);

            $wishlist->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Liste de souhaits mise à jour',
                'wishlist' => $wishlist
            ]);
        } catch (\Exception $e) {
            return $this->getErrorResponse($e);
        }
    }

    public function destroy(Wishlist $wishlist): JsonResponse
    {
        try {
            $this->authorize('delete', $wishlist);
            $wishlist->delete();

            return response()->json([
                'success' => true,
                'message' => 'Liste de souhaits supprimée'
            ]);
        } catch (\Exception $e) {
            return $this->getErrorResponse($e);
        }
    }

    public function toggleItem(Request $request): JsonResponse
    {
        try {
            $data = $request->validate([
                'product_id' => 'required|exists:products,id'
            ]);

            $product = Product::findOrFail($data['product_id']);

            if (!$product->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ce produit n\'est plus disponible.'
                ], 422);
            }

            $wishlist = Auth::user()->wishlists()->firstOrCreate(
                ['name' => 'Favoris'],
                ['is_public' => false]
            );

            if ($wishlist->isFull()) {
                return response()->json([
                    'success' => false,
                    'message' => 'La liste de souhaits est pleine.'
                ], 422);
            }

            $existingItem = $wishlist->items()->where('product_id', $product->id)->first();

            if ($existingItem) {
                $wishlist->removeItem($product->id);
                $isInWishlist = false;
            } else {
                $wishlist->addItem($product);
                $isInWishlist = true;
            }

            return response()->json([
                'success' => true,
                'message' => $isInWishlist ? 'Produit ajouté aux favoris' : 'Produit retiré des favoris',
                'isInWishlist' => $isInWishlist,
                'wishlist' => [
                    'items_count' => $wishlist->getItemCount()
                ]
            ]);
        } catch (\Exception $e) {
            return $this->getErrorResponse($e);
        }
    }

    public function removeItem(Request $request): JsonResponse
    {
        try {
            $request->validate(['product_id' => 'required|exists:products,id']);
            $wishlist = Auth::user()->wishlists()->first();

            if (!$wishlist) {
                return response()->json([
                    'success' => false,
                    'message' => 'Liste non trouvée'
                ], 404);
            }

            $removed = $wishlist->removeItem($request->product_id);

            return response()->json([
                'success' => $removed,
                'message' => $removed ? 'Produit retiré des favoris' : 'Erreur lors de la suppression',
                'wishlist' => [
                    'items_count' => $wishlist->getItemCount()
                ]
            ]);
        } catch (\Exception $e) {
            return $this->getErrorResponse($e);
        }
    }

    public function clear(): JsonResponse
    {
        try {
            $wishlist = Auth::user()->wishlists()->first();
            if ($wishlist) {
                $wishlist->clear();
            }

            return response()->json([
                'success' => true,
                'message' => 'Liste de souhaits vidée'
            ]);
        } catch (\Exception $e) {
            return $this->getErrorResponse($e);
        }
    }

    public function moveToCart(Wishlist $wishlist): JsonResponse
    {
        try {
            $this->authorize('update', $wishlist);
            $cart = Auth::user()->cart ?? Auth::user()->cart()->create();

            $wishlist->moveToCart($cart);

            return response()->json([
                'success' => true,
                'message' => 'Produits ajoutés au panier',
                'cart' => [
                    'items_count' => $cart->getItemCount()
                ]
            ]);
        } catch (\Exception $e) {
            return $this->getErrorResponse($e);
        }
    }

    public function duplicate(Wishlist $wishlist): JsonResponse
    {
        try {
            $this->authorize('update', $wishlist);
            $duplicate = $wishlist->duplicate();

            return response()->json([
                'success' => true,
                'message' => 'Liste de souhaits dupliquée',
                'wishlist' => $duplicate
            ]);
        } catch (\Exception $e) {
            return $this->getErrorResponse($e);
        }
    }

    public function share(Wishlist $wishlist): JsonResponse
    {
        try {
            $this->authorize('update', $wishlist);
            $shareUrl = $wishlist->share();

            if (!$shareUrl) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cette liste ne peut pas être partagée'
                ], 422);
            }

            return response()->json([
                'success' => true,
                'message' => 'Lien de partage généré',
                'share_url' => $shareUrl
            ]);
        } catch (\Exception $e) {
            return $this->getErrorResponse($e);
        }
    }

    public function checkItem(Request $request): JsonResponse
    {
        try {
            $request->validate(['product_id' => 'required|exists:products,id']);
            $wishlist = Auth::user()->wishlists()->first();

            if (!$wishlist) {
                return response()->json([
                    'success' => true,
                    'isInWishlist' => false
                ]);
            }

            $isInWishlist = $wishlist->items()->where('product_id', $request->product_id)->exists();

            return response()->json([
                'success' => true,
                'isInWishlist' => $isInWishlist
            ]);
        } catch (\Exception $e) {
            return $this->getErrorResponse($e);
        }
    }

    public function get(): JsonResponse
    {
        try {
            Log::info('Début de la méthode Wishlist::get');

            $wishlist = Auth::user()->wishlists()
                ->with(['items' => function ($query) {
                    $query->with('product:id,name,stock,is_active');
                }])
                ->first();

            Log::info('Wishlist récupérée', ['wishlist_id' => $wishlist?->id]);

            if (!$wishlist) {
                Log::info('Aucune wishlist trouvée, retour d\'une wishlist vide');
                return response()->json([
                    'success' => true,
                    'wishlist' => [
                        'items' => [],
                        'items_count' => 0,
                        'total' => 0
                    ]
                ]);
            }

            $response = [
                'success' => true,
                'wishlist' => [
                    'items' => $wishlist->items->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'product_id' => $item->product_id,
                            'name' => $item->name,
                            'price' => $item->price,
                            'default_image' => $item->default_image,
                            'options' => $item->options ?? [],
                            'product' => $item->product ? [
                                'stock' => $item->product->stock,
                                'is_active' => $item->product->is_active,
                            ] : null
                        ];
                    }),
                    'items_count' => $wishlist->getItemCount(),
                    'total' => $wishlist->items->sum('price')
                ]
            ];

            Log::info('Réponse préparée', ['items_count' => count($response['wishlist']['items'])]);
            return response()->json($response);
        } catch (\Exception $e) {
            Log::error('Erreur Wishlist::get: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->getErrorResponse($e);
        }
    }

    protected function getErrorResponse(\Exception $e): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Une erreur est survenue : ' . $e->getMessage()
        ], 500);
    }
}
