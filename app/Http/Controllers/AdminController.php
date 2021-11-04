<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\Role;
use App\Models\User;
use App\Models\Place;
use App\Models\Category;
use App\Models\Table;
use App\Models\Product;
use App\Models\Bill;
use App\Models\Detail;

use App\Http\InitData;

class AdminController extends Controller
{
    use InitData;
    // Sử dụng $this->getInt like this ^ ^
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
            'password' => $request->input('password'),
            'active' => 1
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

        $role = $this->getRole();
        if ($role->level == 1 || $role->level == 2) {
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

            $menu[] = [
                'link' => route('user', [], false),
                'title' => 'Người dùng',
                'icon' => 'fas fa-users'
            ];
        }
        
        $menu[] = [
            'link' => route('profile', [], false),
            'title' => 'Tài khoản',
            'icon' => 'fas fa-user-circle'
        ];

        if ($role->level == 1) {
            $menu[] = [
                'link' => route('bill'),
                'title' => 'Hóa đơn',
                'icon' => 'fas fa-money-bill-alt'
            ];
        }

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

        $table = Table::where('place_id', $id)
            ->update([
                'active_parent' => $active
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
        
        $product = Product::where('category_id', $id)
            ->update([
                'active_parent' => $active
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
        
        $place_check = Place::where('id', '=', $request->input('place'))->get()->toArray();
        $active_parent = $place_check[0]['active'];

        $table = Table::create([
            'name' => $request->input('name'),
            'active' => $active,
            'active_parent' => $active_parent,
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
        
        $category_check = Category::where('id', '=', $request->input('category_id'))->get()->toArray();
        $active_parent = $category_check[0]['active'];

        $product = Product::create([
            'name' => $request->input('name'),
            'price' => $request->input('price'),
            'active' => $active,
            'active_parent' => $active_parent,
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
        
        return $details;
    }
    /**
     * get bill detail by table_id
     */
    public function getBillDetail(Request $request) {
        $bill = Bill::where('table_name', '=', $request->name)
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
        $table_name = $request->table_name;
        $product_id = $request->product_id;
        $number = $request->number;

        $username = Auth::user()->username;
        $product = Product::find($product_id);
        $bill_id = 0;
        $bill = Bill::where('table_name', '=', $table_name)
            ->where('status', '=', 0)
            ->first();
        
        if (!$bill) {
            $bill_id = Bill::create([
                'table_name' => $table_name,
                'username' => $username,
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
            'username' => $username,
            'product' => $product->name,
            'price' => $product->price,
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
        $product_id = $request->product_id;
        $product = Product::find($product_id);

        $number = $request->number;
        $detail_id = $request->detail_id;

        $bill_id = Detail::find($detail_id)->bill_id;

        Detail::where('id', '=', $detail_id)
            ->update(['product' => $product->name, 'number' => $number]);

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

    /**
     * pay table
     */
    public function payTable(Request $request) {
        $table_name = $request->table_name;
        $bill = Bill::where('table_name', '=', $table_name)
            ->where('status', '=', 0)->get()->toArray();

        if (!$bill) {
            return response()->json([
                'status' => 'ERR',
                'message' => 'Bàn này chưa có sản phẩm. Vui lòng thêm trước khi thanh toán.'
            ]);
        }

        Table::where('name', '=', $table_name)
            ->update(['status' => 0]);
        
        Bill::where('table_name', '=', $table_name)
            ->where('status', '=', 0)
            ->update(['status' => 1]);
        return response()->json([
            'status' => 'OK',
            'redirect' => route('order', [], false)
        ]);
    }

    /**
     * get table list for move table
     * return {
     *  status
     *  response : danh sách các bàn để chuyển đến (bàn trống)
     *  table-move: Thông tin bàn được chuyển
     * }
     */
    public function getTableMove(Request $request) {
        $id = $request->table_id;
        $table = Table::find($id)->toArray();
        
        if (!$table['status']) {
            return response()->json([
                'status' => 'ERR',
                'message' => $table['name'] . ' trống'
            ]);
        }

        $response = Table::where('id', '!=', $id)
            ->where('status', '=', 0)
            ->where('active_parent', 1)
            ->get()
            ->toArray();

        return response()->json([
            'status' => 'OK',
            'response' => $response,
            'table_move' => $table
        ]);
    }


    /**
     * get table list for merge table
     * return {
     *  status
     *  response : danh sách các bàn để gộp (bàn có người)
     *  table-move: Thông tin bàn được chuyển
     * }
     */
    public function getTableMerge(Request $request) {
        $id = $request->table_id;
        $table = Table::find($id)->toArray();
        
        if (!$table['status']) {
            return response()->json([
                'status' => 'ERR',
                'message' => $table['name'] . ' trống'
            ]);
        }

        $response = Table::where('id', '!=', $id)
            ->where('status', '=', 1)
            ->where('active_parent', 1)
            ->get()
            ->toArray();

        return response()->json([
            'status' => 'OK',
            'response' => $response,
            'table_move' => $table
        ]);
    }


    /**
     * Thực hiện chuyển bàn
     */
    public function moveTable(Request $request) {
        $table_move_id = $request->table_move_id; // Bàn chuyển/gộp
        $table_move_to_id = $request->table_move_to_id; // Bàn đc chuyển/gộp tới

        $bill2 = Bill::where('table_id', $table_move_to_id)
            ->where('status', 0)
            ->get()->toArray();
        
        if ($bill2) {  // Gộp bàn
            $bill1 = Bill::where('table_id', $table_move_id)->get()->toArray();

            // Chuyển sp bill1 qua bill2
            Detail::where('bill_id', $bill1[0]['id'])
                ->update([
                    'bill_id' => $bill2[0]['id']
                ]);

            // Xóa bill1
            $bill_del = Bill::where('table_id', $table_move_id);
            $bill_del->delete();

        } else {    // Chuyển bàn
            Bill::where('table_id', $table_move_id)
            ->where('status', 0)
            ->update(['table_id' => $table_move_to_id]);
        }

        Table::where('id', $table_move_id)
            ->update(['status' => 0]);
        
        Table::where('id', $table_move_to_id)
            ->update(['status' => 1]);

        return response()->json([
            'status' => 'OK',
            'redirect' => route('order', [], false)
        ]);
    }

    /**
     * View users => users list
     */
    public function viewUser(Request $request) {
        $data_form = [];
        if ($request->has('add')) {
            $data_form = [
                'id' => null,
                'fullname' => null,
                'username' => null,
                'password' => null,
                'avatar' => null,
                'active' => null,
                'gender' => null,
                'birthday' => null,
                'address' => null,
                'role_id' => null
            ];
        }

        if ($request->has('edit')) {
            $user = User::find($request->edit);
            $data_form = [
                'id' => $user->id,
                'fullname' => $user->fullname,
                'username' => $user->username,
                'password' => $user->password,
                'avatar' => $user->avatar,
                'active' => $user->active,
                'gender' => $user->gender,
                'birthday' => $user->birthday,
                'address' => $user->address,
                'role_id' => $user->role_id
            ];
            if ($data_form['avatar']) {
                $data_form['avatar'] = Storage::url($user->avatar);
            }
        }

        $data = $this->getData();
        $data['title'] = 'Người dùng';
        $users = User::all();
        $data['users'] = $users;
        $data['data_form'] = $data_form;
        return view('admin.user', $data);
    }

    /**
     * Add user
     */

    public function addUser(Request $request) {

        $role = $this->getRole();
        if ($role->level == 3) {
            return back()->withInput()->with('error_role', 'Tài khoản của bạn không có quyền thêm');
        }

        $request->validate(
            [
                'fullname' => 'required',
                'password' => 'required',
                'username' => 'required|unique:users'
            ],
            [
                'fullname.required' => 'Họ tên người dùng là bắt buộc',
                'password.required' => 'Mật khẩu là bắt buộc',
                'username.required' => 'Tên đăng nhập là bắt buộc',
                'username.unique' => 'Tên đăng nhập đã được sử dụng'
            ]
        );

        $old_fullname = $request->old('fullname');
        $old_username = $request->old('username');
        $active = 0;
        if ($request->active) {
            $active = 1;
        }
        $user = User::create([
            'fullname' => $request->fullname,
            'username' => $request->username,
            'active' => $active,
            'password' => bcrypt($request->password),
            'role_id' => $request->role_id
        ]); 
        return redirect()->route('user');
    }

    /**
     * Edit user
     */
    public function editUser(Request $request) {
        $userUpdate = User::find($request->id);
        $roleUserUpdate = Role::find($userUpdate->role_id);

        $role = $this->getRole();
        if ($role->level == 3 || $role->level >= $roleUserUpdate->level) {
            return back()->withInput()->with('error_role', 'Tài khoản của bạn không có quyền chỉnh sửa tài khoản này');
        }

        $request->validate(
            [ 'fullname' => 'required' ],
            [ 'fullname.required' => 'Họ tên người dùng là bắt buộc' ]
        );

        $old_fullname = old($request->fullname);
        $old_address = old($request->address);
        $old_gender = old($request->gender);

        $active = 0;
        if ($request->active) {
            $active = 1;
        }

        $time = explode('-', $request->birthday);
		$birthday = mktime(0, 0, 0 , $time[1], $time[2], $time[0]);

        $avatar = $userUpdate->avatar;
        if ($request->avatar) {
            $avatar = $request->file('avatar')->store('public');
        }
        
        $address = $request->address;
        if ($address == null) {
            $address = '';
        }
        
        $user = User::where('id', $request->id)
            ->update([
                'fullname' => $request->fullname,
                'active' => $active,
                'gender' => $request->gender,
                'birthday' => $birthday,
                'address' => $address,
                'avatar' => $avatar
            ]);
        
        return redirect()->route('user');
    }

    /**
     * Delete user
     */

    public function deleteUser(Request $request) {
        $id = $request->id;

        $userUpdate = User::find($id);
        $roleUserUpdate = Role::find($userUpdate->role_id);

        $role = $this->getRole();
        if ($role->level == 3 || $role->level >= $roleUserUpdate->level) {
            return response()->json([
                'status' => 'ERR',
                'error' => 'Tài khoản của bạn không có quyền xóa tài khoản này'
            ]);
        }

        $user = User::find($id);
        $user->delete();
        return response()->json([
            'status' => 'OK',
            'redirect' => route('user')
        ]);
    }


    /**
     * Delete avatar
     */

    public function deleteAvatar(Request $request) {
        $id = $request->id;
        $userUpdate = User::find($id);
        $roleUserUpdate = Role::find($userUpdate->role_id);

        $role = $this->getRole();
        if ($role->level == 3 || $role->level >= $roleUserUpdate->level) {
            return response()->json([
                'status' => 'ERR',
                'error' => 'Tài khoản của bạn không có quyền chỉnh sửa tài khoản này'
            ]);
        }

        $user = User::find($id)
            ->update([
                'avatar' => ''
            ]);
        return response()->json([
            'status' => 'OK',
            'redirect' => '/admin/user?edit=' . $id
        ]);
    }

    /**
     * Profile
     */
    public function viewProfile(Request $request) {
        $data = $this->getData();
        $data['title'] = "Tài khoản";
        $user = Auth::user();
        $data_form = [
            'id' => $user->id,
            'fullname' => $user->fullname,
            'username' => $user->username,
            'password' => $user->password,
            'avatar' => $user->avatar,
            'active' => $user->active,
            'gender' => $user->gender,
            'birthday' => $user->birthday,
            'address' => $user->address,
            'role_id' => $user->role_id
        ];

        if ($data_form['avatar']) {
            $data_form['avatar'] = Storage::url($user->avatar);
        }

        $data['data_form'] = $data_form;
        return view('admin.profile', $data);
    }


    /**
     * edit profile
     */
    public function editProfile(Request $request) {
        $user = Auth::user();
        $time = explode('-', $request->birthday);
		$birthday = mktime(0, 0, 0 , $time[1], $time[2], $time[0]);
        if ($request->fullname == null || $request->username == null || $request->address == null) {
            return back()->withInput()->with('info_error', 'Vui lòng điền đủ thông tin trước khi cập nhật!');
        }

        $avatar = $user->avatar;
        if ($request->avatar) {
            $avatar = $request->file('avatar')->store('public');
        }

        User::where('id', $user->id)
            ->update([
                'fullname' => $request->fullname,
                'username' => $request->username,
                'gender' => $request->gender,
                'address' => $request->address,
                'birthday' => $birthday,
                'avatar' => $avatar
            ]);
        return redirect()->route('profile');
    }

    /**
     * Change password in profile page
     */
    public function changePassword(Request $request) {
        $old_pass = $request->old_pass;
        $new_pass = $request->new_pass;
        $re_new_pass = $request->re_new_pass;
        $user = Auth::user();

        return redirect()->route('profile');
    }

    /**
     * Delete avatar in profile page
     */
    public function deleteAvatarProfile() {
        $user = Auth::user();
        User::where('id', $user->id)
            ->update(['avatar' => '']);
        return response()->json([
            'status' => 'OK',
            'redirect' => route('profile')
        ]);
    }

    /**
     * Logout
     */
    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'status' => 'OK',
            'redirect' => route('login')
        ]);
    }

    /**
     * view bill page
     */
    public function viewBill() {
        $data = $this->getData();
        $data['title'] = 'Hóa đơn';

        $data['bills'] = Bill::all()->toArray();
        return view('admin.bill', $data);
    }

    /**
     * get product list in bill
     */
    public function billDetail(Request $request) {
        $details = Detail::where('bill_id', $request->bill_id)
            ->get()->toArray();

        for ($i = 0; $i < count($details); $i++) {
            $details[$i]['created_at'] = date('H:i, d/m/y', strtotime($details[$i]['created_at']));
        }
        return response()->json([
            'status' => 'OK',
            'details' => $details
        ]);
    }
}
