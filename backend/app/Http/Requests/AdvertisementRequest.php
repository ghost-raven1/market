<?php

namespace App\Http\Requests;

/**
 * @OA\Schema(
 *     schema="AdvertisementRequest",
 *     title="Advertisement Request",
 *     description="Request body for creating/updating an advertisement",
 *     required={"title", "description", "price", "category_id", "location", "condition"},
 *     @OA\Property(property="title", type="string", maxLength=255),
 *     @OA\Property(property="description", type="string"),
 *     @OA\Property(property="price", type="number", format="float", minimum=0),
 *     @OA\Property(property="category_id", type="integer", format="int64"),
 *     @OA\Property(
 *         property="images",
 *         type="array",
 *         @OA\Items(type="string", format="binary"),
 *         maxItems=5
 *     ),
 *     @OA\Property(property="location", type="string", maxLength=255),
 *     @OA\Property(property="condition", type="string", enum={"new", "used"})
 * )
 */
class AdvertisementRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:2048',
            'images' => 'array|max:5',
            'location' => 'required|string|max:255',
            'condition' => 'required|in:new,used',
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'Название объявления обязательно',
            'title.max' => 'Название не должно превышать 255 символов',
            'description.required' => 'Описание объявления обязательно',
            'price.required' => 'Цена обязательна',
            'price.numeric' => 'Цена должна быть числом',
            'price.min' => 'Цена не может быть отрицательной',
            'category_id.required' => 'Категория обязательна',
            'category_id.exists' => 'Выбранная категория не существует',
            'images.*.image' => 'Файл должен быть изображением',
            'images.*.mimes' => 'Изображение должно быть в формате jpeg, png или jpg',
            'images.*.max' => 'Размер изображения не должен превышать 2MB',
            'images.max' => 'Максимальное количество изображений - 5',
            'location.required' => 'Местоположение обязательно',
            'location.max' => 'Местоположение не должно превышать 255 символов',
            'condition.required' => 'Состояние товара обязательно',
            'condition.in' => 'Недопустимое значение состояния товара',
        ];
    }
} 