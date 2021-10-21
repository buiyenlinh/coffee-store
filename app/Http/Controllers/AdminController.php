<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Role;
use App\Models\User;
use App\Models\Place;
use App\Models\Category;
use App\Models\Table;
use App\Models\Product;
use App\Models\Bill;
use App\Models\Detail;

class AdminController extends Controller
{
    /**
    *    login page
    */
    public function viewLogin() {
        return view('admin.login');
    }

    /**
     * Send login's data
     */

    public function login(Request $request) {
        if (Auth::check()) {
            return redirect()->intended('/admin/');
        }

        $request->validate(
            [
                'username' => 'required',
                'password' => 'required'
            ],
            [
                'username.required' => 'Tên đăng nhập là bắt buộc',
                'password.required' => 'Mật khẩu là bắt buộc'
            ]
        );
        $username = $request->old('username');
        $login = [
            'username' => $request->input('username'),
            'password' => $request->input('password')
        ];

        if (!Auth::attempt($login)) {
            return back()->withInput()->with('error', 'Tên đăng nhập hoặc mật khẩu sai');
        }
        return redirect()->route('home');
    }

    /**
     * Get user's level
     * @param integer $id
     * @return integer
     */
    private function getRole($id = 0) {
        if ($id == 0) {
            $id = Auth::user()->role_id;
        }
        $role = Role::find($id);
        return $role;
    }

    /**
     * Get menu info
     */
    private function getMenu() {
        $menu = [];
        $menu[] = [
            'link' => route('home', [], false),
            'title' => 'Trang chủ',
            'icon' => 'fas fa-tachometer-alt'
        ];

        $menu[] = [
            'link' => route('order', [], false),
            'title' => 'Đặt hàng',
            'icon' => 'fas fa-folder-plus'
        ];

        $menu[] = [
            'link' => route('table', [], false),
            'title' => 'Danh sách bàn',
            'icon' => 'fas fa-table'
        ];

        $menu[] = [
            'link' => route('product', [], false),
            'title' => 'Sản phẩm',
            'icon' => 'fas fa-coffee'
        ];

        $menu[] = [
            'link' => route('category', [], false),
            'title' => 'Loại sản phẩm',
            'icon' => 'fas fa-clipboard-list'
        ];

        $menu[] = [
            'link' => route('place', [], false),
            'title' => 'Khu vực',
            'icon' => 'fas fa-map'
        ];
        return $menu;
    }

    /**
     * Get info after login
     */
    public function getData() {
        $user = Auth::user()->toArray();
        $menu = $this->getMenu();
        $role = $this->getRole();
        if (empty($user['avatar'])) {
            $user['avatar'] = 'https://i.pinimg.com/736x/4e/db/ff/4edbff0d52c0d2f8e85fe0c0cc903993.jpg';
        }
        $data = [
            'user' => $user,
            'menu' => $menu,
            'role' => $role,
            'title' => null
        ];

        return $data;
    }

    /**
     * Admin page
     */
    public function viewHome() {
        $data = $this->getData();
        $data['title'] = 'Trang chủ';
        return view('admin.home', $data);
    }

    /**
     * Places page
     * get table list
     */
    public function viewPlace(Request $request) {
        $data_form = [];
        if ($request->has('add')) {
            $data_form = [
                'id' => null,
                'name' => null,
                'active' => null
            ];
        } 
        if ($request->has('edit')) {
            $place = Place::find($request->edit);
            $data_form = $place;
        }
        $data = $this->getData();
        $data['title'] = 'Khu vực';
        $data['data_form'] = $data_form;
        $places = Place::all();
        $data['places'] = $places->toArray();
        return view('admin.place', $data);
    }

