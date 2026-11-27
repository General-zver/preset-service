<?php

namespace App\Http\Controllers;

use App\Http\Requests\PresetsRequest;
use App\Http\Requests\UpdatePresetsRequest;
use App\Models\Preset;
use App\Models\Product;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PresetController extends Controller
{
    public function index(Request $request)
    {

        $data = [];

        try {
            $presetQuery = Preset::query();


            if (isset($request->filters) && count($request->filters) > 0) {
                foreach ($request->filters as $value) {
                    $presetQuery->where($value['field'], $value['value']);
                }
            }

            $presetQuery->withCount('products as products_count')->withSum('products as products_sum', 'price');

            if (isset($request->sort_field) && isset($request->sort_direction)) {
                $presetQuery->orderBy($request->sort_field, $request->sort_direction)
                ->orderBy('id', $request->sort_direction);
            }

            $data = $presetQuery->paginate(
                perPage: $request->pagintaion_size ?? 10,
                page: $request->page ?? 1
            );

        } catch (\Throwable $th) {
            report($th);

            return $this->problemResponse(
                title: 'Внутренняя ошибка сервера',
                detail: 'Не удалось получить список пресетов.',
                status: 500,
            );
        }

        return response()->json($data, 200);
    }

    public function show(Preset $preset): JsonResponse
    {
        try {
            $preset->load([
                'products' => fn ($query) => $query->orderBy('id'),
            ]);

            return response()->json([
                'data' => $preset,
            ]);
        } catch (\Throwable $th) {
            report($th);

            return $this->problemResponse(
                title: 'Внутренняя ошибка сервера',
                detail: 'Не удалось получить пресет.',
                status: 500,
            );
        }
    }

    public function store(PresetsRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $productIds = $validated['product_ids'] ?? [];
            unset($validated['product_ids']);

            $preset = DB::transaction(function () use ($validated, $productIds) {
                $preset = Preset::create($validated);
                $this->replaceProducts($preset, $productIds);

                return $preset->load('products');
            });

            return response()->json([
                'data' => $preset,
            ], 201);
        } catch (ValidationException $exception) {
            return $this->problemResponse(
                title: 'Ошибка валидации',
                detail: 'Переданные данные не прошли проверку.',
                status: 422,
                errors: $exception->errors(),
            );
        } catch (\Throwable $th) {
            report($th);

            return $this->problemResponse(
                title: 'Внутренняя ошибка сервера',
                detail: 'Не удалось создать пресет.',
                status: 500,
            );
        }
    }

    public function update(Preset $preset, UpdatePresetsRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $replaceProducts = array_key_exists('product_ids', $validated);
            $productIds = $validated['product_ids'] ?? [];

            unset($validated['product_ids']);

            $preset = DB::transaction(function () use ($preset,$validated,$replaceProducts,$productIds,) {
                $preset = Preset::query()
                    ->lockForUpdate()
                    ->findOrFail($preset->getKey());

                $preset->update($validated);

                if ($replaceProducts) {
                    $this->replaceProducts($preset, $productIds);
                }

                return $preset->load('products');
            });

            return response()->json([
                'data' => $preset,
            ]);
        } catch (ValidationException $exception) {
            return $this->problemResponse(
                title: 'Ошибка валидации',
                detail: 'Переданные данные не прошли проверку.',
                status: 422,
                errors: $exception->errors(),
            );
        } catch (ModelNotFoundException) {
            return $this->problemResponse(
                title: 'Ресурс не найден',
                detail: 'Пресет не существует.',
                status: 404,
            );
        } catch (\Throwable $th) {
            report($th);

            return $this->problemResponse(
                title: 'Внутренняя ошибка сервера',
                detail: 'Не удалось обновить пресет.',
                status: 500,
            );
        }
    }

    public function destroy(Preset $preset)
    {
        try {
            DB::transaction(function () use ($preset) {
                $preset->delete();
            });

            return response()->noContent();
        } catch (\Throwable $th) {
            report($th);

            return $this->problemResponse(
                title: 'Внутренняя ошибка сервера',
                detail: 'Не удалось удалить пресет.',
                status: 500,
            );
        }
    }

    public function book(int $id)
    {
        try {
            $user = Auth::user();

            $problem = DB::transaction(function () use ($id, $user) {
                $preset = Preset::query()->whereKey($id)->lockForUpdate()->first();

                if (! $preset instanceof Preset) {
                    return $this->problemResponse(
                        title: 'Ресурс не найден',
                        detail: 'Пресет не существует.',
                        status: 404,
                    );
                }

                if ($preset->status) {
                    return $this->problemResponse(
                        title: 'Конфликт',
                        detail: 'Пресет уже забронирован.',
                        status: 409,
                    );
                }

                $preset->update([
                    'status' => true,
                    'user_id' => $user->id,
                ]);

                return null;
            });

            if ($problem !== null) {
                return $problem;
            }

            return response()->json([
                'message' => 'Пресет успешно забронирован',
                'status' => 200,
            ], 200);
        } catch (\Throwable $th) {
            report($th);

            return $this->problemResponse(
                title: 'Внутренняя ошибка сервера',
                detail: 'Не удалось забронировать пресет.',
                status: 500,
            );
        }
    }

    private function replaceProducts(Preset $preset, array $productIds): void
    {
        $query = Product::query()->where('preset_id', $preset->getKey());

        if ($productIds !== []) {
            $query->orWhereIn('id', $productIds);
        }

        $products = $query->orderBy('id')->lockForUpdate()->get();

        $requestedProducts = $products->whereIn('id', $productIds);

        if ($requestedProducts->count() !== count($productIds)) {
            throw ValidationException::withMessages([
                'product_ids' => ['Один или несколько продуктов не существуют.'],
            ]);
        }

        $conflict = $requestedProducts->first(
            fn (Product $product) => $product->preset_id !== null
                && (int) $product->preset_id !== (int) $preset->getKey()
        );

        if ($conflict !== null) {
            throw ValidationException::withMessages([
                'product_ids' => [
                    "Продукт {$conflict->id} уже связан с другим пресетом.",
                ],
            ]);
        }

        Product::query()->where('preset_id', $preset->getKey())->update(['preset_id' => null]);

        if ($productIds !== []) {
            Product::query()->whereKey($productIds)->update(['preset_id' => $preset->getKey()]);
        }
    }

    private function problemResponse(string $title,string $detail,int $status,?array $errors = null,): JsonResponse {
        $body = [
            'type' => 'about:blank',
            'title' => $title,
            'status' => $status,
            'detail' => $detail,
            'instance' => request()->getRequestUri(),
        ];

        if ($errors !== null) {
            $body['errors'] = $errors;
        }

        return response()->json($body, $status, [
            'Content-Type' => 'application/problem+json',
        ]);
    }
}
