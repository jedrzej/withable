# Withable trait for Laravel's Eloquent models

This package adds possibility to dynamically eager load Eloquent models relations in Laravel 4/5 using request parameters.

## Composer install

Add the following line to `composer.json` file in your project:

    "jedrzej/withable": "0.0.1"
	
or run the following in the commandline in your project's root folder:	

    composer require "jedrzej/withable" "0.0.1"

## Setting up withable models

In order to make an Eloquent model withable, add the trait to the model and define a list of relations that can be eagerly loaded using request paramaters.
You can either define a `$withable` property or implement a `getWithableRelations` method if you want to execute some logic to define
list loadable relations.

    use Jedrzej\Withable\WithableTrait;
    
    class Post extends Eloquent
    {
        use WithableTrait;

        // either a property holding a list of loadable relations...
        public $withable = ['owner', 'forum'];

        // ...or a method that returns a list of loadable relations
        public function getWithableRelations()
        {
            return ['owner', 'forum'];
        }
    }

In order to make all relations loadable put an asterisk `*` in the list:

    public $withable = ['*'];

## Loading relations

`WithableTrait` adds a `withRelations()` scope to the model - you can pass it a list of relations to load as you would pass to Eloquent's `with()` method:

    // return all posts with the user who created them and forum where they were posted
    Post::withRelations(['owner', 'forum'])->get();
    // return all posts with the user who created them
    Post::withRelations('owner')->get();

 or it will use `Input::all()` as default. In the URL you can pass a list of relations to load or a single relation in the `with` parameter:
    
    // return all posts with the user who created them and forum where they were posted by appending to the URL
    ?with[]=owner&with[]=forum
    // return all posts with the user who created them
     ?with=owner
     //and then calling
    Post::withRelations()->get();

## Additional configuration

If you are using `with` request parameter for other purpose or you need to execute additional logic to define a list of
relations to load (e.g. permission checking), you can implement `getWithRelationsList()` method in your model and make that return list of relations:

    public function getWithRelationsList() {
      return Input::get('relations');
    }