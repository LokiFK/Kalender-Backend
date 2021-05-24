# Documentation
### Routes
Routes can be considered either physical or theoretical. Physical routes actually refer to a path in a filesystem while theoretical routes only refer to a string that's compared to the url. In this API, theoretical routes are used.

A new Route is added by calling the add method of the Routes class in index.php ==> `add($route, $method, $activator)`

- `$route` is the theoretical path that you want to create.
- `$method` is the request method that can hit the route. You can choose from Routes::METHOD_GET and Routes::METHOD_POST.
- `$activator` is a concept that points to a method in a controller-class. In order to set it up properly, you have to provide an array looking like this: [ClassName, MethodName]. By convention you point towards a controller class.

### Controllers
Controllers are the place to put your logic into. It's the one and only place to fetch, manipulate or create data from. Controllers live in `./controllers/*`.
Every method in a controller must be public and non-static.

### Models
Models are an abstraction of a database table. Using them you'll be able to easily fetch, update, delete or insert new data to your table. Models live in `./models/*`.

- Models have a name saved in `protected $tableName = "";`
- Models have a `columns()` method that stores all attributes.
    - First, all attributes have to be shown for once. You can do that by using the `$this->int()` or `$this->string()` method giving your attribute the type. Both methods have two parameters, the first one is the attribute name and the second one is the length.
    - After you created all of your attributes, you have to call the `$this->create()` method to create a first version of your table.
    - Finally, you can customize your primary and foreign keys.
        - A primary key is created by calling the `$this->primary()` method with the attribute name of the attribute, that is supposed to become a primary key, provided as an argument.
        - A foreign key is created by calling the `$this->foreign()` method. You have to provide three parameters:
            1. The attribute that you want the foreign key to be.
            2. The referencing table
            3. The referencing attribute
- Below the `columns()` method, there are all possible operations, that are available to you, listed.
    - `where($conditions, $withForeignRelations)`
        - with this method you can place conditions in your query to fetch a special row in your table.
        - Examples
            - Easy
                Tokens::where(
                    ['id', '=', 1], 
                    Tokens::INCLUDE_FOREIGN_DATA
                )->get();
                - This query fetches all Tokens, that have an id of 1.
                - Your condition is formulated in an array following `[column, comparing sign, value]`
                - Because the INCLUDE_FOREIGN_DATA option is chosen, this query will automatically include the data behind your foreign key. That data is stored into your result in a subfolder called whatever the attribute was called minus the _id (user_id would be user)
                - The `get()` method grabs the data from the query result. As a parameter you can provide an array of columns that you want to be fetched.
            - Hard
                Tokens::where(
                    [
                        'control' => "1 OR 2",
                        1 => ['id', '=', 5],
                        2 => ['id', '=', 2],
                    ],
                    Tokens::IGNORE_FOREIGN_DATA
                )->get(['user_id', 'token']);
                - This query fetches all Tokens, that have an id of 2 or 5.
                - The given array at index 0 now doesn't just have a column, a comparing sign and a value, but one item called `control`, which gives the program an idea of how you want the conditions to be combined. You can customize it in a way you could with SQL. Your conditions are named with numbers (!!! Only with numbers and only from 0 to 9 !!!)). Your conditons are formulated just like if you wouldn't have multiple ones.
                - IGNORE_FOREIGN_DATA implifies, that your query won't contain any foreign data.
                - In this case, 2 columns (user_id and token) were specified in the `get()` method. This means, that only these 2 columns are returned.
    - `fetchAll()`
        - This method returns all entries of the table
    - `fetch()`
        - Provided an id, this method returns the element using that id.
    - `insert()`
        - Provided all values in order of the table without an id, this method inserts a new record to the table.
    - `delete()`
        - Provided an id, this method deletes the element using that id.
    - `drop()`
        - This method deletes the whole table with all records unrecoverably. It's recommended to use this method only in a development environment.

### Auth
Using APIs, Authentification is not as easy as usual. Since a session is not available, you have to store the logged in users somewhere else and create a new connection. The approach in this API uses Tokens, which stores a 64 characters, random string (= the token), the user id and a timestamp. If you save the token on the local machine of the frontend-computer, you can always access your user id which has a connection to all of your data.
Auth is a very powerful class that allows you to manage the authentification system of your application with ease. In the class, you are provided with 5 methods:
- `login($userID)`
    - The login method creates a new token and saves it to the Tokens database. It returns the saved token, which is recommended to be sent back to the frontend and there saved it using cookies.
- `user()` and `userID()`
    - If the user is logged in and the token is provided in the request, the user() method returns the whole user. userID only returns the user id.
- `getToken()`
    - This method checks the given token and returns it if it's correct.
- `logout()`
    - This method deletes the token from the database which logs the user out.

### Request
Every function that's pointed on by a route, gets a request as a parameter. 
- Using the `getBody()` method, you can get all parameters that were sent in the request.
- Using the `getMethod()` method, you can get the request method of the route.

### UI
The UI class creates the bridge between frontend and backend.
- `send()` sends data inserted as a parameter to the frontend.
- `error()` sends an error message to the frontend, it is recommended to exit the script after an error, using the `exit;` keyword, to prevent damage to your database.
