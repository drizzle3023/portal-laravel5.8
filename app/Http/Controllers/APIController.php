<?php


namespace App\Http\Controllers;

use App\Http\Models\Domain;
use App\Http\Models\Customer;
use App\Http\Models\Product;
use App\Http\Utils\Utils;
use Illuminate\Support\Facades\Hash;
use Validator;


class APIController
{
    public function doLogin()
    {
        $validation = Validator::make(request()->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validation->fails()) {
            return Utils::makeResponse([], config('constants.response-message.invalid-params'));
        }

        $credentials = request(['email', 'password']);

        $user = Customer::where([
            ['email', $credentials['email']],
            ['enable_flag', 1],
        ])->first();

        if ($user == null) {
            return Utils::makeResponse([], config('constants.response-message.invalid-credentials'));
        }

        if (!Hash::check($credentials['password'], $user->password)) {
            return Utils::makeResponse([], config('constants.response-message.invalid-credentials'));
        }

        if (!$token = auth()->attempt($credentials)) {
            return Utils::makeResponse([], config('constants.response-message.error-generate-api-token'));
        }

        $user = $user->setHidden([
            'password'
        ]);

        return Utils::makeResponse([
            'api_token' => $token,
            'user' => $user,
        ]);
    }

    public function getCategoriesByClient()
    {
        $email = request('email');
        $client = Customer::where('email', $email)->first();

        if (!isset($client)) {
            return Utils::makeResponse([], config('constants.response-message.invalid-params'));
        }

        $categories = Domain::where([
            ['customer_id', $client->id],
            ['show_flag', 1]
        ])->get();

        return Utils::makeResponse([
            'category_array' => $categories
        ]);

    }

    public function getProductsByCategory()
    {
        $category_id = request('category_id');
        $title = request('title');
        $search_keyword = request('search_keyword');

        $where_clause = [];
        if ($search_keyword != "" && isset($search_keyword)) {
            $where_clause[] = ['name', 'like', "%$search_keyword%"];
            $where_clause[] = ['name_second', 'like', "%$search_keyword%"];
            $where_clause[] = ['description', 'like', "%$search_keyword%"];
            $where_clause[] = ['description_second', 'like', "%$search_keyword%"];
        }

        if (!isset($category_id)) {
            return Utils::makeResponse([], config('constants.response-message.invalid-params'));
        }

        $products = Product::where([
            ['category_id', $category_id],
            ['show_flag', 1]
        ])->where(function ($query) use ($where_clause) {
            if (count($where_clause ) > 0) {
                $query->where([$where_clause[0]]);
                for ($i = 1; $i < count($where_clause); $i++) {
                    $query->orwhere([$where_clause[$i]]);
                }
            }
        })->with('category', 'currency')->get();

        return Utils::makeResponse([
            'product_array' => $products
        ]);

    }

    public function getProductDetail()
    {
        $product_id = request('product_id');
        if (!isset($product_id)) {
            return Utils::makeResponse([], config('constants.response-message.invalid-params'));
        }

        $product = Product::where([
            ['id', $product_id],
            ['show_flag', 1]
        ])->with('category', 'currency')->first();

        if ($product == null) {
            return Utils::makeResponse([], config('constants.response-message.invalid-params'));
        }

        return Utils::makeResponse([
            'product' => $product
        ]);

    }

    public function getClientDetail()
    {
        $client_email = request('email');
        if (!isset($client_email)) {
            return Utils::makeResponse([], config('constants.response-message.invalid-params'));
        }

        $client = Customer::where('email', $client_email)->first();

        if ($client == null) {
            return Utils::makeResponse([], config('constants.response-message.invalid-params'));
        }

        $client = $client->setHidden([
            'password'
        ]);

        return Utils::makeResponse([
            'client' => $client
        ]);

    }

}
