<?php

namespace App\Controllers\Client;

use App\Helpers\AuthHelper;

use App\Helpers\NotificationHelper;
use App\Validations\AuthValidation;
// use App\Views\Admin\Pages\Category\Edit;
use App\Views\Client\Components\Notification;
use App\Views\Client\Layouts\Footer;
use App\Views\Client\Layouts\Header;
use App\Views\Client\Pages\Auth\Login;
use App\Views\Client\Pages\Auth\Register;
use App\Views\Client\Pages\Auth\Edit;
use App\Views\Client\Pages\Auth\ForgotPassword;
use App\Views\Client\Pages\Auth\ResetPassword;

class AuthController
{

    // hàm hiển thị giao diện form regiter
    public static function register()
    {
        // Hiển thị header
        Header::render();


        // hien thi thong bao
        Notification::render();

        //huy session thong bao
        NotificationHelper::unset();
        // Hiển thị form đăng ký
        Register::render();
        // Hiển thị footer
        Footer::render();
    }
    public static function registerAction()
    {
        //    bắt lỗi validate
        // Kiểm tra thỏa mãn không?
        // nếu có: tiếp tục chạy lệnh ở dưới
        // nếu không thỏa (lỗi): thông báo và chuyển về trang đăng ký

        $is_valid = AuthValidation::register();

        if (!$is_valid) {
            NotificationHelper::error('register_valid', 'Đăng ký thất bại');
            header('location: /register');
            exit();
        }

        // Lấy dữ liệu người dùng nhập
        $username = $_POST['username'];
        $password = $_POST['password'];
        $hash_password = password_hash($password, PASSWORD_DEFAULT);
        $email = $_POST['email'];
        $name = $_POST['name'];

        // đưa dữ liệu vào mảng, lưu ý "key" trùng với tên cột trong cơ sở dữ liệu
        $data = [
            'username' => $username,
            'password' => $hash_password,
            'email' => $email,
            'name' => $name,
        ];

        $result = AuthHelper::register($data);
        if ($result) {
            header('location: /');
        } else {
            header('location: /register');
        }
    }
    public static function login()
    {
        // Hiển thị header
        Header::render();
        Notification::render();
        NotificationHelper::unset();
        // Hiển thị form đăng nhập
        Login::render();
        // Hiển thị footer
        Footer::render();
    }
    public static function loginAction()
    {
        // bat loi
        $is_valid = AuthValidation::login();

        if (!$is_valid) {
            NotificationHelper::error('login', 'Đăng nhập thất bại!');
            header('Location: /login');
            exit();
        }

        $data = [
            'username' => $_POST['username'],
            'password' => $_POST['password'],
            'remember' => isset($_POST['member']),

        ];

        $result = AuthHelper::login($data);
        if ($result) {
            // NotificationHelper::success('login', 'Đăng nhập thành công');
            header('Location: /');
        } else {
            // NotificationHelper::error('login', 'Đăng nhập thất bại');
            header('Location: /login');
        }
    }

    public static function logout()
    {
        AuthHelper::logout();
        NotificationHelper::success('logout', 'Đăng xuất thành công');
        header('Location: /');
    }

    public static function edit($id)
    {
        $result = AuthHelper::edit($id);
        if (!$result) {
            if (isset($_SESSION['error']['login'])) {
                header('Location: /login');
                exit;
            }
            if (isset($_SESSION['error']['user_id'])) {
                $data = $_SESSION['user'];
                $user_id = $data['id'];
                header("Location: /users/$user_id");
                exit;
            }
        }
        $data = $_SESSION['user'];
        header::render();
        Notification::render();
        NotificationHelper::unset();
        // giao dien thong tin user
        Edit::render($data);

        Footer::render();
    }

    // hien thi giao dien form lay lai mat khau 
    public static function forgotPassword()
    {
        // Hiển thị header
        Header::render();
        Notification::render();
        NotificationHelper::unset();
        // Hiển thị form đăng nhập
        ForgotPassword::render();
        // Hiển thị footer
        Footer::render();
    }

    // thuc thien chuc nang lay lai mat khau
    public static function forgotPasswordAction()
    {
        $is_valid = AuthValidation::forgotPassword();
        if (!$is_valid) {
            NotificationHelper::error('forgot_password', 'lấy lại mật khẩu thất bại');
            header('location:/forgot-password');
            exit;
        }
        $username = $_POST['username'];
        $email = $_POST['email'];
        $data = [
            'username' => $username,

        ];
        $result = AuthHelper::forgotPassword($data);
        if (!$result) {
            NotificationHelper::error('username_exist', 'Không tồn tại tài khoản này');
            header('location: /forgot-password');
            exit;
        }

        if ($result['email'] != $email) {
            NotificationHelper::error('email_exist', 'Email không đúng');
            header('location: /forgot-password');
            exit;
        }

        $_SESSION['reset_password'] = ['username' => $username, 'email' => $email];
        header('location:/reset-password');
        // echo 'thanh cong';
    }


    public static function resetPassword()
    {

        if(!isset($_SESSION['reset_password'])){
            NotificationHelper::error('reset_password', 'Vui lòng nhập đầy đủ thông tin của form này');
            header('location: /forgot-password');
            exit;
        }
        // Hiển thị header
        Header::render();
        Notification::render();
        NotificationHelper::unset();
        // Hiển thị form 
        ResetPassword::render();
        // Hiển thị footer
        Footer::render();
    }

    public static function resetPasswordAction()
    {
        // validation

        $is_valid = AuthValidation::resetPassword();

        if (!$is_valid) {
            NotificationHelper::error('reset_password', 'Đặt lại mật khẩu thất bại');
            header('location: /reset-password');
            exit;
        }

        $password = $_POST['password'];
        $hash_password = password_hash($password,PASSWORD_DEFAULT);
        $data =[
            'username'=> $_SESSION['reset_password']['username'],
            'email'=> $_SESSION['reset_password']['email'],
            'password'=> $hash_password
        ];
        $result=AuthHelper::resetPassword($data);
        if($result){
            NotificationHelper::success('reset_password','Đạt lại mật khẩu thành công');
            unset($_SESSION['reset_password']); //
            header('location: /login');
        }else{
            NotificationHelper::error('reset_password', 'Đặt lại mật khẩu thất bại');
            header('location: /reset-password');
        }
    }
}
