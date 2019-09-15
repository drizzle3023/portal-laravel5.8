<?php


namespace App\Http\Controllers;

use App\Http\Models\Admin;
use App\Http\Models\Domain;
use App\Http\Models\Currency;
use App\Http\Models\Customer;
use App\Http\Models\Invoices;
use App\Http\Models\Product;
use App\Http\Models\Employees;
use App\Http\Utils\Utils;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\In;
use Intervention\Image\Facades\Image;
use Barryvdh\DomPDF\Facade as PDF;

class AdminController
{
    public function index()
    {
        $user = session()->get('user');
        $user_type = session()->get('user-type');

        if (isset($user) && isset($user_type)) {
            return redirect('/dashboard');
        } else {
            return view('login');
        }
    }

    public function doLogin()
    {
        $email = request('login-username');
        $password = request('login-password');

        if (!isset($email)) {
            session()->flash('error-msg', 'Please enter valid email.');
            return redirect()->back();
        }
        if (!isset($password)) {
            session()->flash('error-msg', 'Please enter valid password.');
            return redirect()->back();
        }

        $admin = Admin::where('email', $email)->first();
        if (!isset($admin)) {

            $user = Customer::where('email', $email)->first();
            if (!isset($user)) {
                session()->flash('error-msg', 'User not found.');
                return redirect()->back();
            }

            if (!hash::check($password, $user->password)) {
                session()->flash('error-msg', 'Invalid password.');
                return redirect()->back();
            }

            session()->put('user', $user);
            session()->put('user-type', 2);
            return redirect('/dashboard');
        }

        if (!hash::check($password, $admin->password)) {
            session()->flash('error-msg', 'Invalid password.');
            return redirect()->back();
        }

        session()->put('user', $admin);
        session()->put('user-type', 1);
        return redirect('/dashboard');
    }

    public function logout()
    {
        session()->remove('user');
        session()->remove('user-type');
        return redirect('/login');
    }

    public function showForgotPasswordPage()
    {
        return view('forgot-password');

    }

    public function resetPassword() {

        $email = request('reminder-credential');
        $new_password = $this->randomPassword();
        $data = array('password'=>$new_password);

        try {
            Mail::send(['text' => 'mail'], $data, function ($message) use ($email) {
                $message->to($email, '')->subject
                ('Recovery Password');
                $message->from('portal@cubewerk.de', 'Web Portal');
            });
        } catch (Exception $e) {
            return view('forgot-password')->with([
                'message' => 'fail'
            ]);
        }

        $update_array = array(
        );
        $update_array['password'] = hash::make($new_password);

        Customer::where('email', $email)->update($update_array);

        return view('forgot-password')->with([
            'message' => 'success'
        ]);
    }

    function randomPassword() {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }

    public function dashboard()
    {
        $current_user_id = session()->get('user')->id;
        $products = Product::where('customer_id', $current_user_id)->count();
        $domains = Domain::where('customer_id', $current_user_id)->count();
        return view('dashboard')->with([
            'products' => $products,
            'domains' => $domains,
        ]);
    }

    public function showProfilePage() {
        $user = session()->get('user');
        return view('profile')->with('user', $user);
    }

    public function showMyPage() {
        $user = session()->get('user');
        $user_type = session()->get('user-type');

        if ($user_type == 3) {
            return redirect("/customers/detail/$user->id");
        } else {
            return view('profile')->with('user', $user);
        }
    }

    public function editProfile() {
        $id = request('id');
        $password = request('password');

        $update_array = array(
        );

        if ($password != '') {
            $update_array['password'] = hash::make($password);
        }

        $user_type = session()->get('user-type');

        if (count($update_array) > 0) {
            if ($user_type === 1) {
                Admin::where('id', $id)->update($update_array);
                session()->put('user', Admin::where('id', $id)->first());
            } else {
                Customer::where('id', $id)->update($update_array);
                session()->put('user', Customer::where('id', $id)->first());
            }
        }

        return back()
            ->with('success', 'You have successfully updated your profile.');
    }

    public function showCustomersPage()
    {
        $customers = Customer::get();
        return view('customers')->with('customers', $customers);
    }

    public function showCustomerAddPage()
    {
        return view('customer_add');
    }

