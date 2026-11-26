<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $productQuery = Product::query();

            if (isset($request->sort_field) && isset($request->sort_direction)) {
                $productQuery
                    ->orderBy($request->sort_field, $request->sort_direction)
                    ->orderBy('id', $request->sort_direction);
            }

            $products = $productQuery->paginate(
                perPage: $request->pagintaion_size ?? 10,
                page: $request->page ?? 1,
            );

            return response()->json($products);
        } catch (\Throwable $th) {
            report($th);

            return $this->problemResponse(
                title: 'Внутренняя ошибка сервера',
                detail: 'Не удалось получить список продуктов.',
                status: 500,
            );
        }
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        try {
            $product = DB::transaction(
                fn () => Product::create($request->validated())
            );

            return response()->json([
                'data' => $product,
            ], 201);
        } catch (\Throwable $th) {
            report($th);

            return $this->problemResponse(
                title: 'Внутренняя ошибка сервера',
                detail: 'Не удалось создать продукт.',
                status: 500,
            );
        }
    }

    public function update(Product $product, UpdateProductRequest $request): JsonResponse
    {
        try {
            $product = DB::transaction(function () use ($product, $request) {
                $product = Product::query()
                    ->lockForUpdate()
                    ->findOrFail($product->getKey());

                $product->update($request->validated());

                return $product;
            });

            return response()->json([
                'data' => $product,
            ]);
        } catch (ModelNotFoundException) {
            return $this->problemResponse(
                title: 'Ресурс не найден',
                detail: 'Продукт не существует.',
                status: 404,
            );
        } catch (\Throwable $th) {
            report($th);

            return $this->problemResponse(
                title: 'Внутренняя ошибка сервера',
                detail: 'Не удалось обновить продукт.',
                status: 500,
            );
        }
    }

    public function destroy(Product $product){
        try {
            DB::transaction(function () use ($product) {
                $product->delete();
            });

            return response()->noContent();
        } catch (\Throwable $th) {
            report($th);

            return $this->problemResponse(
                title: 'Внутренняя ошибка сервера',
                detail: 'Не удалось удалить продукт.',
                status: 500,
            );
        }
    }

    private function problemResponse(string $title,string $detail,int $status,): JsonResponse {
        $body = [
            'type' => 'about:blank',
            'title' => $title,
            'status' => $status,
            'detail' => $detail,
            'instance' => request()->getRequestUri(),
        ];

        return response()->json($body, $status, [
            'Content-Type' => 'application/problem+json',
        ]);
    }
}
