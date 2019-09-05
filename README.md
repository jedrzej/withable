# Withable trait for Laravel's Eloquent models

This package adds possibility to dynamically eager load Eloquent models relations in Laravel 4/5/6 using request parameters.

You could also find those packages useful:

- [Searchable](https://github.com/jedrzej/searchable) - Allows filtering your models using request parameters
- [Sortable](https://github.com/jedrzej/sortable) - Allows sorting your models using request parameters
- [Pimpable](https://github.com/jedrzej/pimpable) - A meta package that combines Sortable, Searchable and Withable behaviours

## Composer install

Add the following line to `composer.json` file in your project:

    "jedrzej/withable": "0.0.6"
	
or run the following in the commandline in your project's root folder:	

    composer require "jedrzej/withable" "0.0.6"

## Setting up withable models

In order to make an Eloquent model withable, add the trait to the model and define a list of relations that can be eagerly loaded using request paramaters.
You can either define a `$withable` property or implement a `getWithableRelations` method if you want to execute some logic to define
list loadable relations.

```php
use Jedrzej\Withable\WithableTrait;

class Post extends Eloquent
{
	use WithableTrait;
	
	// either a property holding a list of loadable relations...
	protected $withable = ['owner', 'forum'];
	
	// ...or a method that returns a list of loadable relations
	protected function getWithableRelations()
	{
		return ['owner', 'forum'];
	}
}
```

In order to make all relations loadable put an asterisk `*` in the list:

```php
protected $withable = ['*'];
```

## Loading relations

`WithableTrait` adds a `withRelations()` scope to the model - you can pass it a list of relations to load as you would pass to Eloquent's `with()` method:

```php
// return all posts with the user who created them and forum where they were posted
Post::withRelations(['owner', 'forum'])->get();
// return all posts with the user who created them
Post::withRelations('owner')->get();
```

 or it will use `Request::all()` as default. In the URL you can pass a list of relations to load or a single relation in the `with` parameter:

```php    
// return all posts with the user who created them and forum where they were posted by appending to the URL
?with[]=owner&with[]=forum
// return all posts with the user who created them
?with=owner
//and then calling
Post::withRelations()->get();
```

## Loading scoped relations
If you have defined local scopes in a related model, you can load this relation with the scope applied. E.g. in order to eagerly
load posts with their approved comments:

```php
// define local scope in Comment model
public function scopeApproved($query) {
  $query->whereIsApproved(true);
}

// append the following to the URL in order to load "comments" relation with "approved" local scope applied
?with=comments:approved

// load the posts in your controller
Post::withRelations()->get();
```


## Additional configuration

If you are using `with` request parameter for other purpose, you can change the name of the parameter that will be
 interpreted as a list of relations to load by setting a `$withParameterName` property in your model, e.g.:

```php
protected $withParameterName = 'relations';
```

If you need to execute additional logic to define a list of relations to load (e.g. permission checking),
you can implement `getWithRelationsList()` method in your model and make that return list of relations:

```php
public function getWithRelationsList() {
  return Request::get('relations');
}
```