    /**
     * Add place
     */
    public function addPlace(Request $request) {
        $role = $this->getRole();
        if ($role->level == 3) {
            return back()->withInput()->with('error_role', 'Tài khoản của bạn không có quyền thêm');
        }

        $request->validate(
            [ 'name' => 'required|unique:places' ],
            [
                'name.required' => 'Tên khu vực là bắt buộc',
                'name.unique' => 'Tên khu vực này đã tồn tại'
            ]
        );
        
        $name = $request->old('name');

        $active = $request->input('active');
        if ($active == null) {
            $active = 0;
        } else {
            $active = 1;
        }

        $place = Place::create([
            'name' => $request->input('name'),
            'active' => $active
        ]);
        return redirect()->route('place');
    }

    /**
     * Edit place
     */
    public function editPlace(Request $request) {
        $id = $request->id;
        $role = $this->getRole();
        if ($role->level == 3) {
            return back()->withInput()->with('error_role', 'Tài khoản của bạn không có quyền thực hiện');
        }

        $request->validate(
            [ 'name' => 'required' ],
            ['name.required' => 'Tên khu vực là bắt buộc']
        );

        $active = $request->input('active');
        if ($active == null) {
            $active = 0;
        } else {
            $active = 1;
        }
        $name = $request->old('name');
        $place_edit = Place::where('id', '!=' , $id)
            ->where('name', $request->input('name'))->get()->toArray();

        if(!empty($place_edit)) {
            return back()->withInput()->with('error_name', 'Tên khu vực đã tồn tại');
        }
        
        $place = Place::where('id', $id)
            ->update([
                'name' => $request->input('name'),
                'active' => $active
            ]);
        return redirect()->route('place');
    }

    /**
     * Delete place
     */
    public function deletePlace(Request $request) {
        $id = $request->id;
        $role = $this->getRole();
        if ($role->level == 3) {
            return response()->json([
                'status' => 'ERR',
                'error' => 'Tài khoản của bạn không có quyền xóa'
            ]);
        }
        $place = Place::find($id);
        $place->delete();
        return response()->json([
            'status' => 'OK',
            'redirect' => route('place', [], false)
        ]);
    }

    /**
     * CATEGORY PAGE
     * view category page
     */
    public function viewCategory(Request $request) {
        $data_form = [];
        if ($request->has('add')) {
            $data_form = [
                'id' => null,
                'name' => null,
                'active' => null
            ];
        } 
        if ($request->has('edit')) {
            $category = Category::find($request->edit);
            $data_form = $category;
        }
        $data = $this->getData();
        $data['title'] = 'Loại sản phẩm';
        $data['categories'] = Category::all()->toArray();
        $data['data_form'] = $data_form;
        return view('admin.category', $data);
    }

    /**
     * add category
     */
    public function addCategory(Request $request) {
        $role = $this->getRole();
        if ($role->level == 3) {
            return back()->withInput()->with('error', 'Tài khoản của bạn không có quyền thêm');
        }
        $request->validate(
            [ 'name' => 'required|unique:categories' ],
            [
                'name.required' => 'Tên loại sản phẩm là bắt buộc',
                'name.unique' => 'Tên loại sản phẩm này đã tồn tại'
            ]
        );
        
        $name = $request->old('name');

        $active = $request->input('active');
        if ($active == null) {
            $active = 0;
        } else {
            $active = 1;
        }
        
        $caterogy = Category::create([
            'name' => $request->input('name'),
            'active' => $active
        ]);
        return redirect()->route('category');
    }

    /**
     * Edit category
     */
    public function editCategory(Request $request) {
        $id = $request->id;
        $role = $this->getRole();
        if ($role->level == 3) {
            return back()->withInput()->with('error_role', 'Tài khoản của bạn không có quyền cập nhật');
        }

        $request->validate(
            ['name' => 'required'],
            ['name.required' => 'Tên loại sản phẩm là bắt buộc']
        );

        $active = $request->input('active');
        if ($active == null) {
            $active = 0;
        } else {
            $active = 1;
        }
        $name = $request->old('name');
        $category_edit = Category::where('id', '!=' , $id)
            ->where('name', $request->input('name'))->get()->toArray();

        if(!empty($category_edit)) {
            return back()->withInput()->with('error_name', 'Tên loại sản phẩm đã tồn tại');
        }
        
        $category = Category::where('id', $id)
            ->update([
                'name' => $request->input('name'),
                'active' => $active
            ]);
        return redirect()->route('category');
    }

