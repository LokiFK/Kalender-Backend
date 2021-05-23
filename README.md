# Documentation
### Routes
Routes can be considered either physical or theoretical. Physical routes actually refer to a path in a filesystem while theoretical routes only refer to a string that's compared to the url.

A new Route is added by calling the add method of the Routes class in index.php.
    'add($route, $method, $activator)'
- '$route' is the theoretical path that you want to create.
- '$method' is the request method that can hit the route. You can choose from Routes::METHOD_GET and Routes::METHOD_POST.
- '$activator' is a concept that points to a method in a controller-class. In order to set it up properly you have to provide an array looking like this: [ClassName, MethodName]. By convention you point towards a controller class.
