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
        - Example: `Tokens::where(['id', '=', 1], Tokens::INCLUDE_FOREIGN_DATA)->get();`
            - 