# Documentation
### Routes
Routes can be considered either physical or theoretical. Physical routes actually refer to a path in a filesystem while theoretical routes only refer to a string that's compared to the url. In this API, theoretical routes are used.

A new Route is added by calling the get or post method of the Routes class in index.php ==> `$routes->get(string $route, $activator)` or `$routes->get(string $route, $activator)`. Depending on the use of the route, you can choose from the more secured post or get.
- `$route` is the theoretical path that you want to create.
- `$activator` is a concept that points to a method in a controller-class. In order to set it up properly, you have to provide a string looking like this: "ControllerClassName@MethodName". By convention you point towards a method in a controller class.

After you finished listing all your routes, you have to call the `$routes->listen()` method in order to check for route hits.

### Controllers
Controllers are the place to put your logic into. It's the one and only place to fetch, manipulate or create data from. Controllers live in `./controllers/*`.
Every method in a controller recieves two arguments being a `Request $req` and a `Response $res`. The Request contains a body `$req->getBody()`, which returns all post / get request parameters, and the request method `$req->getMethod()`. In order to serve content to the user, you have to use the `$res` parameter. There are three different options:
- `$res->json(array $input)` sends a dictionary, in json format, to the user.
- `$res->view(string $component, array $data)` renders an html component. `$component` is the relative path starting at `./public/html/`, which means, that a component living in `./public/html/auth/register.html` would be called `auth/register`. `$data` is a dictionary where key is the name of the variable and value is its value. Giving a title to each page is mandatory.
Error management can be done using the `Response $res`. There are two different ways to send an error: a visual error page `$res->errorVisual($errCode, $msg)` or a json message `$res->errorJSON($errCode, $msg)`. The visual error refers to an error page living in `./public/html/general/error.html`.

### Components
Components are HTML files, that can be manipulated in terms of placing data from php into it. They can be served by a controller using the `$res->view(string $component, array $data)` function. There are five different tags that manipulate, load or insert data:
- {{ variable_name }}
    - This tag creates a space for a variable called variable_name, which was passed over by the controller in the $data parameter in the view method.
- {% ressource_type %}
    - This tag inserts styles or scripts that live in `./public/[ressource_type]/[component_path]`
- {+ snippet_path +}
    - This tag inserts html snippets. The path format follows the component naming.
- <?php ?>
    - This tag allows you to write plain php inside of HTML. It shouldn't be used if it's not neccessacy since it's not that secure.

### Auth
Using APIs, Authentification is not as easy as usual. Since a session is not available, you have to store the logged in users somewhere else and create a new connection. The approach in this API uses the concept of Tokens, which are a 64 characters long, random string. When logged in, the token gets stored together with the user id and a timestamp. If you save the token on the local machine of the frontend-computer, you can always access your user id which has a connection to all of your data.

Auth is a very powerful class that allows you to manage the authentification system of your application with ease.
- `Auth::registerUser(User $user)`
    - This method registers a new user and sends an email containing an account setup link.
- `Auth::registerAccount(Account $account)`
    - This method registers a new account if a fitting user exists.
- `Auth::login(string $username, string $password, string $ip, bool $remember)`
    - The login method creates a new token and saves it to the Tokens database. It returns the saved token, which is recommended to be sent back to the frontend and there saved it using cookies.
- `Auth::user()` and `Auth::userID()`
    - If the user is logged in and the token is provided in the request, the user() method returns the whole user. userID() only returns the user id.
- `Auth::isLoggedIn()`
    - This method returns a bool if the user is logged in or not.
- `Auth::getToken()`
    - This method checks the given token and returns it if it's correct and not yet expired.
- `Auth::logout()`
    - This method deletes the token from the database which logs the user out.


### Middleware
Middleware is a set of restrictions that are checked, before the data is presented to the user. These restrictions can either be placed in the constructor of your controller to restrict all routes going through this controller or into individual methods which only restricts that specific route. You can use the `Middleware::auth()` method to restrict the access to this/these page/s to only be shown to registered users.