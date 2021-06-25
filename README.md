# Documentation
### Routes
Routes can be considered either physical or theoretical. Physical routes actually refer to a path in a filesystem while theoretical routes only refer to a string that's compared to the url. In this API, theoretical routes are used.

A new Route is added by calling the get or post method of the Routes class in index.php ==> `$routes->get(string $route, $activator)` or `$routes->get(string $route, $activator)`. Depending on the use of the route, you can choose from the more secured post or get.
- `$route` is the theoretical path that you want to create.
- `$activator` is a concept that points to a method in a controller-class. In order to set it up properly, you have to provide a string looking like this: "ControllerClassName@MethodName". By convention you point towards a method in a controller class.
If you want to trigger get and post on the same route, you can use `$routes->both(string $route, $activator)` and split the `$activator` method in GET / POST parts using the `$req->getMethod()` method

After you finished listing all your routes, you have to call the `$routes->listen()` method in order to check for route hits.

### Controllers
Controllers are the place to put your logic into. It's the one and only place to fetch, manipulate or create data from. Controllers live in `./controllers/*`.
Every method in a controller recieves two arguments being a `Request $req` and a `Response $res`. The Request contains a body `$req->getBody()`, which returns all post / get request parameters, and the request method `$req->getMethod()`. In order to serve content to the user, you have to use the `$res` parameter. There are three different options:
- `$res->json(array $input)` sends a dictionary, in json format, to the user.
- `$res->view(...)` renders an html component.
Error management can be done using the `Response $res`. There are two different ways to send an error: a visual error page `$res->errorVisual($errCode, $msg)` or a json message `$res->errorJSON($errCode, $msg)`. The visual error refers to an error page living in `./public/html/general/error.html`.

### Template Engine
#### Controller Part
Components are HTML files, that can be manipulated in terms of placing data from php into it. They can be served by a controller using the `$res->view(string $component, array $data = array(), array $safeData = array(), array $loopData = array())` function.
- `string $component`
    - This parameter is the path to the html component starting at `./public/html/`. This means, that a component living in `./public/html/auth/register.html` would be called `auth/register`.
- `array $data = array()`
    - Using this parameter, you can push variables from php into an html file. All basic types are allowed, Arrays and Dictionaries are not. An example is the username, which is later displayed in a welcome message. The variables are inserted using a key / value pair where key is the name called in HTML and value is your variable value: ['Your_wonderful_var_name' => 'Your_wonderful_var_value'].
- `array $safeData = array()`
    - It's basically the same as above, but you aren't allowed to put dynamic data into it. Since this data is inserted before executing php in the html file, you could catch an injection attack if any user inserts PHP code as for example their username. A good example for using this parameter is to serve the current date and time or the title of the page.
- `array $loopData = array()`
    - This parameter is used to serve arrays in HTML. The type convension is array, basic data types are not allowed. You can insert your array as following: ['Your_wonderful_array_name' => ['Your_wonderful_array_values']].

#### HTML Part
There are tags that manipulate, load or insert data directly in HTML (Warning: every space is important, we work with indexes of different characters):
- `{{ variable_name }}`
    - This tag creates a place, where your variable, inserted in `array $data = array()`, can be inserted.
- `{! array_name: *Iteration template* !}`
    - This tag allows you to loop over arrays.
    - `*Iteration template*` is the element, that you want to create with every iteration.
    - Inside `*Iteration template*`, you can call the current iteration by using:
        - `{{ array_name(.*) }}` gives you the data behind the current iteration. Using the `.*` syntax, you can walk through json data.
        - `{{ array_name.iterationNr }}` gives you the current iteration index (= i)
- `{% ressource_type %}`
    - This tag inserts styles or scripts that live in `./public/[ressource_type]/[component_path]`.
    - `{% ressource_type/path_to_ressource %}`
        - If you want to load custom ressources, you can use this syntax. The `path_to_ressource` follows the naming of components.
- `{+ snippet_path +}`
    - This tag inserts HTML snippets. The path format follows the component naming.
- `{[ variable_name=>variable_value ]}`
    - This tag manipulates `array $data = array()` by inserting another value. This is used to create different page titles for different pages
- `{# create container_name #}`
    - This tag creates a container, which you can later extend using `{# extend path_to_container_file@container_name #}`
- `<?php ?>`
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

### DB
Using DB, you can query databases without having to connect every time. There are two different methods:
- `DB::query(string $query, array $params = array())`
    - With this method, you can write all kinds of queries
- `DB::table(string $tableName)->...->get()`
    - This method is only for simple select queries. You can `orderBy(string $column, int $direction = 0)`, write conditions `where(string $query, array $params = array())` or simply `get(array $foreignData = array(), array $columns = array())`
        - `get()`
            - `$foreignData` is an array following this sequence: [new ForeignDataKey($key, $relationTable, $relationColumn)] where $key is the foreign key, $relationTable is the referencing table and $relationColumn is the referencing column. If your $key is "userID", the foreign Data is going to be stored in "user".
            - `$columns` is an array of all columns that you want to get.

### Middleware
Middleware is a set of restrictions that are checked, before the data is presented to the user. These restrictions can either be placed in the constructor of your controller to restrict all routes going through this controller or into individual methods which only restricts that specific route.
- `auth()` ==> Only for logged in users
- `statusBiggerThan(int $status)`
- `statusSmallerThan(int $status)`
- `statusEqualTo(int $status)`
- `statusBiggerOrEqualTo(int $status)`
- `statusSmallerOrEqualTo(int $status)`