    /**
     * Delete category
     */
    public function deleteCategory(Request $request) {
        $id = $request->id;
        $role = $this->getRole();
        if ($role->level == 3) {
            return response()->json([
                'status' => 'ERR',
                'error' => 'Tài khoản của bạn không có quyền xóa'
            ]);
        }
        $category = Category::find($id);
        $category->delete();
        return response()->json([
            'status' => 'OK',
            'redirect' => route('category', [], false)
        ]);
    }

    /**
     * TABLE PAGE
     */
    public function viewTable(Request $request) {
        $data_form = [];
        if ($request->has('add')) {
            $data_form = [
                'id' => null,
                'name' => null,
                'active' => null,
                'status' => null,
                'place' => null
            ];
        }

        if ($request->has('edit')) {
            $table = Table::find( $request->edit);
            $data_form = [
                'id' => $table->id,
                'name' => $table->name,
                'active' => $table->active,
                'status' => $table->status,
                'place' => $table->place_id
            ];
        }
        $data = $this->getData();
        $data['title'] = 'Danh sách bàn';
        $data['tables'] = Table::all();
        $data['data_form'] = $data_form;
        $data['places'] = Place::all()->toArray();
        return view('admin.table', $data);
    }

    /**
     * add table
     */
    public function addTable(Request $request) {
        $role = $this->getRole();
        if ($role->level == 3) {
            return back()->withInput()->with('error', 'Tài khoản của bạn không có quyền thêm');
        }
        $request->validate(
            ['name' => 'required|unique:tables'],
            [
                'name.required' => 'Tên bàn là bắt buộc',
                'name.unique' => 'Tên bàn này đã tồn tại'
            ]
        );
        
        $name = $request->old('name');
        $place = $request->old('place');

        $active = $request->input('active');
        if ($active == null) {
            $active = 0;
        } else {
            $active = 1;
        }
        
        $table = Table::create([
            'name' => $request->input('name'),
            'active' => $active,
            'place_id' => $request->input('place'),
            'status' => 0
        ]);
        return redirect()->route('table');
    }

    /**
     * Edit Table
     */
    public function editTable(Request $request) {
        $id = $request->id;
        $role = $this->getRole();
        if ($role->level == 3) {
            return back()->withInput()->with('error_role', 'Tài khoản của bạn không có quyền cập nhật');
        }

        $request->validate(
            [ 'name' => 'required' ],
            ['name.required' => 'Tên bàn là bắt buộc']
        );

        $active = $request->input('active');
        if ($active == null) {
            $active = 0;
        } else {
            $active = 1;
        }

        $name = $request->old('name');
        $table_edit = Table::where('id', '!=' , $id)
            ->where('name', $request->input('name'))->get()->toArray();

        if(!empty($table_edit)) {
            return back()->withInput()->with('error_name', 'Tên bàn đã tồn tại');
        }

        $table = Table::where('id', $id)
            ->update([
                'name' => $request->input('name'),
                'active' => $active,
                'place_id' => $request->input('place'),
            ]);
        return redirect()->route('table');
    }

    /**
     * Delete table
     */
    public function deleteTable(Request $request) {
        $id = $request->id;
        $role = $this->getRole();
        if ($role->level == 3) {
            return response()->json([
                'status' => 'ERR',
                'error' => 'Tài khoản của bạn không có quyền xóa'
            ]);
        }
        $table = Table::find($id);
        $table->delete();
        return response()->json([
            'status' => 'OK',
            'redirect' => route('table', [], false)
        ]);
    }

