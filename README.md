# Documentation
### Routes
Routes can be considered either physical or theoretical. Physical routes actually refer to a path in a filesystem while theoretical routes only refer to a string that's compared to the url. In this API, theoretical routes are used.

A new Route is added by calling the add method of the Routes class in index.php ==> `$routes->add($route, $method, $activator)`
- `$route` is the theoretical path that you want to create.
- `$method` is the request method that can hit the route. You can choose from Routes::METHOD_GET and Routes::METHOD_POST.
- `$activator` is a concept that points to a method in a controller-class. In order to set it up properly, you have to provide a string looking like this: "ControllerClassName@MethodName". By convention you point towards a controller class.

### Controllers
Controllers are the place to put your logic into. It's the one and only place to fetch, manipulate or create data from. Controllers live in `./controllers/*`.
Every method in a controller recieves two arguments being a Request $req and a Response $res. The Request contains a body `$req->getBody()`, which returns all post / get request parameters and the request method `$req->getMethod()`. In order to serve content to the user, you have to use the $res parameter. There are two different options:
- `$res->json(array $input)` sends an array, in json format, to the user.
- `$res->view(string $component, array $data)` renders an html component.
- `$res->errorCode(int $errorCode)` sends an error code to the client.
- `$res->error(string $msg)` sends a json-message to the client.

### Components
Components are HTML files, that can be manipulated in terms of placing data from php into it. They can be served by a controller using the `$res->view(string $component, array $data)` function. The first parameter specifies, how the component, that lives in `./public/[component-name].html`, is called. Data can be inserted into your html file by creating a refferal like this: {{ your_variable_name }}. The data parameter has the structure of an array of key / value pairs, where the key is your_variable_name and the value is the code that you want the refferal with. If you want to serve an array of items, you have to create a table. This is done by using the `UI::table(array $data, array $headers, int $style)` function. The data has the same structure as above, with the change, that the keys are the link to their header. The headers are a list of all headers that you want to be presented. You could for example only present 2 out of 10 attributes sent with $data. To style the table, you can choose from different styles like the `UI::TABLE_STYLE_DEFAULT`. If you want your code to be styled, you have to insert `{% styles %}` at the bottom of the html head. Custom styles can be created in `./public/css/[component-name].css`

### Auth
Using APIs, Authentification is not as easy as usual. Since a session is not available, you have to store the logged in users somewhere else and create a new connection. The approach in this API uses the concept of Tokens, which are a 64 characters long, random string. When logged in, the token gets stored together with the user id and a timestamp. If you save the token on the local machine of the frontend-computer, you can always access your user id which has a connection to all of your data.

Auth is a very powerful class that allows you to manage the authentification system of your application with ease. In the class, you are provided with 5 methods:
- `Auth::login($userID)`
    - The login method creates a new token and saves it to the Tokens database. It returns the saved token, which is recommended to be sent back to the frontend and there saved it using cookies.
- `Auth::user()` and `Auth::userID()`
    - If the user is logged in and the token is provided in the request, the user() method returns the whole user. userID() only returns the user id.
- `Auth::getToken()`
    - This method checks the given token and returns it if it's correct and not yet expired.
- `Auth::logout()`
    - This method deletes the token from the database which logs the user out.
- `Auth::getPermissions()`
    - This method returns the level of permission that the user has. It can be either one of the following: `Auth::GUEST`, `Auth::USER`, `Auth::ADMIN` or `Auth::DOCTOR`. This can be used to restrict access to users, who shouldn't see, whatever the doctor has to see.


### Middleware
Middleware is a set of restrictions that are checked, before the data is presented to the user. These restrictions can either be placed in the constructor of your controller to restrict all routes going through this controller or into individual methods which only restricts that specific route. You can use the `Middleware::auth()` method to restrict the access to this/these page/s to only be shown to registered users.