    public function showCustomerEditPage()
    {
        $id = request('id');
        $customer = Customer::where('id', $id)->first();
        if ($customer != null) {
            return view('customer_edit')->with([
                'customer' => $customer
            ]);
        }
        return redirect('/customers');
    }

    public function showCustomerDetailPage()
    {
        $id = request('id');
        $customer = Customer::where('id', $id)->first();

        // Permission check
        $user_type = session()->get('user-type');
        $user = session()->get('user');
        if($user_type === 3 && $user->id != $id) {
            return redirect('/my-page');
        }

        $products = Product::where('customer_id', $id)->with('category','currency')->get();
        if ($customer != null) {
            return view('customer_detail')->with([
                'customer' => $customer,
                'products' => $products
            ]);
        }
        return redirect('/customers');
    }

    public function addCustomer()
    {
        $first_name = request('first-name');
        $last_name = request('last-name');
        $email = request('email');
        $password = request('password');
        $birthday = request('birthday');
        $gender = request('gender');
        $phonenumber = request('phone-number');
        $company = request('company');
        $address = request('address');
        $city = request('city');
        $state = request('state');
        $zipcode = request('zip-code');
        $start_date = request('start-date');
        $expire_date = request('expire-date');
        $price = request('price');

        request()->validate([
            'first-name' => 'required',
            'last-name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'birthday' => 'required|date',
            'phone-number' => 'required',
            'start-date' => 'required|date',
            'expire-date' => 'required|date',
            'price' => 'required|numeric',
        ]);

        $birthday = strtotime($birthday);
        $birthday = date('Y-m-d', $birthday);

        $start_date = strtotime($start_date);
        $start_date = date('Y-m-d', $start_date);

        $expire_date = strtotime($expire_date);
        $expire_date = date('Y-m-d', $expire_date);

        $customer = new Customer();
        $customer->first_name = $first_name;
        $customer->last_name = $last_name;
        $customer->email = $email;
        $customer->password = hash::make($password);
        $customer->birthday = $birthday;
        $customer->gender = $gender;
        $customer->phonenumber = $phonenumber;
        $customer->company = $company;
        $customer->address = $address;
        $customer->city = $city;
        $customer->state = $state;
        $customer->zipcode = $zipcode;
        $customer->start_date = $start_date;
        $customer->expire_date = $expire_date;
        $customer->price = $price;

        $customer->save();

        $invoice = new Invoices();
        $invoice->customer_id = $customer->id;
        $invoice->start_date = $start_date;
        $invoice->expire_date = $expire_date;
        $invoice->price = $price;

        $invoice->save();

        Customer::where('id', $customer->id)->update([
            'current_invoice_id' => $invoice->id
        ]);

        return back()
            ->with('success', 'You have successfully add new customer.');
    }

    public function editCustomer()
    {
        $id = request('id');
        $first_name = request('first-name');
        $last_name = request('last-name');
        $email = request('email');
        $password = request('password');
        $birthday = request('birthday');
        $gender = request('gender');
        $phonenumber = request('phone-number');
        $company = request('company');
        $address = request('address');
        $city = request('city');
        $state = request('state');
        $zipcode = request('zip-code');

        request()->validate([
            'first-name' => 'required',
            'last-name' => 'required',
            'email' => 'required|email',
            'birthday' => 'required|date',
            'phone-number' => 'required',
        ]);

        $birthday = strtotime($birthday);
        $birthday = date('Y-m-d', $birthday);

        if ($password != '') {
            Customer::where('id', $id)->update([
                'first_name' => $first_name,
                'last_name' => $last_name,
                'email' => $email,
                'password' => hash::make($password),
                'birthday' => $birthday,
                'gender' => $gender,
                'phonenumber' => $phonenumber,
                'company' => $company,
                'address' => $address,
                'city' => $city,
                'state' => $state,
                'zipcode' => $zipcode,
            ]);
        } else {
            Customer::where('id', $id)->update([
                'first_name' => $first_name,
                'last_name' => $last_name,
                'email' => $email,
                'birthday' => $birthday,
                'gender' => $gender,
                'phonenumber' => $phonenumber,
                'company' => $company,
                'address' => $address,
                'city' => $city,
                'state' => $state,
                'zipcode' => $zipcode,
            ]);
        }

        return back()
            ->with('success', 'You have successfully updated the customer\'s account');
    }

