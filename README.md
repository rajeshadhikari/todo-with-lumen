# Lumen PHP Framework
```sh
git clone git@bitbucket.org:rajeshadhikari/todo-with-lumen.git
composer install
configure database connection in .env file
php artisan migrate
php artisan db:seed
php -S localhost:8000 -t public
```



`LOGIN`

	Post : http://localhost:8000/api/v1/login
	bodyParam email        email
	bodyParam password     default_password = 12345678      
	

`Get all todo items with filter option`

	Url GET : /api/v1/todo
	headerParam api_token       string
	queryParam is_complete optional value 0 or 1 e.g GET : /api/v1/todo?is_complete=1

`Get single todo item`

	GET : /api/v1/todo/{id}
	param id
	headerParam api_token       string

`Crete new todo item`

	POST : /api/v1/todo
	headerParam api_token       string
	bodyParam title             string
	bodyParam body              string
	bodyParam due_date          datetime Y-m-d H:i:s
	bodyParam attachment_file   file

`Edit todo item`

	POST : /api/v1/todo/{id}	
	param id
	headerParam api_token       string
	bodyParam title             string
	bodyParam body              string
	bodyParam due_date          datetime Y-m-d H:i:s
	bodyParam attachment_file   file

`Delete todo item`

	DELETE : /api/v1/todo/{id}
	headerParam api_token       string
	param id

`Update only status of todo item`

	PUT : /api/v1/todo-update-status/{id}	
	param id
	headerParam api_token       string
	body json {"is_complete":"1"}

`Add reminder to todo item`

	POST /api/v1/todo-add-reminder	
	headerParam api_token       string
	bodyParam todo_id           integer
	bodyParam reminder_date     datetime Y-m-d H:i:s