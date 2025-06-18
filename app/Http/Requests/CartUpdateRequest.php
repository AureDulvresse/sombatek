<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CartUpdateRequest extends FormRequest
{
   public function authorize()
   {
      return true;
   }

   public function rules()
   {
      return [
         'product_id' => 'required|exists:products,id',
         'quantity' => 'required|integer|min:1',
         'options' => 'nullable|array',
      ];
   }
}