    public function delCustomer()
    {
        $id = request('id');
        Customer::where('id', $id)->delete();

        return Utils::makeResponse();
    }

    public function toggleCustomerEnable()
    {
        $id = request('id');
        $enable_flag = Customer::where('id', $id)->first()->enable_flag;

        Customer::where('id', $id)->update([
            'enable_flag' => 1 - $enable_flag,
        ]);

        return Utils::makeResponse();
    }

    public function toggleCustomerAddProduct()
    {
        $customer_id = request('customer_id');
        $product_id = request('product_id');
        $exist_flag = CustomerProducts::where([
            'customer_id' => $customer_id,
            'product_id' => $product_id,
        ])->exists();

        if ($exist_flag) {
            CustomerProducts::where([
                'customer_id' => $customer_id,
                'product_id' => $product_id,
            ])->delete();
        } else {
            $customer_product = new CustomerProducts();
            $customer_product->customer_id = $customer_id;
            $customer_product->product_id = $product_id;
            $customer_product->save();
        }

        return Utils::makeResponse();
    }

    public function printCustomerInvoice()
    {
        $id = request('id');
        $customer = Customer::where('id', $id)->first();
        $invoices = Invoices::where('customer_id', $id)->get();
        $total = Invoices::where('customer_id', $id)->sum('price');

        $pdf = PDF::loadView('customer_invoice_pdf', [
            'customer' => $customer,
            'invoices' => $invoices,
            'total' => $total,
            ]);
        //$pdf->save(storage_path('app/public') . '_filename.pdf');
        return $pdf->download('customer_invoice.pdf');

    }

    public function showProductsPage()
    {
        $current_user_id = session()->get('user')->id;
        $products = Product::where('customer_id', $current_user_id)->get();

        return view('products')->with([
            'product_array' => $products,
        ]);

    }

    public function showProductAddPage()
    {
        $customer_id = request('customer_id');
        if (session()->get('user-type') == 3) {
            $customer_id = session()->get('user')->id;
        }
        $categories = Domain::where('customer_id', $customer_id)->get();
        $currency_list = Currency::get();
        return view('product_add')->with([
            'categories' => $categories,
            'customer_id' => $customer_id,
            'currency_list' => $currency_list
        ]);
    }

    public function showProductEditPage()
    {
        $id = request('id');
        $product = Product::where('id', $id)->first();
        $categories = Domain::where('customer_id', $product->customer_id)->get();
        $currency_list = Currency::get();
        if ($product != null) {
            return view('product_edit')->with([
                'product' => $product,
                'categories' => $categories,
                'currency_list' => $currency_list,
            ]);
        }
        return redirect('/products');
    }

    public function showProductDetailPage()
    {
        $id = request('id');
        $product = Product::where('id', $id)->first();
        $categories = Domain::get();
        if ($product != null) {
            return view('product_detail')->with([
                'product' => $product,
                'categories' => $categories
            ]);
        }
        return redirect('/products');
    }