    /**
     * PRODUCT PAGE
     */
    public function viewProduct(Request $request) {
        $data_form = [];
        if ($request->has('add')) {
            $data_form = [
                'id' => null,
                'name' => null,
                'price' => null,
                'active' => null,
                'status' => null,
                'category_id' => null
            ];
        }

        if ($request->has('edit')) {
            $product = Product::find($request->edit);
            $data_form = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'active' => $product->active,
                'status' => $product->status,
                'category_id' => $product->category_id
            ];
        }

        $data = $this->getData();
        $data['title'] = 'Danh sách sản phẩm';
        $data['products'] = Product::all();
        $data['data_form'] = $data_form;
        $data['categories'] = Category::all();
        return view('admin.product', $data);
    }

     /**
     * add Product
     */
    public function addProduct(Request $request) {
        $role = $this->getRole();
        if ($role->level == 3) {
            return back()->withInput()->with('error_role', 'Tài khoản của bạn không có quyền thêm');
        }
        $request->validate(
            ['name' => 'required|unique:products'],
            [
                'name.required' => 'Tên sản phẩm là bắt buộc',
                'name.unique' => 'Tên sản phẩm này đã tồn tại'
            ]
        );
        
        $old_name = $request->old('name');
        $old_price = $request->old('price');
        $price = $request->price;
        if ($price == null) {
            return back()->withInput()->with('error_price', 'Giá sản phẩm là bắt buộc');
        }

        $active = $request->input('active');
        if ($active == null) {
            $active = 0;
        } else {
            $active = 1;
        }

        $status = $request->input('status');
        if($status == null) {
            $status = 0;
        } else {
            $status = 1;
        }
        
        $product = Product::create([
            'name' => $request->input('name'),
            'price' => $request->input('price'),
            'active' => $active,
            'category_id' => $request->input('category_id'),
            'status' => $status
        ]);
        return redirect()->route('product');
    }

    /**
     * Edit Product
     */
    public function editProduct(Request $request) {
        $id = $request->id;
        $role = $this->getRole();
        if ($role->level == 3) {
            return back()->withInput()->with('error_role', 'Tài khoản của bạn không có quyền cập nhật');
        }

        $request->validate(
            [ 'name' => 'required' ],
            ['name.required' => 'Tên sản phẩm là bắt buộc']
        );

        $active = $request->input('active');
        if ($active == null) {
            $active = 0;
        } else {
            $active = 1;
        }

        $status = $request->input('status');
        if ($status == null) {
            $status = 0;
        } else {
            $status = 1;
        }

        $price = $request->price;
        if ($price == null) {
            return back()->withInput()->with('error_price', 'Giá sản phẩm là bắt buộc');
        }

        $old_name = $request->old('name');
        $old_price = $request->old('price');

        $product_edit = Product::where('id', '!=' , $id)
            ->where('name', $request->input('name'))->get()->toArray();

        if(!empty($product_edit)) {
            return back()->withInput()->with('error_name', 'Tên sản phẩm đã tồn tại');
        }

        $product = Product::where('id', $id)
            ->update([
                'name' => $request->input('name'),
                'price' => $request->input('price'),
                'active' => $active,
                'category_id' => $request->input('category_id'),
                'status' => $status
            ]);
        return redirect()->route('product');
    }

    /**
     * Delete Product
     */
    public function deleteProduct(Request $request) {
        $id = $request->id;
        $role = $this->getRole();
        if ($role->level == 3) {
            return response()->json([
                'status' => 'ERR',
                'error' => 'Tài khoản của bạn không có quyền xóa'
            ]);
        }
        $product = Product::find($id);
        $product->delete();
        return response()->json([
            'status' => 'OK',
            'redirect' => route('product', [], false)
        ]);
    }


    /**
     * ORDER PAGE
     * view order
     */
    public function viewOrder() {
        $data = $this->getData();
        $data['places'] = Place::all();
        $data['products'] = Product::all();
        $data['categories'] = Category::all();
        $data['title'] = 'Đặt hàng';
        return view('admin.order', $data);
    }

    /**
     * find table with place_id
     */
    public function searchTable(Request $request) {
        $tables = [];
        if ($request->id == 0) {
            $tables = Table::all();
        } else {
            $tables = Table::where('place_id', '=', $request->id)->get()->toArray();
        }
        
        return response()->json([
            'status' => 'OK',
            'tables' => $tables
        ]);
    }


    public function getDetailByBillId($id) {
        $details = Detail::where('bill_id', '=', $id)->get()->toArray();
        $response = [];
        foreach($details as $_detail) {
            $dt = $_detail;
            $product = Product::find($_detail['product_id']);
            $response[] = [
                'dt' => $dt,
                'product' => $product
            ];
        };
            
        return $response;
    }
    /**
     * get bill detail by table_id
     */
    public function getBillDetail(Request $request) {
        $bill = Bill::where('table_id', '=', $request->id)
            ->where('status', '=', 0)->first();
        $response = [];
        if ($bill) {
            $response = $this->getDetailByBillId($bill->id);
        }
        
        return response()->json([
            'status' => 'OK',
            'details' => $response
        ]);
    } 

    /**
     * get product with category_id
     */
    public function getProductByCategory(Request $request) {
        $products = [];
        if ($request->id == 0) {
            $products = Product::all();
        } else {
            $products = Product::where('category_id', '=', $request->id)->get()->toArray();
        }
        
        return response()->json([
            'status' => 'OK',
            'products' => $products
        ]);
    }

    /**
     * add product in bill
     */
    public function addProductInBill(Request $request) {
        $table_id = $request->table_id;
        $product_id = $request->product_id;
        $number = $request->number;

        $bill_id = 0;
        $bill = Bill::where('table_id', '=', $table_id)
            ->where('status', '=', 0)
            ->first();
        if (!$bill) {
            $bill_id = Bill::create([
                'table_id' => $table_id,
                'user_id' => 1,
                'status' => 0
            ])->id;

            // Cập nhật status của table
            Table::where('id', '=', $table_id)
                ->update(['status' => 1]);
            
        } else {
            $bill_id = $bill->id;
        }

        Detail::create([
            'bill_id' => $bill_id,
            'product_id' => $product_id,
            'number' => $number
        ]);

        $response = $this->getDetailByBillId($bill_id);

        return response()->json([
            'status' => 'OK',
            'details' => $response
        ]);
    }

    /**
     * delete product in bill
     */
    public function deleteProductInBill(Request $request) {
        $id = $request->id;
        $detail = Detail::find($id);
        $bill_id = $detail->bill_id;
        $detail->delete();

        $response = $this->getDetailByBillId($bill_id);

        return response()->json([
            'act' => 'delete',
            'status' => 'OK',
            'details' => $response
        ]);
    }

    /**
     * update product in bill
     */
    function updateProductInBill(Request $request) {
        $table_id = $request->table_id;
        $product_id = $request->product_id;
        $number = $request->number;
        $detail_id = $request->detail_id;
        $bill_id = Detail::find($detail_id)->bill_id;
        Detail::where('id', '=', $detail_id)
            ->update(['product_id' => $product_id, 'number' => $number]);
        
        $response = $this->getDetailByBillId($bill_id);
        return response()->json([
            'status' => 'OK',
            'details' => $response
        ]);
    }

    /**
     * cancel table
     */
    function cancelTable(Request $request) {
        $table_id = $request->id;
        $bill = Bill::where('table_id', '=', $table_id)
            ->where('status', '=', 0)->get()->toArray();
        
        $bill_delete = Bill::find($bill[0]['id']);
        $bill_delete->delete();

        Table::where('id', $table_id)
            ->update(['status' => 0]);
            
        return response()->json([
            'status' => 'OK',
            'redirect' => route('order', [], false)
        ]);
    }
}
