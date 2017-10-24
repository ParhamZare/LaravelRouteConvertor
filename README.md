# LaravelRouteConvertor
Convert Laravel 5.2 Route to Laravel 5.4 or Higher...

**Introduction:**  
    Import RouteConvertor Class in Your Project and read Example ;)   
**example**
```
        $strRoutes = <<<str
          Route::controller('/home', 'HomeController');      
          Route::controller('/login', 'loginController');      
        str;
        $routes = explode(';', $strRoutes);
        $routeConvertor = new RouteConvertor();
        foreach ($routes as $route) {
            $tmpSt = trim($route);
            if (strlen($tmpSt) > 0) {
                echo $routeConvertor->convertRouteController($tmpSt);
            }
        }                
```
**Result**  
```
#################################### START SECTION HomeController####################################
  Route::get('/', 'HomeController@getIndex');
  Route::get('/home2', 'HomeController@getHome');  
#################################### END SECTION####################################
  
#################################### START SECTION loginController####################################
  Route::get('/login', 'loginController@postIndex');
  Route::post('/login', 'loginController@postIndex');  
  Route::get('/login/signup', 'loginController@getSignup');  
  Route::post('/login/signup', 'loginController@postSignup');  
#################################### END SECTION####################################

```





  