    public function addProduct()
    {
        $customer_id = request('customer_id');
        if (session()->get('user-type') == 3) {
            $customer_id = session()->get('user')->id;
        }
        $name = request('product-name');
        $name_ar = request('product-name-ar');
        $category_id = request('category');
        $price = request('product-price');
        $video_url = request('video-url');
        $description = request('product-description');
        $description_ar = request('product-description-ar');
        $currency = request('currency');

        request()->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:20480',
            'product-name' => 'required',
            'product-price' => 'required',
            'category' => 'required',
            'currency' => 'required',
        ]);

        $imageName = time() . '.' . request()->image->getClientOriginalExtension();

        $original_image_path = public_path('media/images/products/original');
        if (!file_exists($original_image_path)) {
            mkdir($original_image_path);
        }

        $appview_image_path = public_path('media/images/products/appview');
        if (!file_exists($appview_image_path)) {
            mkdir($appview_image_path);
        }

        $thumbnail_image_path = public_path('media/images/products/thumbnail');
        if (!file_exists($thumbnail_image_path)) {
            mkdir($thumbnail_image_path);
        }

        //Save original image
        request()->image->move($original_image_path, $imageName);

        // generate appview image
        Image::make($original_image_path . DIRECTORY_SEPARATOR . $imageName)
            ->resize(1200, 1200, function($constraint) {
                $constraint->aspectRatio();
            })
            ->save($appview_image_path . DIRECTORY_SEPARATOR . $imageName);

        // generate thumbnail image
        Image::make($original_image_path . DIRECTORY_SEPARATOR . $imageName)
            ->fit(320, 320)
            ->save($thumbnail_image_path . DIRECTORY_SEPARATOR . $imageName);

        if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $video_url, $match))
            $video_id = $match[1];
        else $video_id = $video_url;

        $product = new Product();
        $product->customer_id = $customer_id;
        $product->name = $name;
        $product->name_second = $name_ar;
        $product->price = $price;
        $product->currency_id = $currency;
        $product->category_id = $category_id;
        $product->description = $description;
        $product->description_second = $description_ar;
        $product->picture = $imageName;
        $product->video_id = $video_id;
        $product->video_url = $video_url;

        $product->save();

        return back()
            ->with('success', 'You have successfully add new product.');
    }

    public function editProduct()
    {
        $id = request('id');
        $name = request('product-name');
        $name_ar = request('product-name-ar');
        $category_id = request('category');
        $price = request('product-price');
        $description = request('product-description');
        $description_ar = request('product-description-ar');
        $currency = request('currency');
        $video_url = request('video-url');

        request()->validate([
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:20480',
            'product-name' => 'required',
            'product-price' => 'required',
            'category' => 'required',
            'currency' => 'required',
        ]);

        if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $video_url, $match))
            $video_id = $match[1];
        else $video_id = $video_url;

        if (isset(request()->image)) {
            $imageName = time() . '.' . request()->image->getClientOriginalExtension();

            $original_image_path = public_path('media/images/products/original');
            if (!file_exists($original_image_path)) {
                mkdir($original_image_path);
            }

            $thumbnail_image_path = public_path('media/images/products/thumbnail');
            if (!file_exists($thumbnail_image_path)) {
                mkdir($thumbnail_image_path);
            }

            //Save original image
            request()->image->move($original_image_path, $imageName);

            // generate thumbnail image
            Image::make($original_image_path . DIRECTORY_SEPARATOR . $imageName)
                ->fit(320, 320)
                ->save($thumbnail_image_path . DIRECTORY_SEPARATOR . $imageName);

            Product::where('id', $id)->update([
                'name' => $name,
                'name_second' => $name_ar,
                'price' => $price,
                'currency_id' => $currency,
                'category_id' => $category_id,
                'description' => $description,
                'description_second' => $description_ar,
                'video_id' => $video_id,
                'video_url' => $video_url,
                'picture' => $imageName,
            ]);
        } else {
            Product::where('id', $id)->update([
                'name' => $name,
                'name_second' => $name_ar,
                'price' => $price,
                'currency_id' => $currency,
                'category_id' => $category_id,
                'description' => $description,
                'description_second' => $description_ar,
                'video_id' => $video_id,
                'video_url' => $video_url
            ]);
        }
        return back()
            ->with('success', 'You have successfully updated the product.');
    }

    public function delProduct()
    {
        $id = request('id');
        Product::where('id', $id)->delete();

        return Utils::makeResponse();
    }

    public function toggleProductVisible()
    {
        $id = request('id');
        $show_flag = Product::where('id', $id)->first()->show_flag;

        Product::where('id', $id)->update([
            'show_flag' => 1 - $show_flag,
        ]);

        return Utils::makeResponse();
    }

    public function showCategoryFirstPage()
    {
        if (session()->get('user-type') != 3) {
            $customers_cnt = Customer::count();
            if ($customers_cnt > 0) {
                $customers = Customer::get();
                return redirect('categories/' . $customers[0]->id);
            }

            return view('customer_add')->with([
                'warning' => 'Warning'
            ]);
        }
        return redirect('categories/'.session()->get('user')->id);
    }

    public function showDomainPage()
    {
        $current_user_id = session()->get('user')->id;
        $domains = Domain::where('customer_id', $current_user_id)->get();
        return view('domain')->with([
            'domain_array' => $domains
        ]);
    }

    public function showCategoryAddPage()
    {
        $customer_id = request('customer_id');
        if (session()->get('user-type') == 3) {
            $customer_id = session()->get('user')->id;
        }
        return view('category_add')->with('customer_id', $customer_id);
    }

    public function showCategoryEditPage()
    {
        $id = request('id');
        $category = Domain::where('id', $id)->first();
        if ($category != null) {
            return view('category_edit')->with([
                'category' => $category
            ]);
        }
        return redirect('/categories');
    }

    public function showCategoryDetailPage()
    {
        $id = request('id');
        $category = Domain::where('id', $id)->first();
        $products = Product::where('category_id', $id)->get();
        if ($category != null) {
            return view('category_detail')->with([
                'category' => $category,
                'products' => $products
            ]);
        }
        return redirect('/categories');
    }

    public function addCategory()
    {
        $customer_id = request('customer_id');
        if (session()->get('user-type') == 3) {
            $customer_id = session()->get('user')->id;
        }
        $name = request('category-name');
        $name_ar = request('category-name-ar');
        $tags = request('category-tags');
        $tags_ar = request('category-tags-ar');

        request()->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:20480',
            'category-name' => 'required',
        ]);

        $imageName = time() . '.' . request()->image->getClientOriginalExtension();

        $original_image_path = public_path('media/images/categories/original');
        if (!file_exists($original_image_path)) {
            mkdir($original_image_path);
        }

        $thumbnail_image_path = public_path('media/images/categories/thumbnail');
        if (!file_exists($thumbnail_image_path)) {
            mkdir($thumbnail_image_path);
        }

        //Save original image
        request()->image->move($original_image_path, $imageName);

        // generate thumbnail image
        Image::make($original_image_path . DIRECTORY_SEPARATOR . $imageName)
            ->fit(320, 320)
            ->save($thumbnail_image_path . DIRECTORY_SEPARATOR . $imageName);

        $category = new Domain();
        $category->customer_id = $customer_id;
        $category->name = $name;
        $category->tags = $tags;
        $category->name_second = $name_ar;
        $category->tags_second = $tags_ar;
        $category->picture = $imageName;

        $category->save();

        return back()
            ->with('success', 'You have successfully add new category.')
            ->with('image', $imageName);
    }

    public function editCategory()
    {
        $id = request('id');
        $name = request('category-name');
        $name_ar = request('category-name-ar');
        $tags = request('category-tags');
        $tags_ar = request('category-tags-ar');

        request()->validate([
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:20480',
            'category-name' => 'required',
        ]);

        if (isset(request()->image)) {
            $imageName = time() . '.' . request()->image->getClientOriginalExtension();

            $original_image_path = public_path('media/images/categories/original');
            if (!file_exists($original_image_path)) {
                mkdir($original_image_path);
            }

            $thumbnail_image_path = public_path('media/images/categories/thumbnail');
            if (!file_exists($thumbnail_image_path)) {
                mkdir($thumbnail_image_path);
            }

            //Save original image
            request()->image->move($original_image_path, $imageName);

            // generate thumbnail image
            Image::make($original_image_path . DIRECTORY_SEPARATOR . $imageName)
                ->fit(320, 320)
                ->save($thumbnail_image_path . DIRECTORY_SEPARATOR . $imageName);
            Domain::where('id', $id)->update([
                'name' => $name,
                'tags' => $tags,
                'name_second' => $name_ar,
                'tags_second' => $tags_ar,
                'picture' => $imageName
            ]);
        } else {
            Domain::where('id', $id)->update([
                'name' => $name,
                'tags' => $tags,
                'name_second' => $name_ar,
                'tags_second' => $tags_ar
            ]);
        }
        return back()
            ->with('success', 'You have successfully updated category.');
    }

    public function delCategory()
    {
        $id = request('id');
        Domain::where('id', $id)->delete();
        Product::where('category_id', $id)->delete();

        return Utils::makeResponse();
    }

    public function toggleCategoryVisible()
    {
        $id = request('id');
        $show_flag = Domain::where('id', $id)->first()->show_flag;

        Domain::where('id', $id)->update([
            'show_flag' => 1 - $show_flag,
        ]);

        return Utils::makeResponse();
    }
}
