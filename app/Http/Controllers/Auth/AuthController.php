<?php namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\Guard;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;

class AuthController extends Controller {

    /**
     * the model instance
     * @var User
     */
    protected $user;
    /**
     * The Guard implementation.
     *
     * @var Authenticator
     */
    protected $auth;

    protected $loginPath = '/auth/login';

    /**
     * Create a new authentication controller instance.
     *
     * @param  Authenticator  $auth
     * @return void
     */
    public function __construct(Guard $auth, User $user)
    {
        $this->user = $user;
        $this->auth = $auth;

        $this->middleware('guest', ['except' => ['getLogout']]);
    }

    /**
     * Show the application registration form.
     *
     * @return Response
     */
    public function getRegister()
    {
        return redirect()->to('http://community.ggmaker.com/index.php?/register/');
    }

    /**
     * Show the application login form.
     *
     * @return Response
     */
    public function getLogin()
    {
        return view('auth.login');
    }

    /**
     * Handle a login request to the application.
     *
     * @param  LoginRequest  $request
     * @return Response
     */
    public function postLogin(LoginRequest $request)
    {
        $email = $request->get('email');
        $password = $request->get('password');
        $key = md5(config('services.ips_connect.secret') . $email);
        $baseUrl = config('services.ips_connect.url');

        $params = [
            'do' => 'fetchSalt',
            'key' => $key,
            'idType' => '2', // 1 = Display name, 2 = Email, 3 = Display name or email
            'id' => $email
        ];

        $response = json_decode(file_get_contents($baseUrl . '?' . http_build_query($params)), true);
        if ($response["status"] != 'SUCCESS') {
            return redirect()->back()->withErrors([
                'email' => 'The credentials you entered did not match our records. Try again?',
            ]);
        }

        $params = [
            'do' => 'login',
            'key' => $key,
            'idType' => '2', // 1 = Display name, 2 = Email, 3 = Display name or email
            'id' => $email,
            'password' => crypt($password, '$2a$13$' . $response["pass_salt"])
        ];

        $response = json_decode(file_get_contents($baseUrl . '?' . http_build_query($params)), true);
        if ($response["status"] != 'SUCCESS' || $response["connect_status"] != 'SUCCESS') {
            return redirect()->back()->withErrors([
                'email' => 'The credentials you entered did not match our records. Try again?',
            ]);
        }

        $user = $this->user->firstOrCreate([
            'email' => $email
        ]);

        $user->name = $response["name"];
        $user->connect_id = $response["connect_id"];
        $user->save();

        $this->auth->login($user, true);

        // If this is a login from Disqus, automatically close the popup window to trigger a Disqus reload.
        if ($request->get('from') == 'disqus') return "<script type='text/javascript'>window.close();</script>Login successful!";

        return redirect()->route('dashboard');
    }

    /**
     * Log the user out of the application.
     *
     * @return Response
     */
    public function getLogout()
    {
        $this->auth->logout();

        return redirect('/');
    }

}